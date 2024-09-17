<?php

namespace app\common\enum\message;

use MyCLabs\Enum\Enum;

/**
 * 訊息型別列舉類
 */
class MessageTypeEnum extends Enum
{
    // 訂單
    const ORDER = 10;

    // 分銷
    const AGENT = 20;


    /**
     * 獲取列舉資料
     */
    public static function data()
    {
        return [
            self::ORDER => [
                'value' => self::ORDER,
                'name' => '訂單',
            ],
            self::AGENT => [
                'value' => self::AGENT,
                'name' => '分銷',
            ],
        ];
    }

}