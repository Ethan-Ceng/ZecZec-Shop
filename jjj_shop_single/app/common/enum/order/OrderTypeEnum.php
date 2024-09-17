<?php

namespace app\common\enum\order;

use MyCLabs\Enum\Enum;

/**
 * 訂單型別列舉類,用於後期擴充套件，比如虛擬物品
 */
class OrderTypeEnum extends Enum
{
    // 商城訂單
    const MASTER = 10;

    // 禮包購訂單
    const GIFT = 20;

    // 餘額充值訂單
    const BALANCE = 30;

    // 預售定金訂單
    const FRONT = 40;

    // 預售尾款訂單
    const ADVANCE = 50;

    /**
     * 獲取訂單型別值
     */
    public static function data()
    {
        return [
            self::MASTER => [
                'name' => '商城訂單',
                'value' => self::MASTER,
            ],
            self::GIFT => [
                'name' => '禮包購訂單',
                'value' => self::GIFT,
            ],
            self::BALANCE => [
                'name' => '餘額充值訂單',
                'value' => self::BALANCE,
            ],
            self::FRONT => [
                'name' => '預售定金訂單',
                'value' => self::FRONT,
            ],
            self::ADVANCE => [
                'name' => '預售尾款訂單',
                'value' => self::ADVANCE,
            ],
        ];
    }

    /**
     * 獲取訂單型別名稱
     */
    public static function getTypeName()
    {
        static $names = [];

        if (empty($names)) {
            foreach (self::data() as $item)
                $names[$item['value']] = $item['name'];
        }

        return $names;
    }

}