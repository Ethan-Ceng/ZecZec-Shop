<?php

namespace app\shop\controller\product;

use app\shop\controller\Controller;
use app\shop\model\product\Category as CategoryModel;

/**
 * 商品分類
 */
class Category extends Controller
{
    /**
     * 商品分類列表
     */
    public function index()
    {
        $model = new CategoryModel;
        $list = $model->getCacheTree();
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 刪除商品分類
     */
    public function delete($category_id)
    {
        $model = CategoryModel::detail($category_id);
        if ($model->remove($category_id)) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?:'刪除失敗');
    }

    /**
     * 新增商品分類
     */
    public function add()
    {
        $model = new CategoryModel;
        // 新增記錄
        if ($model->add($this->request->post())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?:'新增失敗');
    }

    /**
     * 編輯商品分類
     */
    public function edit($category_id)
    {
        // 模板詳情
        $model = CategoryModel::detail($category_id);
        // 更新記錄
        if ($model->edit($this->request->post())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 得到修改圖片
     */
    public function image($category_id)
    {
        $model = new CategoryModel;
        $detail = $model->detailWithImage(['category_id' => $category_id]);
        return $this->renderSuccess('', compact('detail'));
    }

    /**
     * 設定狀態
     */
    public function set($category_id)
    {
        // 詳情
        $model = CategoryModel::detail($category_id);
        // 更新記錄
        if ($model->setStatus($this->request->post())) {
            return $this->renderSuccess('設定成功');
        }
        return $this->renderError($model->getError() ?: '設定失敗');
    }
}
