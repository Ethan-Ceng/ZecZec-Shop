<?php

namespace app\api\service\order\checkpay;

use app\common\enum\order\OrderPayStatusEnum;
use app\common\enum\order\OrderStatusEnum;
use app\common\service\BaseService;

/**
 * 訂單支付檢查服務類
 */
abstract class CheckPayService extends BaseService
{
    /**
     * 判斷訂單是否允許付款
     */
    abstract public function checkOrderStatus($order);

    /**
     * 判斷商品狀態、庫存 (未付款訂單)
     */
    abstract protected function checkProductStatus($productList);

    /**
     * 判斷訂單狀態(公共)
     */
    protected function checkOrderStatusCommon($order)
    {
        // 判斷訂單狀態
        if (
            $order['order_status']['value'] != OrderStatusEnum::NORMAL
            || $order['pay_status']['value'] != OrderPayStatusEnum::PENDING
        ) {
            $this->error = '很抱歉，當前訂單不合法，無法支付';
            return false;
        }
        return true;
    }

}