<?php

namespace app\shop\model\plus\advance;

use app\common\model\plus\advance\Product as ProductModel;
use app\common\model\plus\advance\AdvanceSku as AdvanceSkuModel;

/**
 * Class Partake
 * 預售商品模型
 */
class Product extends ProductModel
{
    /*
     * 獲取列表
     */
    public function getList($param)
    {
        // 獲取列表資料
        return $this->with(['product.image.file', 'sku'])
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->paginate($param);
    }

    /*
     * 獲取排除id
     */
    public function getExcludeIds()
    {
        // 獲取列表資料
        return $this->where('is_delete', '=', 0)
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
            $stock = array_sum(array_column($product['spec_list'], 'advance_stock'));
            //新增商品表
            $this->save([
                'product_id' => $data['product_id'],
                'money' => $data['money'],
                'limit_num' => $data['limit_num'],
                'sort' => $data['sort'],
                'status' => $data['status'],
                'stock' => $stock,
                'start_time' => strtotime($data['active_time'][0]),
                'end_time' => strtotime($data['active_time'][1]),
                'app_id' => self::$app_id
            ]);
            //商品規格
            $sku_model = new AdvanceSkuModel();
            $save_data = [];
            $not_in_sku_id = [];
            foreach ($product['spec_list'] as $sku) {
                $sku_data = [
                    'advance_product_id' => $this['advance_product_id'],
                    'product_id' => $data['product_id'],
                    'product_sku_id' => $sku['product_sku_id'],
                    'advance_stock' => $sku['advance_stock'],
                    'product_attr' => $sku['product_attr'],
                    'product_price' => $sku['product_price'],
                    'advance_price' => $sku['advance_price'],
                    'app_id' => self::$app_id,
                ];
                if ($sku['advance_product_sku_id'] > 0) {
                    $detail = $sku_model->find($sku['advance_product_sku_id']);
                    if ($detail) {
                        $detail->save($sku_data);
                        array_push($not_in_sku_id, $sku['advance_product_sku_id']);
                    }
                } else {
                    $save_data[] = $sku_data;
                }
            }

            //刪除規格
            count($not_in_sku_id) > 0 && $sku_model->where('advance_product_id', '=', $data['advance_product_id'])
                ->whereNotIn('advance_product_sku_id', $not_in_sku_id)
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
        return $this->where('advance_product_id', '=', $arr['advance_product_id'])->with(['product.image.file', 'sku'])->find();
    }

    /**
     * 刪除
     */
    public function del($id)
    {
        return $this->where('advance_product_id', '=', $id)->update([
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
            ->value('advance_product_id');
    }

    /**
     * 獲取diy預售活動商品
     */
    public function getDiyProduct()
    {
        $res = $this->with(['advanceProduct.advanceSku', 'advanceProduct.product'])->where('end_time', '>', time())
            ->where('end_time', '>=', time())->find();
        if (isset($res['advanceProduct'])) {
            $list = [];
            foreach ($res['advanceProduct'] as $k => $val) {
                $list[$k]['product_name'] = $val['product']['product_name'];
                $list[$k]['product_id'] = $val['product_id'];
                $list[$k]['product_name'] = $val['product']['product_name'];
            }
            return $res['advanceProduct'];
        }
        return [];
    }
}