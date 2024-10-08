<?php

namespace app\common\model\plus\point;

use app\common\model\BaseModel;

/**
 * Class Exchange
 * 積分兌換模型
 * @package app\common\model\plus\exchange
 */
class Product extends BaseModel
{
    protected $name = 'point_product';
    protected $pk = 'point_product_id';


    /**
     * 詳情
     */
    public static function detail($point_product_id, $with = [])
    {
        return (new static())->with($with)->find($point_product_id);
    }

    /**
     *關聯商品表
     */
    public function product()
    {
        return $this->belongsTo('app\\common\\model\\product\\Product', 'product_id', 'product_id');
    }

    /**
     *關聯商品表
     */
    public function sku()
    {
        return $this->hasMany('app\\common\\model\\plus\\point\\ProductSku');
    }
}