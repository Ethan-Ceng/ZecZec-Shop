<?php

namespace app\shop\controller\plus\card;

use app\shop\controller\Controller;
use app\shop\model\plus\card\Category as CategoryModel;

/**
 * 分類控制器
 */
class Category extends Controller
{
    /**
     * 獲取分類
     */
    public function index()
    {
        // 文章分類
        $model = new CategoryModel;
        $category = $model->getAll();
        return $this->renderSuccess('', compact('category'));
    }

    /**
     * 新增文章分類
     */
    public function add()
    {
        $model = new CategoryModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 編輯文章分類
     */
    public function edit($category_id)
    {
        // 分類詳情
        $model = CategoryModel::detail($category_id);
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 刪除文章分類
     */
    public function delete($category_id)
    {
        $model = CategoryModel::detail($category_id);
        if (!$model->remove()) {
            return $this->renderError('該分類下存在卡券，刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }
}