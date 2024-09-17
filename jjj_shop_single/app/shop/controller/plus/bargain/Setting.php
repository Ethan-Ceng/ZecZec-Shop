<?php

namespace app\shop\controller\plus\bargain;


use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 砍價設定
 */
class Setting extends Controller
{

    /**
     * 拼團設定
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->getSetting();
        }
        $model = new SettingModel;
        if ($model->edit('bargain', $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError('操作失敗');
    }
    /**
     * 設定開啟砍價
     */
    public function getSetting()
    {
        $vars['values'] = SettingModel::getItem('bargain');
        return $this->renderSuccess('', compact('vars'));
    }

}