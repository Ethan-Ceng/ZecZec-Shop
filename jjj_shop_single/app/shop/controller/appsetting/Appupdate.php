<?php

namespace app\shop\controller\appsetting;

use app\shop\controller\Controller;
use app\shop\model\app\AppUpdate as AppUpdateModel;

/**
 * 升級管理
 */
class Appupdate extends Controller
{
    /**
     * 列表
     */
    public function index()
    {
        $model = new AppUpdateModel();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增
     */
    public function add()
    {
        $model = new AppUpdateModel();
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }

    /**
     * 編輯
     */
    public function edit($update_id)
    {
        $model = AppUpdateModel::detail($update_id);
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError()?:'更新失敗');
    }

    /**
     * 刪除
     */
    public function delete($update_id)
    {
        $model = AppUpdateModel::detail($update_id);
        if ($model->setDelete()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError('刪除失敗');
    }
}
