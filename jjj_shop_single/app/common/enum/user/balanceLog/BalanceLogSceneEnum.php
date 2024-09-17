<?php

namespace app\common\enum\user\balanceLog;

use MyCLabs\Enum\Enum;

/**
 * 餘額變動場景列舉類
 */
class BalanceLogSceneEnum extends Enum
{
    // 使用者充值
    const RECHARGE = 10;

    // 使用者消費
    const CONSUME = 20;

    // 管理員操作
    const ADMIN = 30;

    // 訂單退款
    const REFUND = 40;

    // 活動獎勵
    const AWARD = 50;

    // 積分轉換
    const POINTS = 60;

    // 餘額提現
    const CASH = 70;

    /**
     * 獲取訂單型別值
     */
    public static function data()
    {
        return [
            self::RECHARGE => [
                'name' => '使用者充值',
                'value' => self::RECHARGE,
                'describe' => '使用者充值：%s',
            ],
            self::CONSUME => [
                'name' => '使用者消費',
                'value' => self::CONSUME,
                'describe' => '使用者消費：%s',
            ],
            self::ADMIN => [
                'name' => '管理員操作',
                'value' => self::ADMIN,
                'describe' => '後臺管理員 [%s] 操作',
            ],
            self::REFUND => [
                'name' => '訂單退款',
                'value' => self::REFUND,
                'describe' => '訂單退款：%s',
            ],
            self::AWARD => [
                'name' => '活動獎勵',
                'value' => self::AWARD,
                'describe' => '活動獎勵：%s',
            ],
            self::POINTS => [
                'name' => '積分轉換',
                'value' => self::POINTS,
                'describe' => '積分轉換餘額',
            ],
            self::CASH => [
                'name' => '餘額提現',
                'value' => self::CASH,
                'describe' => '餘額提現',
            ],
        ];
    }

}