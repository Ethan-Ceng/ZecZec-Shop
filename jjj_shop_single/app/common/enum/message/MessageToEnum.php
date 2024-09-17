<?php

namespace app\common\enum\message;

use MyCLabs\Enum\Enum;

/**
 * 訊息傳送物件列舉類
 */
class MessageToEnum extends Enum
{
    // 會員
    const MEMBER = 10;

    // 商家
    const SHOP = 20;

    /**
     * 獲取列舉資料
     */
    public static function data()
    {
        return [
            self::MEMBER => [
                'value' => self::MEMBER,
                'name' => '會員',
            ],
            self::SHOP => [
                'value' => self::SHOP,
                'name' => '商家',
            ],
        ];
    }

}