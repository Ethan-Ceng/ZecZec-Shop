<?php

namespace app\api\model\settings;

use app\common\model\settings\Setting as SettingModel;

/**
 * 設定模型
 */
class Setting extends SettingModel
{
    /**
     * 獲取積分名稱
     */
    public static function getPointsName()
    {
        return static::getItem('points')['points_name'];
    }

    /**
     * 獲取積分名稱
     */
    public static function getBargain()
    {
        return static::getItem('bargain');
    }
}