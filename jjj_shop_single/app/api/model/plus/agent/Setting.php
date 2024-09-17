<?php

namespace app\api\model\plus\agent;

use app\common\model\plus\agent\Setting as SettingModel;

/**
 * 分銷商設定模型
 */
class Setting extends SettingModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'update_time',
    ];

}