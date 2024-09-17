<?php

namespace app\shop\model\product;

use app\common\library\helper;
use app\common\model\product\Product as ProductModel;
use app\shop\service\ProductService;

/**
 * 商品模型
 */
class Product extends ProductModel
{
    /**
     * 獲取器：預告開啟購買時間
     */
    public function getPreviewTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * 新增商品
     */
    public function add(array $data)
    {
        if (!isset($data['image']) || empty($data['image'])) {
            $this->error = '請上傳商品圖片';
            return false;
        }
        //商品開始時間結束時間
        if (!empty($data['active_time'])) {
            $data['start_time'] = strtotime($data['active_time'][0]);
            $data['end_time'] = strtotime($data['active_time'][1]);
        }
        $data['content'] = isset($data['content']) ? $data['content'] : '';
        $data['alone_grade_equity'] = isset($data['alone_grade_equity']) ? json_decode($data['alone_grade_equity'], true) : '';
        $data['grade_ids'] = implode(',', $data['grade_ids']);
        $data['app_id'] = $data['sku']['app_id'] = self::$app_id;

        // 開啟事務
        $this->startTrans();
        try {
            // 新增商品
            $this->save($data);
            // 商品規格
            $this->addProductSpec($data);
            // 商品圖片
            $this->addProductImages($data['image']);
            // 商品詳情圖片
            if ($data['is_picture'] == 1) {
                $this->addProductContentImages($data['contentImage']);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 新增商品圖片
     */
    private function addProductImages($images)
    {
        $this->image()->delete();
        $data = array_map(function ($images) {
            return [
                'image_id' => isset($images['file_id']) ? $images['file_id'] : $images['image_id'],
                'app_id' => self::$app_id
            ];
        }, $images);
        return $this->image()->saveAll($data);
    }

    /**
     * 新增商品詳情圖片
     */
    private function addProductContentImages($images)
    {
        $this->contentImage()->delete();
        $data = array_map(function ($images) {
            return [
                'image_id' => isset($images['file_id']) ? $images['file_id'] : $images['image_id'],
                'image_type' => 1,
                'app_id' => self::$app_id
            ];
        }, $images);
        return $this->contentImage()->saveAll($data);
    }

    /**
     * 編輯商品
     */
    public function edit($data)
    {
        if (!isset($data['image']) || empty($data['image'])) {
            $this->error = '請上傳商品圖片';
            return false;
        }
        //商品開始時間結束時間
        if (!empty($data['active_time'])) {
            $data['start_time'] = strtotime($data['active_time'][0]);
            $data['end_time'] = strtotime($data['active_time'][1]);
        }
        if (!$data['delivery_id'] && $data['is_virtual'] == 0) {
            $this->error = '請選擇運費模板';
            return false;
        }
        $data['spec_type'] = isset($data['spec_type']) ? $data['spec_type'] : $this['spec_type'];
        $data['content'] = isset($data['content']) ? $data['content'] : '';
        $data['alone_grade_equity'] = isset($data['alone_grade_equity']) ? json_decode($data['alone_grade_equity'], true) : '';
        $data['grade_ids'] = implode(',', $data['grade_ids']);
        $data['app_id'] = $data['sku']['app_id'] = self::$app_id;
        $productSkuIdList = helper::getArrayColumn(($this['sku']), 'product_sku_id');
        return $this->transaction(function () use ($data, $productSkuIdList) {
            // 儲存商品
            $this->save($data);
            // 商品規格
            $this->addProductSpec($data, true, $productSkuIdList);
            // 商品圖片
            $this->addProductImages($data['image']);
            // 商品詳情圖片
            if ($data['is_picture'] == 1) {
                $this->addProductContentImages($data['contentImage']);
            }
            return true;
        });
    }

    /**
     * 新增商品規格
     */
    private function addProductSpec($data, $isUpdate = false, $productSkuIdList = [])
    {
        // 更新模式: 先刪除所有規格
        $model = new ProductSku;
        $isUpdate && $model->removeAll($this['product_id']);
        $stock = 0;//總庫存
        $product_price = 0;//價格
        $line_price = 0; //劃線價
        // 新增規格資料
        if ($data['spec_type'] == '10') {
            // 刪除多規格遺留資料
            $isUpdate && $model->removeSkuBySpec($this['product_id']);
            // 單規格
            $this->sku()->save($data['sku']);
            $stock = $data['sku']['stock_num'];
            $product_price = $data['sku']['product_price'];
            $line_price = $data['sku']['line_price'];
        } else if ($data['spec_type'] == '20') {
            // 新增商品與規格關係記錄
            $model->addProductSpecRel($this['product_id'], $data['spec_many']['spec_attr']);
            // 新增商品sku
            $model->addSkuList($this['product_id'], $data['spec_many']['spec_list'], $productSkuIdList);
            $product_price = $data['spec_many']['spec_list'][0]['spec_form']['product_price'];
            foreach ($data['spec_many']['spec_list'] as $item) {
                $stock += $item['spec_form']['stock_num'];
                if ($item['spec_form']['product_price'] < $product_price) {
                    $product_price = $item['spec_form']['product_price'];
                }
                if ($item['spec_form']['line_price'] < $line_price) {
                    $line_price = $item['spec_form']['line_price'];
                }
            }
        }
        $this->save([
            'product_stock' => $stock,
            'product_price' => $product_price,
            'line_price' => $line_price
        ]);
    }

    /**
     * 修改商品狀態
     */
    public function setStatus($state)
    {
        return $this->save(['product_status' => $state]) !== false;
    }

    /**
     * 軟刪除
     */
    public function setDelete()
    {
        if (ProductService::checkSpecLocked($this, 'delete')) {
            $this->error = '當前商品正在參與其他活動，不允許刪除';
            return false;
        }
        //  回收站，和未稽核透過的直接刪
        if ($this['product_status']['value'] == 30) {
            return $this->save(['is_delete' => 1]);
        } else {
            return $this->save(['product_status' => 30]);
        }
    }

    /**
     * 獲取當前商品總數
     */
    public function getProductTotal($where = [])
    {
        return $this->where('is_delete', '=', 0)->where($where)->count();
    }

    /**
     * 根據時間獲取當前商品總數
     */
    public function getProductTimeTotal($day = "")
    {
        $model = $this;
        if ($day) {
            $model = $model->where('create_time', 'between', [strtotime($day), strtotime($day) + 86399]);
        }
        return $model->where('is_delete', '=', 0)->count();
    }

    /**
     * 獲取商品告急數量總數
     */
    public function getProductStockTotal()
    {

        return $this->where('is_delete', '=', 0)->where('product_status', '=', 10)->whereBetween('product_stock', [1, 10])->count();
    }

    public function getProductId($search)
    {
        $res = $this->where('product_name', 'like', '%' . $search . '%')->select()->toArray();
        return array_column($res, 'product_id');
    }

    /**
     * 查詢指定商品
     */
    public function getProduct($value)
    {
        return $this->where('product_id', 'in', $value)->select();
    }

    /**
     * 獲取數量
     */
    public function getCount($type)
    {
        $model = $this;
        // 銷售中
        if ($type == 'sell') {
            $model = $model->where('product_status', '=', 10);
        }
        //倉庫中
        if ($type == 'lower') {
            $model = $model->where('product_status', '=', 20);
        }
        // 回收站
        if ($type == 'recovery') {
            $model = $model->where('product_status', '=', 30);
        }
        //庫存緊張
        if ($type == 'stock') {
            $model = $model->whereBetween('product_stock', [1, 10]);
            $model = $model->where('product_status', '=', 10);
        }
        //已售罄
        if ($type == 'over') {
            $model = $model->where('product_stock', '=', 0);
            $model = $model->where('product_status', '=', 10);
        }
        return $model->where('is_delete', '=', 0)
            ->count();
    }

    public static function getAll()
    {
        $model = new static();
        return $model->where('product_status', '=', 10)
            ->order(['product_sort' => 'asc', 'create_time' => 'desc'])
            ->select();
    }
}
