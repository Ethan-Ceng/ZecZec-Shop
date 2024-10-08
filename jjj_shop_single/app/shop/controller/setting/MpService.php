<?php

namespace app\shop\controller\setting;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 公眾號訊息設定控制器
 */
class MpService extends Controller
{
    /**
     * 公眾號訊息設定
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new SettingModel;
        $data = $this->request->param();
        $arr = [
            'qq' => $data['qq'],
            'wechat' => $data['wechat'],
            'mp_image' => $data['mp_image'],
        ];
        if ($model->edit('mp_service', $arr)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取商城配置
     */
    public function fetchData()
    {
        $vars['values'] = SettingModel::getItem('mp_service');
        return $this->renderSuccess('', compact('vars'));
    }

}
