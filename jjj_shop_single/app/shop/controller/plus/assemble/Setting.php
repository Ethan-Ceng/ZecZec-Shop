<?php

namespace app\shop\controller\plus\assemble;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 整點秒殺設定
 */
class Setting extends Controller
{
    /**
     *獲取拼團設定
     */
    public function getSetting()
    {
        $vars['values'] = SettingModel::getItem('assemble');
        return $this->renderSuccess('', compact('vars'));
    }

    /**
     * 拼團設定
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->getSetting();
        }
        $model = new SettingModel;
        $data = $this->request->param();
        if ($model->edit('assemble', $data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError('操作失敗');
    }
}