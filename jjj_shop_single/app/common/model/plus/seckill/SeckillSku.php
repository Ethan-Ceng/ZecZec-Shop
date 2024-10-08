<?php

namespace app\common\model\plus\seckill;

use app\common\model\BaseModel;

/**
 * Class Partake
 * 參與記錄模型
 * @package app\common\model\plus\invitationgift
 */
class SeckillSku extends BaseModel
{
    protected $name = 'seckill_product_sku';
    protected $pk = 'seckill_product_sku_id';


    public static function detail($seckill_product_sku_id, $with = [])
    {
        return (new static())->with($with)->where('seckill_product_sku_id', '=', $seckill_product_sku_id)->find();
    }
    /**
     *關聯商品表
     */
    public function product()
    {
        return $this->belongsTo('app\\common\\model\\plus\\seckill\\Product', 'seckill_product_id', 'seckill_product_id');
    }
    /**
     *關聯商品sku表
     */
    public function productSku()
    {
        return $this->belongsTo('app\\common\\model\\product\\ProductSku', 'product_sku_id', 'product_sku_id');
    }
}