<?php

namespace app\api\service\order\paysuccess\type;

use app\common\enum\order\OrderTypeEnum;

/**
 * 支付成功輔助工廠類
 */
class PayTypeSuccessFactory
{
    public static function getFactory($out_trade_no, $order_type, $pay_status = 10)
    {
        switch ($order_type) {
            case OrderTypeEnum::MASTER:
                return new MasterPaySuccessService($out_trade_no, $pay_status);
                break;
            case OrderTypeEnum::GIFT;
                return new GiftPaySuccessService($out_trade_no, $pay_status);
                break;
            case OrderTypeEnum::BALANCE;
                return new BalancePaySuccessService($out_trade_no, $pay_status);
                break;
            case OrderTypeEnum::FRONT;
                return new FrontPaySuccessService($out_trade_no, $pay_status);
                break;
        }
        return false;
    }
}