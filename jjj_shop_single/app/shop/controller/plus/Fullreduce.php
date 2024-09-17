<?php

namespace app\shop\controller\plus;

use app\shop\controller\Controller;
use app\shop\model\plus\product\Reduce as HalfModel;
use app\shop\model\product\Category as CategoryModel;
use app\shop\model\shop\FullReduce as FullReduceModel;

/**
 * 滿減
 */
class Fullreduce extends Controller
{
    /**
     * 會員等級列表
     */
    public function index()
    {
        $model = new FullReduceModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增等級
     */
    public function add()
    {
        $model = new FullReduceModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }

    /**
     * 編輯會員等級
     */
    public function edit($fullreduce_id)
    {
        $model = FullReduceModel::detail($fullreduce_id);
        // 修改記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess();
        }
        return $this->renderError();
    }

    /**
     * 刪除會員等級
     */
    public function delete($fullreduce_id)
    {
        // 會員等級詳情
        $model = FullReduceModel::detail($fullreduce_id);
        if ($model->setDelete()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError('刪除失敗');
    }

    /**
     *商品推薦
     */
    public function product()
    {
        $model = new HalfModel;
        $list = $model->getList($this->postData());
        // 商品分類
        $category = CategoryModel::getCacheTree();
        return $this->renderSuccess('', compact('list', 'category'));
    }

    /**
     *商品推薦
     */
    public function editProduct($product_id)
    {
        $model = new HalfModel;
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError()?:'修改失敗');
    }
}