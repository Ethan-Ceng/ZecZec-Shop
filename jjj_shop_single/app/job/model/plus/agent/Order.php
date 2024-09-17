<?php

namespace app\job\model\plus\agent;

use app\common\model\plus\agent\Order as OrderModel;
use app\common\service\order\OrderService;

/**
 * 分銷商訂單模型
 */
class Order extends OrderModel
{
    /**
     * 獲取未結算的分銷訂單
     */
    public function getUnSettledList()
    {
        $list = $this->where('is_invalid', '=', 0)
            ->where('is_settled', '=', 0)
            ->where('settle_end_time', '<>', 0) //已完成
            ->where('settle_end_time', '<', time()) //過了結算時間
            ->select();
        if ($list->isEmpty()) {
            return $list;
        }
        // 整理訂單資訊
        $with = ['product' => ['refund']];
        return OrderService::getOrderList($list, 'order_master', $with);
    }

    /**
     * 標記訂單已失效(批次)
     */
    public function setInvalid($ids)
    {
        return $this->where('id', 'in', $ids)
            ->save(['is_invalid' => 1]);
    }

}