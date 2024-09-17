<?php

namespace app\shop\controller\product;

use app\shop\model\product\Product as ProductModel;
use app\shop\model\product\Category as CategoryModel;
use app\shop\service\ProductService;
use app\shop\controller\Controller;

/**
 * 商品管理控制器
 */
class Product extends Controller
{
    /**
     * 商品列表(全部)
     */
    public function index()
    {
        // 獲取全部商品列表
        $model = new ProductModel;
        $list = $model->getList(array_merge(['status' => -1], $this->postData()));
        // 商品分類
        $category = CategoryModel::getCacheTree();
        // 數量
        $product_count = [
            'sell' => $model->getCount('sell'),
            'stock' => $model->getCount('stock'),
            'recovery' => $model->getCount('recovery'),
            'over' => $model->getCount('over'),
            'lower' => $model->getCount('lower')
        ];
        return $this->renderSuccess('', compact('list', 'category', 'product_count'));
    }

    /**
     * 商品列表(在售)
     */
    public function lists()
    {
        // 獲取全部商品列表
        $model = new ProductModel;
        $list = $model->getLists($this->postData());
        // 商品分類
        $catgory = CategoryModel::getCacheTree();
        return $this->renderSuccess('', compact('list', 'catgory'));
    }

    /**
     * 新增商品
     */
    public function add($scene = 'add')
    {
        // get請求
        if($this->request->isGet()){
            return $this->getBaseData();
        }
        //post請求
        $data = json_decode($this->postData()['params'], true);
        if($scene == 'copy'){
            unset($data['create_time']);
            unset($data['sku']['product_sku_id']);
            unset($data['sku']['product_id']);
            unset($data['product_sku']['product_sku_id']);
            unset($data['product_sku']['product_id']);
            if($data['spec_type'] == 20){
                foreach ($data['spec_many']['spec_list'] as &$spec){
                    $spec['product_sku_id'] = 0;
                }
            }
            //初始化銷量等資料
            $data['sales_initial'] = 0;
        }

        $model = new ProductModel;
        if (isset($data['product_id'])) {
            unset($data['product_id']);
        }

        if ($model->add($data)) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 獲取基礎資料
     */
    public function getBaseData()
    {
        return $this->renderSuccess('', array_merge(ProductService::getEditData(null, 'add'), []));
    }

    /**
     * 獲取編輯資料
     */
    public function getEditData($product_id, $scene = 'edit')
    {
        $model = ProductModel::detail($product_id);
        return $this->renderSuccess('', array_merge(ProductService::getEditData($model, $scene), compact('model')));
    }

    /**
     * 商品編輯
     */
    public function edit($product_id, $scene = 'edit')
    {
        if($this->request->isGet()){
            $model = ProductModel::detail($product_id);
            return $this->renderSuccess('', array_merge(ProductService::getEditData($model, $scene), compact('model')));
        }
        if ($scene == 'copy') {
            return $this->add($scene);
        }
        // 商品詳情
        $model = ProductModel::detail($product_id);
        // 更新記錄
        if ($model->edit(json_decode($this->postData()['params'], true))) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 修改商品狀態
     */
    public function state($product_id, $state)
    {
        // 商品詳情
        $model = ProductModel::detail($product_id);
        if (!$model->setStatus($state)) {
            return $this->renderError('操作失敗');
        }
        return $this->renderSuccess('操作成功');
    }

    /**
     * 刪除商品
     */
    public function delete($product_id)
    {
        // 商品詳情
        $model = ProductModel::detail($product_id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

}
