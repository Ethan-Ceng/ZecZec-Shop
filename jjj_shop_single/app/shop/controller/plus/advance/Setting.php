<?php

namespace app\shop\controller\plus\advance;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 預售活動設定
 */
class Setting extends Controller
{
    /**
     *獲取設定
     */
    public function getSetting()
    {
        $vars['values'] = SettingModel::getItem('advance');
        return $this->renderSuccess('', compact('vars'));
    }

    /**
     * 預售設定
     */
    public function index()
    {
        if ($this->request->isGet()) {
            return $this->getSetting();
        }
        $model = new SettingModel;
        $data = $this->request->param();
        if ($model->edit('advance', $data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError('操作失敗');
    }
}