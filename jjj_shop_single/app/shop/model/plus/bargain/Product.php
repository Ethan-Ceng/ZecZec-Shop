<?php

namespace app\shop\model\plus\bargain;

use app\common\model\plus\bargain\Product as ProductModel;
use app\common\model\plus\bargain\BargainSku as BargainSkuModel;

/**
 * 砍價商品模型
 */
class Product extends ProductModel
{
    /**
     * 獲取砍價商品列表
     */
    public static function getList($bargain_activity_id)
    {
        return (new static())->with(['product', 'bargainSku'])
            ->where('bargain_activity_id', '=', $bargain_activity_id)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }

    /**
     * 檢查商品是否存在
     */
    public function checkProduct($product_id)
    {
        return $this->where('product_id', '=', $product_id)->find();
    }

    /**
     * 新增
     */
    public function add($bargain_activity_id, $product_list)
    {
        //新增活動
        foreach ($product_list as $product){
            $this->addProduct($bargain_activity_id, $product);
        }
    }

    /**
     * 修改
     */
    public function edit($bargain_activity_id, $product_list)
    {
        //新增活動
        foreach ($product_list as $product){
            $this->addProduct($bargain_activity_id, $product, true);
        }
    }
    /**
     * 新增商品
     */
    public function addProduct($bargain_activity_id, $product, $isUpdate = false)
    {
        //新增商品
        $stock = array_sum(array_column($product['spec_list'], 'bargain_stock'));
        $arr = [
            'product_id' => $product['product_id'],
            'limit_num' => $product['limit_num'],
            'stock' => $stock,
            'bargain_activity_id' => $bargain_activity_id,
            'sort' => $product['sort'],
            'sales_initial' => $product['sales_initial'],
            'is_delete' => $product['is_delete'],
            'app_id' => self::$app_id,
        ];
        if($isUpdate){
            $model = static::detail($product['bargain_product_id'])?:new self();
        }else{
            $model = new self();
        }
        $model->save($arr);
        //商品規格
        $sku_model = new BargainSkuModel();
        $save_data = [];
        $not_in_sku_id = [];
        foreach ($product['spec_list'] as $sku) {
            $sku_data = [
                'bargain_product_id' => $model['bargain_product_id'],
                'product_id' => $product['product_id'],
                'product_sku_id' => $sku['product_sku_id'],
                'bargain_price' => $sku['bargain_price'],
                'bargain_num' => $product['spec_list'][0]['bargain_num'],
                'product_price' => $sku['product_price'],
                'bargain_stock' => $sku['bargain_stock'],
                'product_attr' => isset($sku['product_attr'])?$sku['product_attr']:'',
                'bargain_activity_id' => $bargain_activity_id,
                'app_id' => self::$app_id,
            ];
            if($sku['bargain_product_sku_id'] > 0){
                $detail = $sku_model->find($sku['bargain_product_sku_id']);
                if($detail){
                    $detail->save($sku_data);
                    array_push($not_in_sku_id, $sku['bargain_product_sku_id']);
                }
            }else{
                $save_data[] = $sku_data;
            }
        }

        //刪除規格
        count($not_in_sku_id) > 0 && $sku_model->where('bargain_product_id', '=', $model['bargain_product_id'])
            ->whereNotIn('bargain_product_sku_id', $not_in_sku_id)
            ->delete();
        //新增規格
        count($save_data) > 0 && $sku_model->saveAll($save_data);
    }

    /**
     * 商品ID是否存在,並且活動未結束，或未刪除
     */
    public static function isExistProductId($productId)
    {
        return !!(new static)->alias('product')
            ->join('bargain_activity activity', 'activity.bargain_activity_id = product.bargain_activity_id','left')
            ->where('product.product_id', '=', $productId)
            ->where('product.is_delete', '=', 0)
            ->where('activity.end_time', '>', time())
            ->where('activity.is_delete', '=', 0)
            ->value('product.bargain_product_id');
    }
}