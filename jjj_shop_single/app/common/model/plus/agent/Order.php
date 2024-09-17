<?php

namespace app\common\model\plus\agent;

use app\common\model\BaseModel;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\plus\agent\User as AgentUserModel;
use app\common\model\product\Product as ProductModel;
use app\common\model\plus\agent\Product as AgentProductModel;

/**
 * 分銷商訂單模型
 */
class Order extends BaseModel
{
    protected $name = 'agent_order';
    protected $pk = 'id';

    /**
     * 訂單所屬使用者
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('app\common\model\user\User');
    }

    /**
     * 一級分銷商使用者
     * @return \think\model\relation\BelongsTo
     */
    public function agentFirst()
    {
        return $this->belongsTo('app\common\model\user\User', 'first_user_id');
    }

    /**
     * 二級分銷商使用者
     * @return \think\model\relation\BelongsTo
     */
    public function agentSecond()
    {
        return $this->belongsTo('app\common\model\user\User', 'second_user_id');
    }

    /**
     * 三級分銷商使用者
     * @return \think\model\relation\BelongsTo
     */
    public function agentThird()
    {
        return $this->belongsTo('app\common\model\user\User', 'third_user_id');
    }

    /**
     * 訂單型別
     * @param $value
     * @return array
     */
    public function getOrderTypeAttr($value)
    {
        $types = OrderTypeEnum::getTypeName();
        return ['text' => $types[$value], 'value' => $value];
    }

    /**
     * 訂單詳情
     * @param $orderId
     * @param $orderType
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getDetailByOrderId($orderId, $orderType)
    {
        return (new static())->where('order_id', '=', $orderId)
            ->where('order_type', '=', $orderType)
            ->find();
    }

    /**
     * 發放分銷訂單佣金
     * @param $order
     * @param int $orderType
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function grantMoney($order, $orderType = OrderTypeEnum::MASTER)
    {
        // 訂單是否已完成
        if ($order['order_status']['value'] != 30) {
            return false;
        }
        // 分銷訂單詳情
        $model = self::getDetailByOrderId($order['order_id'], $orderType);
        if (!$model || $model['is_settled'] == 1) {
            return false;
        }
        // 佣金結算天數
        $settleDays = Setting::getItem('settlement', $order['app_id'])['settle_days'];
        // 寫入結算時間
        $deadlineTime = $model['settle_end_time'];
        if ($deadlineTime == 0) {
            $deadlineTime = $order['receipt_time'] + $settleDays * 86400;
            $model->save([
                'settle_end_time' => $deadlineTime
            ]);
        }
        if ($deadlineTime > time()) {
            return false;
        }

        // 重新計算分銷佣金
        $capital = $model->getCapitalByOrder($order);
        // 發放一級分銷商佣金
        $model['first_user_id'] > 0 && User::grantMoney($model['first_user_id'], $capital['first_money']);
        // 發放二級分銷商佣金
        $model['second_user_id'] > 0 && User::grantMoney($model['second_user_id'], $capital['second_money']);
        // 發放三級分銷商佣金
        $model['third_user_id'] > 0 && User::grantMoney($model['third_user_id'], $capital['third_money']);
        // 更新分銷訂單記錄
        $model->save([
            'order_price' => $capital['orderPrice'],
            'first_money' => $model['first_user_id'] > 0 ? $capital['first_money'] : 0,
            'second_money' => $model['second_user_id'] > 0 ? $capital['second_money'] : 0,
            'third_money' => $model['third_user_id'] > 0 ? $capital['third_money'] : 0,
            'is_settled' => 1,
            'settle_time' => time()
        ]);
        event('AgentUserGrade', $model['first_user_id']);
        event('AgentUserGrade', $model['second_user_id']);
        event('AgentUserGrade', $model['third_user_id']);
        return true;
    }

    /**
     * 計算訂單分銷佣金
     * @param $order
     * @return array
     */
    protected function getCapitalByOrder($order, $source = '')
    {
        // 分銷佣金設定
        $setting = Setting::getItem('commission', $order['app_id']);
        // 分銷層級
        $level = Setting::getItem('basic', $order['app_id'])['level'];
        // 分銷訂單佣金資料
        $capital = [
            // 訂單總金額(不含運費)
            'orderPrice' => bcsub($order['pay_price'], $order['express_price'], 2),
            // 一級分銷佣金
            'first_money' => 0.00,
            // 二級分銷佣金
            'second_money' => 0.00,
            // 三級分銷佣金
            'third_money' => 0.00,
            // 是否記錄
            'is_record' => true
        ];
        $total_count = count($order['product']);
        // 計算分銷佣金
        foreach ($order['product'] as $product) {
            // 判斷商品是否開放分銷
            if (ProductModel::detail($product['product_id'])['is_agent'] == 0 && $source == 'create') {
                $total_count--;
                continue;
            }
            // 判斷商品存在售後退款則不計算佣金
            if ($this->checkProductRefund($product)) {
                continue;
            }

            // 商品實付款金額
            $productPrice = min($capital['orderPrice'], $product['total_pay_price']);
            // 計算商品實際佣金
            $productCapital = $this->calculateProductCapital($setting, $product, $productPrice);
            // 累積分銷佣金
            $level >= 1 && $capital['first_money'] += $productCapital['first_money'];
            $level >= 2 && $capital['second_money'] += $productCapital['second_money'];
            $level == 3 && $capital['third_money'] += $productCapital['third_money'];
        }
        if ($total_count == 0) {
            $capital['is_record'] = false;
        }
        return $capital;
    }

