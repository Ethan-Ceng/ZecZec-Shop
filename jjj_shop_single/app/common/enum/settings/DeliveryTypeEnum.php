<?php

namespace app\common\enum\settings;

use MyCLabs\Enum\Enum;
/**
 * 配送方式列舉類
 */
class DeliveryTypeEnum extends Enum
{
    // 快遞配送
    const EXPRESS = 10;

    // 上門自提
    const EXTRACT = 20;

    // 無需物流
    const NO_EXPRESS = 30;

    /**
     * 獲取列舉資料
     */
    public static function data()
    {
        return [
            self::EXPRESS => [
                'name' => '快遞配送',
                'value' => self::EXPRESS,
            ],
            self::NO_EXPRESS => [
                'name' => '無需物流',
                'value' => self::NO_EXPRESS,
            ],
            self::EXTRACT => [
                'name' => '上門自提',
                'value' => self::EXTRACT,
            ],
        ];
    }

}