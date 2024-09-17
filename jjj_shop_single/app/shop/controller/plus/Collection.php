<?php

namespace app\shop\controller\plus;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 引導收藏控制器
 */
class Collection extends Controller
{
    /**
     * 引導收藏設定
     */
    public function index()
    {
        $key = 'collection';
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