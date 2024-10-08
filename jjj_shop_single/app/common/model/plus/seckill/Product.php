<?php

namespace app\common\model\plus\seckill;

use app\common\model\BaseModel;

/**
 * 參與記錄模型
 */
class Product extends BaseModel
{
    protected $name = 'seckill_product';
    protected $pk = 'seckill_product_id';


    protected $append = ['product_sales'];

    /**
     * 計算顯示銷量 (初始銷量 + 實際銷量)
     */
    public function getProductSalesAttr($value, $data)
    {
        return $data['sales_initial'] + $data['total_sales'];
    }

    public static function detail($seckill_product_id, $with = ['product.sku', 'seckillSku'])
    {
        return (new static())->with($with)->where('seckill_product_id', '=', $seckill_product_id)->find();
    }

    public function active()
    {
        return $this->belongsTo('app\\common\\model\\plus\\seckill\\Active', 'seckill_activity_id', 'seckill_activity_id');
    }

    public function product()
    {
        return $this->belongsTo('app\\common\\model\\product\\Product', 'product_id', 'product_id');
    }

    public function seckillSku()
    {
        return $this->hasMany('seckillSku', 'seckill_product_id', 'seckill_product_id');
    }


}