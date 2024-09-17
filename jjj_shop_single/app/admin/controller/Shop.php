<?php

namespace app\admin\controller;

use app\admin\model\app\App as AppModel;
use app\admin\model\Shop as ShopModel;

class Shop extends Controller
{
    /**
     * 小程式列表
     */
    public function index()
    {
        $model = new AppModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增應用
     */
    public function add()
    {
        $model = new AppModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 新增應用
     */
    public function edit($app_id)
    {
        $model = AppModel::detail($app_id);
        // 新增記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     * 刪除小程式
     */
    public function delete($app_id)
    {
        // 小程式詳情
        $model = AppModel::detail($app_id);
        if (!$model->setDelete()) {
            return $this->renderError('操作失敗');
        }
        return $this->renderSuccess('操作成功');
    }

    /*
     *啟用停用
    */
    public function updateStatus($app_id)
    {
        $model = AppModel::detail($app_id);
        if ($model->updateStatus()) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError('操作失敗');
    }

    /*
     *啟用停用
    */
    public function updateWxStatus($app_id)
    {
        $model = AppModel::detail($app_id);
        if (!$model->updateWxStatus()) {
            return $this->renderError('操作失敗');
        }
        return $this->renderSuccess('操作成功');
    }
}