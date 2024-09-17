<?php

namespace app\api\model\product;

use app\common\model\settings\Delivery as DeliveryModel;

/**
 * 運費模板模型
 */
class Delivery extends DeliveryModel
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