<?php

namespace app\shop\controller\plus;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 首頁推送控制器
 */
class Homepush extends Controller
{

    /**
     *首頁推送配置
     */
    public function index()
    {
        $key = 'homepush';
        if($this->request->isGet()){
            $vars['values'] = SettingModel::getItem($key);
            return $this->renderSuccess('', compact('vars'));
        }

        $model = new SettingModel;
        if ($model->edit($key, $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

}