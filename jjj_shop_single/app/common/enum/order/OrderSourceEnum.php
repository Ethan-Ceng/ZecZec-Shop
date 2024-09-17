<?php

namespace app\common\enum\order;

use MyCLabs\Enum\Enum;

/**
 * 訂單來源
 */
class OrderSourceEnum extends Enum
{
    // 普通訂單
    const MASTER = 10;
    // 積分訂單
    const POINTS = 20;
    // 拼團
    const ASSEMBLE = 30;
    // 砍價
    const BARGAIN = 40;
    // 秒殺
    const SECKILL = 50;
    //禮包購
    const GIFT = 60;
    //抽獎
    const LOTTERY = 70;
    //預售訂單
    const ADVANCE = 80;

    /**
     * 獲取列舉資料
     */
    public static function data()
    {
        return [
            self::MASTER => [
                'name' => '普通',
                'value' => self::MASTER,
            ],
            self::POINTS => [
                'name' => '積分',
                'value' => self::POINTS,
            ],
            self::ASSEMBLE => [
                'name' => '拼團',
                'value' => self::ASSEMBLE,
            ],
            self::BARGAIN => [
                'name' => '砍價',
                'value' => self::BARGAIN,
            ],
            self::SECKILL => [
                'name' => '秒殺',
                'value' => self::SECKILL,
            ],
            self::GIFT => [
                'name' => '禮包購',
                'value' => self::GIFT,
            ],
            self::LOTTERY => [
                'name' => '抽獎',
                'value' => self::LOTTERY,
            ],
            self::ADVANCE => [
                'name' => '預售',
                'value' => self::ADVANCE,
            ],
        ];
    }

}