<?php

namespace app\api\model\product;

use app\common\model\settings\DeliveryRule as DeliveryRuleModel;
/**
 * 配送模板區域及運費模型
 */
class DeliveryRule extends DeliveryRuleModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'app_id',
        'create_time',
        'update_time'
    ];

}