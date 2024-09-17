<?php

namespace app\common\model\product;

use app\common\model\BaseModel;

/**
 * 商品SKU模型
 */
class ProductSku extends BaseModel
{
    protected $name = 'product_sku';
    protected $pk = 'product_sku_id';
    /**
     * 規格圖片
     */
    public function image()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }


    /**
     * 獲取sku資訊詳情
     */
    public static function detail($productId, $specSkuId)
    {
        return (new static())->where('product_id', '=', $productId)
        ->where('spec_sku_id', '=', $specSkuId)->find();
    }

}
