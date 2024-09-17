<?php

namespace app\api\model\settings;

use app\common\model\settings\Express as ExpressModel;

/**
 * 物流公司模型
 */
class Express extends ExpressModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'express_code',
        'sort',
        'app_id',
        'create_time',
        'update_time'
    ];

}