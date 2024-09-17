<?php

namespace app\shop\controller\plus\card;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 卡券設定控制器
 */
class Setting extends Controller
{
    /**
     * 卡券設定
     */
    public function index()
    {
        if ($this->request->isGet()) {
            return $this->fetchData();
        }
        $model = new SettingModel;
        $data = $this->request->param();
        $arr = [
            'image' => $data['image'],
        ];
        if ($model->edit('card', $arr)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取卡券配置
     */
    public function fetchData()
    {
        $vars['values'] = SettingModel::getItem('card');
        return $this->renderSuccess('', compact('vars'));
    }

}
