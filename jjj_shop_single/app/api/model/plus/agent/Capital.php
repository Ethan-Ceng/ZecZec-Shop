<?php

namespace app\api\model\plus\agent;

use app\common\model\plus\agent\Capital as CapitalModel;

/**
 * 分銷商資金明細模型
 */
class Capital extends CapitalModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'create_time',
        'update_time',
    ];

}