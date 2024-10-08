<?php

namespace app\shop\model\order;

use app\common\model\order\OrderRefundAddress as OrderRefundAddressModel;
use app\shop\model\settings\ReturnAddress;

/**
 * 售後單退貨地址模型
 */
class OrderRefundAddress extends OrderRefundAddressModel
{
    /**
     * 新增售後單退貨地址記錄
     */
    public function add($order_refund_id, $address_id)
    {
        $detail = ReturnAddress::detail($address_id);
        return $this->save([
            'order_refund_id' => $order_refund_id,
            'name' => $detail['name'],
            'phone' => $detail['phone'],
            'detail' => $detail['detail'],
            'app_id' => self::$app_id
        ]);
    }

}