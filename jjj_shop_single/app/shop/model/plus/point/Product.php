<?php

namespace app\shop\model\plus\point;

use app\common\model\plus\point\Product as ProductModel;
use app\common\model\plus\point\ProductSku as ProductSkuModel;

/**
 * Class Exchange
 * 積分兌換模型
 * @package app\shop\model\plus\exchange
 */
class Product extends ProductModel
{
    /*
     * 獲取列表
     */
    public function getList($query = [])
    {
        // 獲取列表資料
        return $this->with(['product.image.file','sku'])
            ->where('is_delete','=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->paginate($query);
    }

    /*
     * 獲取排除id
     */
    public function getExcludeIds()
    {
        // 獲取列表資料
        return $this->field(['product_id'])->where('is_delete','=', 0)
            ->select()->toArray();
    }
    /**
     * 新增商品
     * @param $data
     * @return bool
     */
    public function saveProduct($data, $isUpdate = false)
    {
        $product = $data['product'];
        $this->startTrans();
        try {
            $stock = array_sum(array_column($product['spec_list'], 'point_stock'));
            //新增商品表
            $this->save([
                'product_id' => $data['product_id'],
                'limit_num' => $data['limit_num'],
                'sort' => $data['sort'],
                'status' => $data['status'],
                'stock' => $stock,
                'app_id' => self::$app_id
            ]);
            //商品規格
            $sku_model = new ProductSkuModel();
            $save_data = [];
            $not_in_sku_id = [];
            foreach ($product['spec_list'] as $sku) {
                $sku_data = [
                    'point_product_id' => $this['point_product_id'],
                    'product_id' => $data['product_id'],
                    'product_sku_id' => $sku['product_sku_id'],
                    'point_stock' => $sku['point_stock'],
                    'point_num' => $sku['point_num'],
                    'product_attr' => $sku['product_attr'],
                    'product_price' => $sku['product_price'],
                    'point_money' => $sku['point_money'],
                    'app_id' => self::$app_id,
                ];
                if($sku['point_product_sku_id'] > 0){
                    $detail = $sku_model->find($sku['point_product_sku_id']);
                    if($detail){
                        $detail->save($sku_data);
                        array_push($not_in_sku_id, $sku['point_product_sku_id']);
                    }
                }else{
                    $save_data[] = $sku_data;
                }
            }

            //刪除規格
            count($not_in_sku_id) > 0 && $sku_model->where('point_product_id', '=', $data['point_product_id'])
                ->whereNotIn('point_product_sku_id', $not_in_sku_id)
                ->delete();
            //新增規格
            count($save_data) > 0 && $sku_model->saveAll($save_data);
            $this->commit();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
        return true;
    }

    /**
     * 檢查商品是否存在
     * @param int $product_id
     */
    public function checkProduct($product_id)
    {
        return $this->where('product_id', '=', $product_id)->where('is_delete', '=', 0)->find();
    }

    /**
     * 獲取商品資訊
     * @param $id
     */
    public function getPointData($arr)
    {
        return $this->where('point_product_id', '=', $arr['point_product_id'])->with(['product.image.file','sku'])->find();
    }

    /**
     * @param $data
     * 修改商品資訊
     */
    public function edit($data)
    {
        $this->startTrans();
        try {
            //新增商品表
            $this->save([
                'limit_num' => $data['limit_num'],
                'sort' => $data['sort'],
                'stock' => isset($data['stock'])?$data['stock']: 0,
                'status' => $data['status'],
            ]);
            if($data['spec_type'] == 20) {
                $stock = 0;//總庫存
                foreach ($data['specData']['spec_list'] as $item) {
                    $sku = $this->getSkuModel($item['spec_form']['point_product_sku_id']);
                    $sku->save([
                        'stock' => $item['spec_form']['stock'],
                        'point_num' => $item['spec_form']['point_num'],
                        'point_money' => $item['spec_form']['point_money'],
                    ]);
                    $stock += $item['spec_form']['stock'];
                }
                $this->save([
                    'stock' => $stock,
                ]);
            }else{
                $sku_model = $this['sku'][0];
                //單規格
                $sku_model->save([
                    'stock' => $data['skuData']['stock'],
                    'point_num' => $data['skuData']['point_num'],
                    'point_money' => $data['skuData']['point_money'],
                ]);
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
        return true;
    }

    private function getSkuModel($point_product_sku_id){
        foreach ($this['sku'] as $sku){
            if($sku['point_product_sku_id'] == $point_product_sku_id){
                return $sku;
            }
        }
        return false;
    }

    /**
     * 刪除
     */
    public function del($id)
    {
        return $this->where('point_product_id', '=', $id)->update([
            'is_delete' => 1
        ]);
    }

    /**
     * 商品ID是否存在
     */
    public static function isExistProductId($productId)
    {
        return !!(new static)->where('product_id', '=', $productId)
            ->where('is_delete', '=', 0)
            ->value('point_product_id');
    }
}