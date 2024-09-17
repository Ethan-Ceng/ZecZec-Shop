<?php

namespace app\shop\controller\appsetting;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;
use app\common\enum\settings\SettingEnum;
/**
 * 支付寶支付設定
 */
class Apph5 extends Controller
{
    /**
     * 支付寶支付設定
     */
    public function pay()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new SettingModel;
        if ($model->edit(SettingEnum::H5ALIPAY, $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取支付寶支付
     */
    public function fetchData()
    {
        $data = SettingModel::getItem(SettingEnum::H5ALIPAY);
        return $this->renderSuccess('', compact('data'));
    }

}
