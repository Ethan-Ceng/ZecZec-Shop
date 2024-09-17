<?php

namespace app\api\model\order;

use app\common\model\order\OrderProduct as OrderProductModel;

/**
 * 商品訂單模型
 */
class OrderProduct extends OrderProductModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'content',
        'app_id',
        'create_time',
    ];

    /**
     * 獲取未評價的商品
     */
    public function getNotCommentProductList($order_id)
    {
        return $this->where(['order_id' => $order_id, 'is_comment' => 0])->with(['orderM', 'image'])->select();
    }
}