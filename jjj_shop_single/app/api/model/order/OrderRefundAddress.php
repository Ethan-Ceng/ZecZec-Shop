<?php

namespace app\api\model\order;

use app\common\model\order\OrderRefundAddress as OrderRefundAddressModel;

/**
 * 售後單退貨地址模型
 */
class OrderRefundAddress extends OrderRefundAddressModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'app_id',
        'create_time'
    ];

}