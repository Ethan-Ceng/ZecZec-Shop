<?php

namespace app\common\model\plus\assemble;

use app\common\model\BaseModel;

/**
 * 參與記錄模型
 */
class Product extends BaseModel
{
    protected $name = 'assemble_product';
    protected $pk = 'assemble_product_id';

    protected $append = ['product_sales'];

    /**
     * 計算顯示銷量 (初始銷量 + 實際銷量)
     */
    public function getProductSalesAttr($value, $data)
    {
        return $data['sales_initial'] + $data['total_sales'];
    }

    public static function detail($assemble_product_id, $with = ['product.sku', 'assembleSku'])
    {
        return (new static())->with($with)->where('assemble_product_id', '=', $assemble_product_id)->find();
    }

    public function active()
    {
        return $this->belongsTo('app\\common\\model\\plus\\assemble\\Active', 'assemble_activity_id', 'assemble_activity_id');
    }

    public function product()
    {
        return $this->belongsTo('app\\common\\model\\product\\Product', 'product_id', 'product_id');
    }

    public function assembleSku()
    {
        return $this->hasMany('AssembleSku', 'assemble_product_id', 'assemble_product_id');
    }

}