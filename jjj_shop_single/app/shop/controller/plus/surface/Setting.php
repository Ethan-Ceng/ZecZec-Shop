<?php

namespace app\shop\controller\plus\surface;

use app\shop\controller\Controller;
use app\shop\model\settings\DeliverySetting as DeliverySettingModel;
use app\shop\model\settings\Express as ExpressModel;

/**
 * 分類控制器
 */
class Setting extends Controller
{
    /**
     * 獲取列表
     */
    public function index()
    {
        // 文章分類
        $model = new DeliverySettingModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增
     */
    public function add()
    {
        if ($this->request->isGet()) {
            // 物流公司列表
            $model = new ExpressModel();
            $expressList = $model->getAll();
            return $this->renderSuccess('', compact('expressList'));
        }
        $model = new DeliverySettingModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 編輯
     */
    public function edit($setting_id)
    {
        // 詳情
        $model = DeliverySettingModel::detail($setting_id);
        if ($this->request->isGet()) {
            // 物流公司列表
            $ExpressModel = new ExpressModel();
            $expressList = $ExpressModel->getAll();
            return $this->renderSuccess('', compact('model', 'expressList'));
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
    public function delete($setting_id)
    {
        $model = DeliverySettingModel::detail($setting_id);
        if (!$model->remove()) {
            return $this->renderError($model->getError() ?: '刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }
}