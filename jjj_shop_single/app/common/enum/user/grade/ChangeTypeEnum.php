<?php

namespace app\common\enum\user\grade;

use MyCLabs\Enum\Enum;

/**
 * 會員等級變更記錄表 -> 變更型別
 */
class ChangeTypeEnum extends Enum
{
    // 後臺管理員設定
    const ADMIN_USER = 10;

    // 自動升級
    const AUTO_UPGRADE = 20;

}