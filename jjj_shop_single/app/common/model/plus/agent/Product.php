<?php

namespace app\common\model\plus\agent;

use app\common\model\BaseModel;

/**
 * 分銷商使用者模型
 */
class Product extends BaseModel
{
    protected $name = 'agent_product';
    protected $pk = 'product_id';

    /**
     * 超管使用者資訊
     */
    public static function detail($product_id)
    {
        return (new static())->where('product_id', '=', $product_id)->find();
    }

}