<?php

namespace app\common\enum\settings;

use MyCLabs\Enum\Enum;

/**
 * 小票印表機型別 列舉類
 */
class PrinterTypeEnum extends Enum
{
    // 飛鵝印表機
    const FEI_E_YUN = 'FEI_E_YUN';

    // 365雲列印
    const PRINT_CENTER = 'PRINT_CENTER';

    // 芯燁雲列印
    const XP_YUN = 'XP_YUN';

    // 獲取印表機型別名稱
    public static function getTypeName()
    {
        return [
            self::FEI_E_YUN => '飛鵝印表機',
            self::PRINT_CENTER => '365雲列印',
            self::XP_YUN => '芯燁雲列印',
        ];
    }

}