<?php

namespace app\job\model\order;

use app\common\model\order\OrderAdvance as OrderAdvanceModel;
use app\common\service\product\factory\ProductFactory;
use app\common\model\order\Order as OrderModel;
use app\common\library\helper;
use app\common\service\order\OrderRefundService;

/**
 * 預售定金訂單模型
 */
class OrderAdvance extends OrderAdvanceModel
{
    /**
     * 獲取訂單列表
     */
    public function getCloseList($with = [])
    {
        return $this->with($with)
            ->where('pay_status', '=', 10)
            ->where('order_status', '=', 10)
            ->where('pay_end_time', '<', time())
            ->where('pay_end_time', '>', 0)
            ->select();
    }

    /**
     * 獲取退定金訂單列表
     */
    public function getReturnList()
    {
        return $this->where('pay_status', '=', 20)
            ->where('order_status', '=', 20)
            ->where('is_refund', '=', 0)
            ->where('money_return', '=', 1)
            ->select();
    }

    /**
     * 未支付訂單自動關閉
     */
    public function close()
    {
        // 查詢截止時間未支付的訂單
        $list = $this->getCloseList(['orderM.product', 'advance']);
        $closeOrderAdvanceIds = helper::getArrayColumn($list, 'order_advance_id');
        $this->startTrans();
        try {
            // 取消訂單事件
            if (!empty($closeOrderAdvanceIds)) {
                $closeOrderIds = helper::getArrayColumn($list, 'order_id');
                foreach ($list as &$order) {
                    // 回退商品庫存
                    ProductFactory::getFactory($order['orderM']['order_source'])->backProductStock($order['orderM']['product'], false);
                }
                // 批次更新訂單狀態為已取消
                (new OrderModel)->onBatchUpdate($closeOrderIds, ['order_status' => 20]);
                $this->where('order_advance_id', 'in', $closeOrderAdvanceIds)->update(['order_status' => 20]);
            }
            $this->commit();
            return $closeOrderAdvanceIds;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
        }

    }

    /**
     * 已取消訂單退定金
     */
    public function return()
    {
        // 查詢截止時間未支付的訂單
        $list = $this->getReturnList();
        $closeOrderAdvanceIds = helper::getArrayColumn($list, 'order_advance_id');
        // 取消訂單事件
        if (!empty($closeOrderAdvanceIds)) {
            foreach ($list as &$item) {
                try {
                    if ((new OrderRefundService)->execute($item)) {
                        // 更新訂單狀態
                        $item->save([
                            'is_refund' => 1,
                            'refund_money' => $item['pay_price'],
                        ]);
                    }
                } catch (\Exception $e) {
                    $this->error = '訂單ID：' . $item['order_advance_id'] . ' 退款失敗，錯誤資訊：' . $e->getMessage();
                }
            }
        }
        return $closeOrderAdvanceIds;
    }

}
