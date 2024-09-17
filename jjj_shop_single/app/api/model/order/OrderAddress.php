<?php

namespace app\api\model\order;

use app\common\model\order\OrderAddress as OrderAddressModel;

/**
 * 訂單地址模型
 */
class OrderAddress extends OrderAddressModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'app_id',
        'create_time',
    ];

}