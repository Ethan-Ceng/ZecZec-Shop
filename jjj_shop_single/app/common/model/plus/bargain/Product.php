<?php

namespace app\common\model\plus\bargain;

use app\common\model\BaseModel;

/**
 * 砍價商品模型
 * @package app\common\model\plus\bargain
 */
class Product extends BaseModel
{
    protected $name = 'bargain_product';
    protected $pk = 'bargain_product_id';

    protected $append = ['product_sales'];

    /**
     * 計算顯示銷量 (初始銷量 + 實際銷量)
     */
    public function getProductSalesAttr($value, $data)
    {
        return $data['sales_initial'] + $data['total_sales'];
    }

    /**
     *關聯商品主表
     */
    public function product()
    {
        return $this->hasOne('app\\common\\model\\product\\Product', 'product_id', 'product_id');
    }

    /**
     * 詳情
     */
    public static function detail($bargain_product_id, $with = [])
    {
        return (new static())->with($with)->find($bargain_product_id);
    }

    /**
     *關聯商品規格表
     */
    public function bargainSku()
    {
        return $this->hasMany('app\\common\\model\\plus\\bargain\\BargainSku', 'bargain_product_id', 'bargain_product_id');
    }

    /**
     *關聯活動表
     */
    public function active()
    {
        return $this->belongsTo('app\\common\\model\\plus\\bargain\\Active', 'bargain_id', 'bargain_id');
    }
}