<?php

namespace app\shop\controller\appsetting;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * app 分享設定
 */
class Appshare extends Controller
{
    /**
     * 儲存設定
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new SettingModel;
        if ($model->edit('appshare', $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取儲存配置
     */
    public function fetchData()
    {
        $data = SettingModel::getItem('appshare');
        return $this->renderSuccess('', compact('data'));
    }

}
