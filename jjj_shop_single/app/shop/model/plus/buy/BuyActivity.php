<?php

namespace app\shop\model\plus\buy;

use app\common\model\plus\buy\BuyActivity as BuyActivityModel;
use app\common\model\product\Product as ProductModel;

/**
 * 買送模型
 */
class BuyActivity extends BuyActivityModel
{
    /**
     * 獲取列表記錄
     */
    public function getList($data)
    {
        $model = $this;
        if (isset($data['status']) && $data['status'] > -1) {
            switch ($data['status']) {
                case 0:
                    $model = $model->where('start_time', '>', time());
                    break;
                case 1;
                    $model = $model->where('start_time', '<', time())->where('end_time', '>', time());
                    break;
                case 2;
                    $model = $model->where('end_time', '<', time());
                    break;
            }
        }
        if (isset($data['name']) && $data['name']) {
            $model = $model->where('name', 'like', '%' . trim($data['name']) . '%');
        }
        return $model->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($data);
    }

    /**
     * 新增記錄
     */
    public function add($data)
    {
        if (!isset($data['limit_product']) || !$data['limit_product']) {
            $this->error = '請新增購買商品';
            return false;
        }
        $result = $this->validateData($data);
        if (!$result) {
            return false;
        }
        // 開啟事務
        $this->startTrans();
        try {
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            $data['app_id'] = self::$app_id;
            $this->save($data);
            // 購買商品
            $this->addProduct($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        if (!isset($data['limit_product']) || !$data['limit_product']) {
            $this->error = '請新增購買商品';
            return false;
        }
        $result = $this->validateData($data, 'edit');
        if (!$result) {
            return false;
        }
        // 開啟事務
        $this->startTrans();
        try {
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            $this->save($data);
            // 購買商品
            $this->addProduct($data, true);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 新增購買商品
     */
    private function addProduct($data, $isUpdate = false)
    {
        // 更新模式: 先刪除所有規格
        $model = new BuyActivityProduct();
        $isUpdate && $model->where('buy_id', '=', $this['buy_id'])->delete();
        $addProduct = [];
        foreach ($data['limit_product'] as $product) {
            $addProduct[] = [
                'buy_id' => $this['buy_id'],
                'product_name' => $product['product_name'],
                'product_id' => $product['product_id'],
                'product_num' => $product['product_num'],
                'app_id' => self::$app_id
            ];
        }
        $model->saveAll($addProduct);
    }

    /**
     * 驗證
     */
    public function validateData($data, $scene = 'add')
    {
        //查詢商品是否存在其他活動中
        $model = $this->alias('b');
        if ($scene == 'edit') {
            $model = $model->where('b.buy_id', '<>', $this['buy_id']);
        }
        $allProduct = $model->join('buy_activity_product p', 'p.buy_id=b.buy_id')
            ->where('b.is_delete', '=', 0)
            ->where('b.status', '=', 1)
            ->column('product_id');
        $limitProductID = [];
        foreach ($data['limit_product'] as $item) {
            $limitProductID[] = $item['product_id'];
        }
        $sameProduct = array_intersect($limitProductID, $allProduct);
        if ($sameProduct) {
            $product_name = (new ProductModel())->where('product_id', 'in', $sameProduct)->value('product_name');
            $this->error = $product_name . '已參與活動';
            return false;
        }
        return true;
    }

    /**
     * 軟刪除
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }
}