<?php

namespace app\job\event;

use app\job\model\order\Order as OrderModel;
use think\facade\Cache;
use app\common\model\settings\Setting as SettingModel;
use app\job\service\OrderService;
use app\common\enum\order\OrderTypeEnum;
use app\common\service\order\OrderCompleteService;
use app\common\library\helper;
/**
 * 訂單事件管理
 */
class Order
{
    // 模型
    private $model;

    // 應用id
    private $appId;

    /**
     * 執行函式
     */
    public function handle($app_id)
    {
        try {
            // 繫結訂單模型
            $this->appId = $app_id;
            $this->model = new OrderModel();
            // 普通訂單行為管理
            $this->master();
            // 未支付訂單自動關閉，拼團,預售除外
            $this->close();
        } catch (\Throwable $e) {
            log_write('ORDER TASK : ' . $app_id . '__ ' . $e->getMessage(), 'task');
        }
        return true;
    }

    /**
     * 普通訂單行為管理
     */
    private function master()
    {
        $key = "task_space__order__{$this->appId}";
        if (Cache::has($key)) return true;
        // 獲取商城交易設定
        $config = SettingModel::getItem('trade', $this->appId);
        $this->model->transaction(function () use ($config) {
            // 已發貨訂單自動確認收貨
            $this->receive($config['order']['receive_days']);
            // 已完成訂單結算
            $this->settled($config['order']['refund_days']);
        });
        Cache::set($key, time(), 60);
        return true;
    }

    /**
     * 未支付訂單自動關閉
     */
    private function close()
    {
        $service = new OrderService();
        // 執行自動關閉
        $service->close();
        // 記錄日誌
        $this->dologs('close', [
            'orderIds' => json_encode($service->getCloseOrderIds()),
        ]);
        return true;
    }
    /**
     * 已發貨訂單自動確認收貨
     */
    private function receive($receiveDays)
    {
        // 截止時間
        if ($receiveDays < 1) return false;
        $deadlineTime = time() - ((int)$receiveDays * 86400);
        // 條件
        // 訂單id集
        $orderId_arr = $this->model->where('pay_status', '=', 20)
            ->where('delivery_status', '=', 20)
            ->where('receipt_status', '=', 10)
            ->where('app_id', '=', $this->appId)
            ->where('delivery_time', '<=', $deadlineTime)
            ->column('order_id');
        $orderIds = helper::getArrayColumnIds($orderId_arr);
        if (!empty($orderIds)) {
            // 更新訂單收貨狀態
            $this->model->onBatchUpdate($orderIds, [
                'receipt_status' => 20,
                'receipt_time' => time(),
                'order_status' => 30
            ]);
            // 批次處理已完成的訂單
            $this->onReceiveCompleted($orderIds);
        }
        // 記錄日誌
        $this->dologs('receive', [
            'receive_days' => (int)$receiveDays,
            'deadline_time' => $deadlineTime,
            'orderIds' => json_encode($orderIds),
        ]);
        return true;
    }

    /**
     * 已完成訂單結算
     */
    private function settled($refundDays)
    {
        // 獲取已完成的訂單（未累積使用者實際消費金額）
        // 條件1：訂單狀態：已完成
        // 條件2：超出售後期限
        // 條件3：is_settled 為 0
        // 截止時間
        $deadlineTime = time() - ((int)$refundDays * 86400);
        // 查詢訂單列表
        $orderList = $this->model->getSettledList($deadlineTime, [
            'product' => ['refund'],  // 用於計算售後退款金額
        ], $this->appId);
        // 訂單id集
        $orderIds = helper::getArrayColumn($orderList, 'order_id');
        // 訂單結算服務
        $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
        !empty($orderIds) && $OrderCompleteService->settled($orderList);
        // 記錄日誌
        $this->dologs('settled', [
            'refund_days' => (int)$refundDays,
            'deadline_time' => $deadlineTime,
            'orderIds' => json_encode($orderIds),
        ]);
    }

    /**
     * 批次處理已完成的訂單
     */
    private function onReceiveCompleted($orderIds)
    {
        // 獲取已完成的訂單列表
        $list = $this->model->getReceiveList($orderIds, [
            'product' => ['refund'],  // 用於發放分銷佣金
            'user', 'address', 'product', 'express',  // 用於同步微信好物圈
        ]);
        if ($list->isEmpty()) return false;
        // 執行訂單完成後的操作
        $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
        $OrderCompleteService->complete($list, $this->appId);
        return true;
    }

    /**
     * 記錄日誌
     */
    private function dologs($method, $params = [])
    {
        $value = 'behavior Order --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value, 'task');
    }

}
