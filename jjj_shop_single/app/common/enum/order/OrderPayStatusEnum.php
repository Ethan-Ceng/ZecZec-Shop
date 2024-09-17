<?php

namespace app\common\enum\order;

use MyCLabs\Enum\Enum;

/**
 * 訂單支付狀態列舉類
 */
class OrderPayStatusEnum extends Enum
{
    // 待支付
    const PENDING = 10;

    // 支付成功
    const SUCCESS = 20;

    /**
     * 獲取列舉資料
     */
    public static function data()
    {
        return [
            self::PENDING => [
                'name' => '待付款',
                'value' => self::PENDING,
            ],
            self::SUCCESS => [
                'name' => '已付款',
                'value' => self::SUCCESS,
            ],
        ];
    }
}