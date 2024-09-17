<?php

namespace app\api\model\plus\card;

use app\common\enum\order\OrderTypeEnum;
use app\common\exception\BaseException;
use app\common\model\plus\card\Order as OrderModel;
use app\common\model\plus\card\Code as CodeModel;
use app\common\model\plus\card\Card as CardModel;
use app\common\service\message\MessageService;
/**
 * 滿減模型
 */
class Order extends OrderModel
{
    public function confirm($user, $product, $params){
        $this->startTrans();
        try {
            // 修改訂單表
            $this->save([
                'name' => $params['name'],
                'mobile' => $params['mobile'],
                'province_id' => $params['province_id'],
                'city_id' => $params['city_id'],
                'region_id' => $params['region_id'],
                'detail' => $params['detail'],
                'order_status' => 1,
                'product_attr' => $this['card']['product_attr'],
                'product_name' => $product['product_name'],
                'product_image' => $product['image'][0]['file_path'],
                'product_price' => $product['product_price'],
            ]);
            // 提貨碼標記為已使用
            (new CodeModel())->where('code_id', '=', $this['code_id'])->update([
                'active_status' => 1,
                'code_status' => 1
            ]);
            // 卡券資料更新
            (new CardModel())->where('card_id', '=', $this['card_id'])->inc('sell_num')->update();
            $wait_num = (new CodeModel())->where('card_id', '=', $this['card_id'])
                ->where('code_status', '=', 0)
                ->where('is_delete', '=', 0)
                ->count();
            (new CardModel())->where('card_id', '=', $this['card_id'])->update([
                'wait_num' => $wait_num
            ]);
            $order = self::detail($this['order_id'], ['code']);
            // 傳送模板訊息
            (new MessageService())->codeOrder($order, $user);
            // 商家簡訊
            (new MessageService())->newOrder($order, []);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 使用者中心訂單列表
     */
    public function getList($user_id, $type, $params)
    {
        // 篩選條件
        $filter = [];
        // 訂單資料型別
        switch ($type) {
            case 'all':
                break;
            case 'wait';
                $filter['delivery_status'] = 0;
                break;
            case 'send';
                $filter['delivery_status'] = 1;
                break;
        }
        return $this->where('user_id', '=', $user_id)
            ->where($filter)
            ->where('order_status', '=', 1)
            ->where('is_delete', '=', 0)
            ->order(['create_time' => 'desc'])
            ->paginate($params);
    }

    /**
     * 訂單詳情
     */
    public static function getUserOrderDetail($order_id, $user_id)
    {
        $model = new static();
        $order = $model->with(['express'])->where(['order_id' => $order_id, 'user_id' => $user_id])->find();
        if (empty($order)) {
            throw new BaseException(['msg' => '訂單不存在']);
        }
        return $order;
    }
}