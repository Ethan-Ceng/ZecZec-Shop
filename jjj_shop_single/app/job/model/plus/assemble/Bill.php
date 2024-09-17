<?php

namespace app\job\model\plus\assemble;

use app\common\enum\order\OrderSourceEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\library\helper;
use app\common\model\plus\assemble\Bill as BillModel;
use app\common\model\plus\assemble\BillUser as BillUserModel;
use app\common\model\order\Order as OrderModel;
use app\common\service\order\OrderCompleteService;
use app\common\service\order\OrderPrinterService;
use app\common\service\order\OrderRefundService;

/**
 * 參與記錄模型
 */
class Bill extends BillModel
{
    /**
     * 獲取待關閉訂單
     */
    public function getCloseIds($fail_type = 0)
    {
        return $this->alias('bill')
            ->join('assemble_activity activity', 'bill.assemble_activity_id = activity.assemble_activity_id', 'left')
            ->where('bill.status', '=', 10)
            ->where('activity.fail_type', '=', $fail_type)
            ->whereTime('bill.end_time', '<=', time())
            ->select();
    }

    /**
     * 關閉訂單
     * @param $billIds
     */
    public function close($billIds)
    {
        // 更新記錄
        $this->startTrans();
        try {
            //修改拼團狀態
            $this->where('assemble_bill_id', 'in', $billIds)->save(['status' => 30]);
            //修改訂單狀態，並退款
            $bill_user_model = new BillUserModel();
            $orderList = $bill_user_model->field(['order_id'])
                ->where('assemble_bill_id', 'in', $billIds)
                ->select();
            $orderIds = helper::getArrayColumn($orderList, 'order_id');
            //修改訂單狀態，拼團狀態
            (new OrderModel)->where('order_id', 'in', $orderIds)->save([
                'order_status' => 20,
                'assemble_status' => 30
            ]);
            $this->commit();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
        }
        // 退款
        $this->orderRefund();
    }

    /**
     * 拼團失敗訂單退款
     */
    public function orderRefund()
    {
        //查詢待退款的拼團訂單，每次取100條
        $orderList = (new OrderModel)->where('order_source', '=', OrderSourceEnum::ASSEMBLE)
            ->where('order_status', '=', 20)
            ->where('is_refund', '=', 0)
            ->where('pay_status', '=', 20)
            ->where('pay_source', '<>', '')
            ->limit(100)
            ->select();
        if (count($orderList) > 0) {
            foreach ($orderList as $order) {
                try {
                    // 執行退款操作
                    if ((new OrderRefundService)->execute($order)) {
                        // 更新訂單狀態
                        $order->save([
                            'is_refund' => 1
                        ]);
                    }
                } catch (\Exception $e) {
                    $this->error = '訂單ID：' . $order['order_id'] . ' 退款失敗，錯誤資訊：' . $e->getMessage();
                }
            }
        }
        return true;
    }

    /**
     * 拼團成功訂單
     * @param $billIds
     */
    public function success($billIds)
    {
        // 更新記錄
        $this->startTrans();
        try {
            //修改拼團狀態
            $this->where('assemble_bill_id', 'in', $billIds)->save(['status' => 20]);
            $order_list = (new BillUserModel)
                ->field(['order_id'])
                ->where('assemble_bill_id', 'in', $billIds)
                ->select();
            $orderIds = helper::getArrayColumn($order_list, 'order_id');
            //更新主訂單表拼團狀態
            if (!empty($orderIds)) {
                (new OrderModel)->where('order_id', 'in', $orderIds)
                    ->save([
                        'assemble_status' => 20
                    ]);
                $orderList = (new OrderModel)->where('order_id', 'in', $orderIds)
                    ->with(['product' => ['image', 'refund'], 'address', 'express', 'extractStore', 'advance'])
                    ->select();
                foreach ($orderList as $order) {
                    if ($order['delivery_type']['value'] == DeliveryTypeEnum::NO_EXPRESS && $order['virtual_auto'] == 1) {
                        $order->save([
                            'delivery_status' => 20,
                            'delivery_time' => time(),
                            'receipt_status' => 20,
                            'receipt_time' => time(),
                            'order_status' => 30,
                            'virtual_content' => $order['product'][0]['virtual_content'],
                        ]);
                        $detail = OrderModel::detail($order['order_id']);
                        // 執行訂單完成後的操作
                        $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
                        $OrderCompleteService->complete([$detail], $detail['app_id']);
                        $detail->sendWxExpress('', '');
                    }
                }
                // 拼團訂單列印
                foreach ($orderIds as $orderId) {
                    (new OrderPrinterService)->printTicket(OrderModel::detail($orderId));
                }
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
        }
    }
}