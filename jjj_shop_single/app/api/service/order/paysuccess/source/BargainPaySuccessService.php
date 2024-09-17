<?php

namespace app\api\service\order\paysuccess\source;

use app\common\enum\order\OrderTypeEnum;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\service\order\OrderCompleteService;
use app\common\service\order\OrderPrinterService;

/**
 * 砍價訂單支付成功後的回撥
 */
class BargainPaySuccessService
{
    /**
     * 回撥方法
     */
    public function onPaySuccess($order)
    {
        // 小票列印
        (new OrderPrinterService)->printTicket($order);
        // 如果是虛擬商品，則標記為已完成，無需發貨
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::NO_EXPRESS && $order['virtual_auto'] == 1) {
            $order->save([
                'delivery_status' => 20,
                'delivery_time' => time(),
                'receipt_status' => 20,
                'receipt_time' => time(),
                'order_status' => 30,
                'virtual_content' => $order['product'][0]['virtual_content'],
            ]);
            // 執行訂單完成後的操作
            $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
            $OrderCompleteService->complete([$order], $order['app_id']);
            $order->sendWxExpress('', '');
        }
        return true;
    }
}