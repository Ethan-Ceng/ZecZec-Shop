<?php

namespace app\common\enum\order;

use MyCLabs\Enum\Enum;

/**
 * 訂單支付方式列舉類
 */
class OrderPayTypeEnum extends Enum
{
    // 餘額支付
    const BALANCE = 10;

    // 微信支付
    const WECHAT = 20;

    // 支付寶支付
    const ALIPAY = 30;

    /**
     * 獲取列舉資料
     */
    public static function data()
    {
        return [
            self::BALANCE => [
                'name' => '餘額支付',
                'value' => self::BALANCE,
            ],
            self::WECHAT => [
                'name' => '微信支付',
                'value' => self::WECHAT,
            ],
            self::ALIPAY => [
                'name' => '支付寶支付',
                'value' => self::ALIPAY,
            ],
        ];
    }
}