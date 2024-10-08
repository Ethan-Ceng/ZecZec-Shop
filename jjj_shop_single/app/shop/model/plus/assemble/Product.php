<?php

namespace app\shop\model\plus\assemble;

use app\common\model\plus\assemble\Product as ProductModel;
/**
 * 拼團商品模型
 * @package app\shop\model\plus\invitationgift
 */
class Product extends ProductModel
{
    /**
     * 獲取秒殺商品列表
     * @param $param
     */
    public static function getList($assemble_activity_id)
    {
        return (new static())->with(['product', 'assembleSku'])
            ->where('assemble_activity_id', '=', $assemble_activity_id)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }
    /**
     * 新增
     * @param $data
     */
    public function add($assemble_activity_id, $product_list)
    {
        //新增活動
        foreach ($product_list as $product){
            $this->addProduct($assemble_activity_id, $product);
        }
    }

    public function addProduct($assemble_activity_id, $product, $isUpdate = false)
    {
        //新增商品
        $stock = array_sum(array_column($product['spec_list'], 'assemble_stock'));
        $arr = [
            'product_id' => $product['product_id'],
            'limit_num' => $product['limit_num'],
            'stock' => $stock,
            'assemble_activity_id' => $assemble_activity_id,
            'assemble_num' => $product['assemble_num'],
            'sort' => $product['sort'],
            'sales_initial' => $product['sales_initial'],
            'is_delete' => $product['is_delete'],
            'app_id' => self::$app_id,
        ];
        if($isUpdate){
            $model = static::detail($product['assemble_product_id'])?:new self();
        }else{
            $model = new self();
        }
        $model->save($arr);
        //商品規格
        $sku_model = new AssembleSku();
        $save_data = [];
        $not_in_sku_id = [];
        foreach ($product['spec_list'] as $sku) {
            $sku_data = [
                'assemble_product_id' => $model['assemble_product_id'],
                'product_id' => $product['product_id'],
                'product_sku_id' => $sku['product_sku_id'],
                'assemble_price' => $sku['assemble_price'],
                'product_price' => $sku['product_price'],
                'assemble_stock' => $sku['assemble_stock'],
                'product_attr' => isset($sku['product_attr'])?$sku['product_attr']:'',
                'assemble_activity_id' => $assemble_activity_id,
                'app_id' => self::$app_id,
            ];
            if($sku['assemble_product_sku_id'] > 0){
                $detail = $sku_model->find($sku['assemble_product_sku_id']);
                if($detail){
                    $detail->save($sku_data);
                    array_push($not_in_sku_id, $sku['assemble_product_sku_id']);
                }
            }else{
                $save_data[] = $sku_data;
            }
        }

        //刪除規格
        count($not_in_sku_id) > 0 && $sku_model->where('assemble_product_id', '=', $model['assemble_product_id'])
            ->whereNotIn('assemble_product_sku_id', $not_in_sku_id)
            ->delete();
        //新增規格
        count($save_data) > 0 && $sku_model->saveAll($save_data);
    }


    /**
     * 修改
     */
    public function edit($assemble_activity_id, $product_list)
    {
        //新增活動
        foreach ($product_list as $product){
            $this->addProduct($assemble_activity_id, $product, true);
        }
    }

    public function del($assemble_product_id)
    {
        $this->startTrans();
        try {
            self::destroy($assemble_product_id);
            $model = new AssembleSku();
            $model->delAll($assemble_product_id);
            // 事務提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     *獲取指定活動商品
     */
    public function getProductList($assemble_activity_id, $param = [])
    {
        $model = $this;
        $res = $model->with(['product.image.file', 'assembleSku', 'active.file'])
            ->where('assemble_activity_id', '=', $assemble_activity_id)
            ->paginate($param);
        if (!empty($res)) {
            $res = $res->toArray();
            foreach ($res['data'] as $key => $val) {
                $arr = array_column($res['data'][$key]['assembleSku'], 'assemble_price');
                if (count($arr) == 1) {
                    $res['data'][$key]['assemble_price'] = '￥' . current($arr);
                } else {
                    sort($arr);
                    $res['data'][$key]['assemble_price'] = '￥' . current($arr) . ' - ￥' . end($arr);
                }

            }
        }
        return $res;
    }

    public function getAssembleDetail($assemble_product_id)
    {
        $res = $this->with(['product.image.file', 'assembleSku.productSku.image'])
            ->where('assemble_product_id', '=', $assemble_product_id)->find();
        if (!empty($res)) {
            $arr = array_column($res->toArray()['assembleSku'], 'assemble_price');
            foreach ($res['assembleSku'] as $key => $val) {
                $res['assembleSku'][$key]['price'] = $res['assembleSku'][$key]['productSku']['product_price'];
            }
            $arr1 = array_column($res->toArray()['assembleSku'], 'price');
            sort($arr);
            sort($arr1);
            $res['assemble_price'] = '￥' . current($arr);
            $res['line_price'] = '￥' . current($arr1);
            if (count($arr) > 1) {
                $res['assemble_price'] = '￥' . current($arr) . ' - ￥' . end($arr);
                $res['line_price'] = '￥' . current($arr1) . ' - ￥' . end($arr1);
            }
        }
        return $res;
    }

    /**
     * 商品ID是否存在,並且活動未結束，或未刪除
     */
    public static function isExistProductId($productId)
    {
        return !!(new static)->alias('product')
            ->join('assemble_activity activity', 'activity.assemble_activity_id = product.assemble_activity_id','left')
            ->where('product.product_id', '=', $productId)
            ->where('product.is_delete', '=', 0)
            ->where('activity.end_time', '>', time())
            ->where('activity.is_delete', '=', 0)
            ->value('product.assemble_product_id');
    }
}