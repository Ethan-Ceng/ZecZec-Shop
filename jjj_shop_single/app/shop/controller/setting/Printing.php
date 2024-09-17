<?php

namespace app\shop\controller\setting;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;
use app\shop\model\settings\Printer as PrinterModel;

/**
 * 列印設定
 */
class Printing extends Controller
{
    /**
     * 列印設定
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new SettingModel;
        if ($model->edit('printer', $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取列印配置
     */
    public function fetchData()
    {
        // 獲取印表機列表
        $vars['printerList'] = PrinterModel::getAll()->toArray();
        $vars['values'] = SettingModel::getItem( 'printer');
        $vars['values']['is_open'] = intval($vars['values']['is_open']);
        return $this->renderSuccess('', compact('vars'));
    }


}
