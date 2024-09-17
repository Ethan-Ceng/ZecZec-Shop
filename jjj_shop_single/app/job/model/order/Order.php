<?php

namespace app\job\model\order;

use app\common\model\order\Order as OrderModel;
use app\common\model\order\OrderAdvance as OrderAdvanceModel;
use app\common\service\product\factory\ProductFactory;
use app\common\library\helper;

/**
 * 訂單模型
 */
class Order extends OrderModel
{
    /**
     * 獲取訂單列表
     */
    public function getCloseList($with = [])
    {
        return $this->with($with)
            ->where('pay_status', '=', 10)
            ->where('order_status', '=', 10)
            ->where('pay_end_time', '<=', time())
            ->where('pay_end_time', '>', 0)
            ->where('is_delete', '=', 0)
            ->where('order_source', '<>', 80)
            ->select();
    }


    /**
     * 獲取訂單列表
     */
    public function getReceiveList($orderIds, $with = [])
    {
        return $this->with($with)
            ->where('order_id', 'in', $orderIds)
            ->select();
    }

    /**
     * 獲取訂單列表
     */
    public function getSettledList($deadlineTime, $with, $app_id)
    {
        return $this->with($with)
            ->where('order_status', '=', 30)
            ->where('receipt_time', '<=', $deadlineTime)
            ->where('is_settled', '=', 0)
            ->where('app_id', '=', $app_id)
            ->select();
    }

    /**
     * 獲取訂單列表
     */
    public function getAdvanceCloseList($with = [])
    {
        return $this->with($with)
            ->where('pay_status', '=', 10)
            ->where('order_status', '=', 10)
            ->where('pay_end_time', '<=', time())
            ->where('pay_end_time', '>', 0)
            ->where('is_delete', '=', 0)
            ->where('order_source', '=', 80)
            ->select();
    }

    /**
     * 未支付訂單自動關閉
     */
    public function close()
    {
        // 查詢截止時間未支付的訂單
        $list = $this->getAdvanceCloseList();
        $closeOrderIds = helper::getArrayColumn($list, 'order_id');
        // 取消訂單事件
        $this->startTrans();
        try {
            if (!empty($closeOrderIds)) {
                $advanceList = OrderAdvanceModel::where('order_id', 'in', $closeOrderIds)
                    ->where('order_status', '=', 10)
                    ->select();
                $closeOrderAdvanceIds = helper::getArrayColumn($advanceList, 'order_advance_id');
                foreach ($list as &$order) {
                    // 回退商品庫存
                    ProductFactory::getFactory($order['order_source'])->backProductStock($order['product'], true);
                }
                // 批次更新訂單狀態為已取消
                (new OrderModel)->onBatchUpdate($closeOrderIds, ['order_status' => 20]);
                (new OrderAdvanceModel)->where('order_advance_id', 'in', $closeOrderAdvanceIds)->update(['order_status' => 20]);
            }
            $this->commit();
            return $closeOrderIds;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
        }
    }

}
