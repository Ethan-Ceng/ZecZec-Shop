<?php

namespace app\admin\controller\plus;

use app\admin\controller\Controller;
use app\admin\model\plus\Category as CategoryModel;
use app\admin\model\Access as AccessModel;
/**
 * 外掛控制器
 */
class Plus extends Controller
{
    /**
     *外掛列表
     */
    public function index()
    {
        $accessList = CategoryModel::getAll();
        return $this->renderSuccess('', compact('accessList'));
    }

    /**
     *新增外掛
     */
    public function add()
    {
        if($this->request->isGet()){
            //查詢所有外掛
            $accessList = AccessModel::getAllPlus();
            return $this->renderSuccess('', compact('accessList'));
        }
        $model = new AccessModel();
        if ($model->addPlus($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }

    /**
     *刪除外掛
     */
    public function delete($plus_id)
    {
        $model = AccessModel::detail($plus_id);
        if ($model->removePlus()) {
            return $this->renderSuccess(' 刪除成功');
        }
        return $this->renderError($model->getError()?:'刪除失敗');
    }

    /**
     *刪除分類
     */
    public function deleteCategory($plus_category_id)
    {
        $model = CategoryModel::detail($plus_category_id);
        if ($model->remove()) {
            return $this->renderSuccess(' 刪除成功');
        }
        return $this->renderError($model->getError()?:'刪除失敗');
    }
}