<?php

namespace app\shop\controller\setting;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 協議控制器
 */
class Protocol extends Controller
{
    /**
     * 協議設定
     */
    public function index()
    {
        if ($this->request->isGet()) {
            return $this->fetchData();
        }
        $model = new SettingModel;
        $data = $this->postData();
        $editData[$data['type']] = $data['value'];
        if ($model->edit($data['type'], $editData)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取協議設定
     */
    public function fetchData()
    {
        $vars['service'] = SettingModel::getItem('service');
        $vars['privacy'] = SettingModel::getItem('privacy');
        return $this->renderSuccess('', compact('vars'));
    }

}
