<?php

namespace app\shop\controller\plus\surface;

use app\shop\controller\Controller;
use app\shop\model\settings\DeliveryTemplate as DeliveryTemplateModel;

/**
 * 模板控制器
 */
class Template extends Controller
{
    /**
     * 獲取列表
     */
    public function index()
    {
        // 列表
        $model = new DeliveryTemplateModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增
     */
    public function add()
    {
        $model = new DeliveryTemplateModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 編輯
     */
    public function edit($template_id)
    {
        // 詳情
        $model = DeliveryTemplateModel::detail($template_id);
        if ($this->request->isGet()) {
            return $this->renderSuccess('', compact('model'));
        }
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 刪除
     */
    public function delete($template_id)
    {
        $model = DeliveryTemplateModel::detail($template_id);
        if (!$model->remove()) {
            return $this->renderError($model->getError() ?: '刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

}