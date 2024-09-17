<?php

namespace app\common\model\product;

use app\common\model\BaseModel;
/**
 * 商品規格關係模型
 */
class ProductSpecRel extends BaseModel
{
    protected $name = 'product_spec_rel';
    protected $pk = 'id';
    protected $updateTime = false;

    /**
     * 關聯規格組
     */
    public function spec()
    {
        return $this->belongsTo('Spec','');
    }

}
