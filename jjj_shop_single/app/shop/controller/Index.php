<?php

namespace app\shop\controller;

use app\common\model\settings\Setting as SettingModel;
use app\shop\service\ShopService;

/**
 * 後臺首頁控制器
 */
class Index extends Controller
{
    /**
     * 後臺首頁
     */
    public function index()
    {
        $service = new ShopService;
        return $this->renderSuccess('', ['data' => $service->getHomeData($this->postData())]);
    }

    /**
     * 登入資料
     */
    public function base()
    {
        $config = SettingModel::getSysConfig();
        $settings = [
            'shop_name' => $config['shop_name'],
            'shop_bg_img' => $config['shop_bg_img']
        ];
        return $this->renderSuccess('', compact('settings'));
    }
}