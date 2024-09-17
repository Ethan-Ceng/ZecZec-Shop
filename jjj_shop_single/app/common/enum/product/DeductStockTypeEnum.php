<?php

namespace app\common\enum\product;


use MyCLabs\Enum\Enum;

/**
 * 列舉類：商品庫存計算方式
 */
class DeductStockTypeEnum extends Enum
{
    // 下單減庫存
    const CREATE = 10;

    // 付款減庫存
    const PAYMENT = 20;

    /**
     * 獲取列舉型別值
     */
    public static function data()
    {
        return [
            self::CREATE => [
                'name' => '下單減庫存',
                'value' => self::CREATE,
            ],
            self::PAYMENT => [
                'name' => '付款減庫存',
                'value' => self::PAYMENT,
            ],
        ];
    }

}