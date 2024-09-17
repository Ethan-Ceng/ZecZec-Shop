<?php

namespace app\common\service\order;

use app\common\library\helper;
use app\common\model\order\OrderProduct;
use app\common\model\product\Product;
use app\common\model\user\User as UserModel;
use app\common\model\settings\Setting as SettingModel;
use app\common\model\plus\agent\Order as AgentOrderModel;
use app\common\model\user\PointsLog as PointsLogModel;
use app\common\enum\order\OrderTypeEnum;

/**
 * 已完成訂單結算服務類
 */
class OrderCompleteService
{
    // 訂單型別
    private $orderType;

    /**
     * 訂單模型類
     * @var array
     */
    private $orderModelClass = [
        OrderTypeEnum::MASTER => 'app\common\model\order\Order',
    ];

    // 模型
    private $model;

    /* @var UserModel $model */
    private $UserModel;

    /**
     * 構造方法
     */
    public function __construct($orderType = OrderTypeEnum::MASTER)
    {
        $this->orderType = $orderType;
        $this->model = $this->getOrderModel();
        $this->UserModel = new UserModel;
    }

    /**
     * 初始化訂單模型類
     */
    private function getOrderModel()
    {
        $class = $this->orderModelClass[$this->orderType];
        return new $class;
    }

    /**
     * 執行訂單完成後的操作
     */
    public function complete($orderList, $appId)
    {
        // 已完成訂單結算
        // 條件：後臺訂單流程設定 - 已完成訂單設定0天不允許申請售後
        if (SettingModel::getItem('trade', $appId)['order']['refund_days'] == 0) {
            $this->settled($orderList);
        }
        // 發放分銷商佣金
        foreach ($orderList as $order) {
            AgentOrderModel::grantMoney($order, $this->orderType);
        }

        //訂單完成，計算商品總金額
        $this->setIncProductTotalMoney($orderList);

        return true;
    }

    /**
     * 執行訂單結算
     */
    public function settled($orderList)
    {
        // 訂單id集
        $orderIds = helper::getArrayColumn($orderList, 'order_id');
        // 累積使用者實際消費金額
        $this->setIncUserExpend($orderList);
        // 處理訂單贈送的積分
        $this->setGiftPointsBonus($orderList);
        // 將訂單設定為已結算
        $this->model->onBatchUpdate($orderIds, ['is_settled' => 1]);
        return true;
    }

    /**
     * 處理訂單贈送的積分
     */
    private function setGiftPointsBonus($orderList)
    {
        // 計算使用者所得積分
        $userData = [];
        $logData = [];
        foreach ($orderList as $order) {
            // 計算使用者所得積分
            $pointsBonus = $order['points_bonus'];
            if ($pointsBonus <= 0) continue;
            // 減去訂單退款的積分
            foreach ($order['product'] as $product) {
                if (
                    !empty($product['refund'])
                    && $product['refund']['type']['value'] == 10      // 售後型別：退貨退款
                    && $product['refund']['is_agree']['value'] == 10  // 商家稽核：已同意
                ) {
                    $pointsBonus -= $product['points_bonus'];
                }
            }
            // 計算使用者所得積分
            !isset($userData[$order['user_id']]) && $userData[$order['user_id']] = 0;
            $userData[$order['user_id']] += $pointsBonus;
            // 整理使用者積分變動明細
            $logData[] = [
                'user_id' => $order['user_id'],
                'value' => $pointsBonus,
                'describe' => "訂單贈送：{$order['order_no']}",
                'app_id' => $order['app_id'],
            ];
        }
        if (!empty($userData)) {
            // 累積到會員表記錄
            $this->UserModel->onBatchIncPoints($userData);
            // 批次新增積分明細記錄
            (new PointsLogModel)->onBatchAdd($logData);
        }
        return true;
    }

    /**
     * 累積使用者實際消費金額
     */
    private function setIncUserExpend($orderList)
    {
        // 計算並累積實際消費金額(需減去售後退款的金額)
        $userData = [];
        foreach ($orderList as $order) {
            // 訂單實際支付金額
            $expendMoney = $order['pay_price'];
            // 減去訂單退款的金額
            foreach ($order['product'] as $product) {
                if (
                    !empty($product['refund'])
                    && $product['refund']['type']['value'] == 10      // 售後型別：退貨退款
                    && $product['refund']['is_agree']['value'] == 10  // 商家稽核：已同意
                ) {
                    $expendMoney -= $product['refund']['refund_money'];
                }
            }
            !isset($userData[$order['user_id']]) && $userData[$order['user_id']] = 0.00;
            $expendMoney > 0 && $userData[$order['user_id']] += $expendMoney;
        }
        // 累積到會員表記錄
        $this->UserModel->onBatchIncExpendMoney($userData);
        return true;
    }

    /**
     * 設定計劃總金額
     * @param $orderList
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setIncProductTotalMoney($orderList)
    {
        $orderIds = helper::getArrayColumn($orderList, 'order_id');
        $orderProductList = OrderProduct::where('order_id', $orderIds)->select();
        foreach ($orderProductList as $value) {
            $product = Product::find($value['product_id']);
            if ($product['start_time'] < time() && $product['end_time'] >= time()) {
                $product->inc('total_money', $product['product_price'])->update();
            }
        }
    }

}