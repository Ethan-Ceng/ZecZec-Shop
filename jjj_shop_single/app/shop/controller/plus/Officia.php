<?php

namespace app\shop\controller\plus;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * Class Officia
 * 公眾號關注控制器
 * @package app\shop\controller\plus\officia
 */
class Officia extends Controller
{
    /**
     *公眾號關注
     */
    public function index()
    {
        $key = 'officia';
        if($this->request->isGet()){
            $vars['values'] = SettingModel::getItem($key);
            return $this->renderSuccess('', compact('vars'));
        }

        $model = new SettingModel;
        if ($model->edit($key, $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError()?:'操作失敗');
    }

}