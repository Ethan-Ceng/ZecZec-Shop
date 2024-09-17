<?php

namespace app\api\service\order\paysuccess\source;

use app\common\enum\settings\DeliveryTypeEnum;
use app\common\library\helper;
use app\api\model\plus\agent\Apply as AgentApplyModel;
use app\common\service\order\OrderPrinterService;
use app\common\service\order\OrderCompleteService;
use app\common\enum\order\OrderTypeEnum;

/**
 * 普通訂單支付成功後的回撥
 */
class MasterPaySuccessService
{
    /**
     * 回撥方法
     */
    public function onPaySuccess($order)
    {
        // 小票列印
        (new OrderPrinterService)->printTicket($order);
        // 購買指定商品成為分銷商
        $this->becomeAgentUser($order);
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

    /**
     * 購買指定商品成為分銷商
     */
    private function becomeAgentUser($order)
    {
        // 整理商品id集
        $productIds = helper::getArrayColumn($order['product'], 'product_id');
        $model = new AgentApplyModel;
        return $model->becomeAgentUser($order['user_id'], $productIds, $order['app_id']);
    }

}