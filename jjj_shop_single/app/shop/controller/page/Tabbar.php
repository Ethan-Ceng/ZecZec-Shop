<?php

namespace app\shop\controller\page;

use app\common\enum\settings\SettingEnum;
use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 交易設定控制器
 */
class Tabbar extends Controller
{
    /**
     * 交易設定
     */
    public function index()
    {
        $vars = SettingModel::getItem(SettingEnum::NAV);
        return $this->renderSuccess('', compact('vars'));
    }

    /**
     * 獲取交易設定
     */
    public function edit()
    {
        $model = new SettingModel;
        $data = $this->postData();
        if ($model->edit('nav', $data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }
}
