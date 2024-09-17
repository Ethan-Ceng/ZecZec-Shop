<?php

namespace app\shop\controller\setting;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;
use app\common\enum\settings\DeliveryTypeEnum;

/**
 * 商城設定控制器
 */
class Store extends Controller
{
    /**
     * 商城設定
     */
    public function index()
    {
        if ($this->request->isGet()) {
            return $this->fetchData();
        }
        $model = new SettingModel;
        $data = $this->request->param();
        $arr = [
            'name' => $data['name'],
            'delivery_type' => $data['checkedCities'],
            'kuaidi100' => ['customer' => $data['customer'], 'secret' => $data['secret'], 'key' => $data['key']],
            'is_get_log' => $data['is_get_log'],
            'is_send_wx' => $data['is_send_wx'],
            'logoUrl' => $data['logoUrl'],
            'sms_open' => $data['sms_open'],
            'wx_open' => $data['wx_open'],
            'wx_phone' => $data['wx_phone'],
            'mp_open' => $data['mp_open'],
            'avatarUrl' => $data['avatarUrl'],
            'tx_key' => $data['tx_key'],
            'login_logo' => $data['login_logo'],
            'login_desc' => $data['login_desc'],
            'user_name' => $data['user_name'],
            'mp_phone' => $data['mp_phone'],
        ];
        if ($model->edit('store', $arr)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取商城配置
     */
    public function fetchData()
    {
        $vars['values'] = SettingModel::getItem('store');
        $all_type = DeliveryTypeEnum::data();
        return $this->renderSuccess('', compact('vars', 'all_type'));
    }

}
