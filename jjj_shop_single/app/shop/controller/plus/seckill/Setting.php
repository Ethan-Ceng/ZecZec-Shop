<?php

namespace app\shop\controller\plus\seckill;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 整點秒殺設定
 */
class Setting extends Controller
{
    /**
     *獲取設定
     */
    public function getSetting()
    {
        $vars['values'] = SettingModel::getItem('seckill');
        return $this->renderSuccess('', compact('vars'));
    }

    /**
     * 秒殺設定
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->getSetting();
        }
        $model = new SettingModel;
        $data = $this->request->param();
        if ($model->edit('seckill', $data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError('操作失敗');
    }
}