    /**
     * 計算商品實際佣金
     * @param $setting
     * @param $product
     * @param $productPrice
     * @return float[]|int[]
     */
    private function calculateProductCapital($setting, $product, $productPrice)
    {
        $first_user = AgentUserModel::detail($this['first_user_id'],['grade']);
        $second_user = AgentUserModel::detail($this['second_user_id'],['grade']);
        $third_user = AgentUserModel::detail($this['third_user_id'],['grade']);
        $add_first_money = 0;
        if($first_user && $first_user['grade']){
            $add_first_money = $productPrice * ($first_user['grade']['first_percent'] * 0.01);
        }
        $add_second_money = 0;
        if($second_user && $second_user['grade']) {
            $add_second_money = $productPrice * ($second_user['grade']['second_percent'] * 0.01);
        }
        $add_third_money = 0;
        if($third_user && $third_user['grade']) {
            $add_third_money = $productPrice * ($third_user['grade']['third_percent'] * 0.01);
        }
        $agent_product = AgentProductModel::detail($product['product_id']);
        // 全域性分銷
        if (!isset($agent_product) || (isset($agent_product['is_ind_agent']) && $agent_product['is_ind_agent'] == 0)) {
            // 全域性分銷比例
            return [
                'first_money' => $productPrice * ($setting['first_money'] * 0.01) + $add_first_money,
                'second_money' => $productPrice * ($setting['second_money'] * 0.01) + $add_second_money,
                'third_money' => $productPrice * ($setting['third_money'] * 0.01) + $add_third_money
            ];
        }
        // 商品單獨分銷
        if (isset($agent_product['agent_money_type']) && $agent_product['agent_money_type'] == 10) {
            // 分銷佣金型別：百分比
            return [
                'first_money' => $productPrice * ($agent_product['first_money'] * 0.01) + $add_first_money,
                'second_money' => $productPrice * ($agent_product['second_money'] * 0.01) + $add_second_money,
                'third_money' => $productPrice * ($agent_product['third_money'] * 0.01) + $add_third_money
            ];
        } else {
            return [
                'first_money' => isset($agent_product['first_money']) ? $product['total_num'] * $agent_product['first_money'] : 0 + $add_first_money,
                'second_money' => isset($agent_product['second_money']) ? $product['total_num'] * $agent_product['second_money'] : 0 + $add_second_money,
                'third_money' => isset($agent_product['third_money']) ? $product['total_num'] * $agent_product['third_money'] : 0 + $add_third_money
            ];
        }
    }

    /**
     * 驗證商品是否存在售後
     * @param $product
     * @return bool
     */
    private function checkProductRefund($product)
    {
        return !empty($product['refund'])
            && $product['refund']['type']['value'] != 20
            && $product['refund']['is_agree']['value'] != 20;
    }

}
