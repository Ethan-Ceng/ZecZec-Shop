<?php

namespace app\admin\controller;

use app\admin\model\settings\Setting as SettingModel;
/**
 * 系統配置控制器
 */
class Setting extends Controller
{
    /**
     * 系統配置
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new SettingModel;
        if ($model->add($this->request->param())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取系統配置
     */
    public function fetchData()
    {
        $vars['values'] = SettingModel::getSysConfig();
        return $this->renderSuccess('', compact('vars'));
    }

}
