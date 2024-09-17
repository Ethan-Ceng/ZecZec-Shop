<?php

namespace app\api\service\order\paysuccess\source;

/**
 * 拼團訂單支付成功後的回撥
 */
class AssemblePaySuccessService
{
    /**
     * 回撥方法
     */
    public function onPaySuccess($order)
    {
        return true;
    }
}