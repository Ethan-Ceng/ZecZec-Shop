<?php

namespace app\job\event;

use app\job\model\order\Order as OrderModel;
use app\job\model\order\OrderAdvance as OrderAdvanceModel;
use think\facade\Cache;

/**
 * 預售訂單事件管理
 */
class AdvanceOrder
{
    /**
     * 執行函式
     */
    public function handle()
    {
        try {
            $cacheKey = "task_space_advance_order_task";
            if (!Cache::has($cacheKey)) {
                // 定金訂單行為管理
                $this->front();
                // 未支付尾款訂單自動關閉
                $this->order();
                // 未支付尾款訂單退定金
                $this->return();
                Cache::set($cacheKey, time(), 10);
            }
        } catch (\Throwable $e) {
            log_write('ORDER_ADVANCE TASK : ' . '__ ' . $e->getMessage(), 'task');
        }
        return true;
    }

    /**
     * 定金訂單行為管理
     */
    private function front()
    {
        $OrderAdvanceModel = new OrderAdvanceModel();
        // 執行自動關閉
        $orderAdvanceIds = $OrderAdvanceModel->close();
        // 記錄日誌
        $this->dologs('frontClose', [
            'orderAdvanceIds' => json_encode($orderAdvanceIds),
        ]);
        return true;
    }

    /**
     * 未支付尾款訂單自動關閉
     */
    private function order()
    {
        $OrderModel = new OrderModel();
        // 執行自動關閉
        $closeOrderIds = $OrderModel->close();
        // 記錄日誌
        $this->dologs('advanceClose', [
            'orderIds' => json_encode($closeOrderIds),
        ]);
        return true;
    }

    /**
     * 未支付尾款訂單自動退定金
     */
    private function return()
    {
        $OrderAdvanceModel = new OrderAdvanceModel();
        // 執行自動關閉
        $closeOrderAdvanceIds = $OrderAdvanceModel->return();
        // 記錄日誌
        $this->dologs('advanceReturn', [
            'orderIds' => json_encode($closeOrderAdvanceIds),
        ]);
        return true;
    }

    /**
     * 記錄日誌
     */
    private function dologs($method, $params = [])
    {
        $value = 'behavior OrderAdvance --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value, 'task');
    }

}
