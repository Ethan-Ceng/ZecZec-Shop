<?php

namespace app\admin\controller;
use app\common\model\settings\Setting as SettingModel;

/**
 * 後臺首頁
 */
class Index extends Controller
{
    /**
     * 後臺首頁
     */
    public function index()
    {
        $version = get_version();
        return $this->renderSuccess('', compact('version'));
    }

    /**
     * 登入資料
     */
    public function base()
    {
        $config = SettingModel::getSysConfig();
        $settings = [
            'admin_name' => $config['admin_name'],
            'admin_bg_img' => $config['admin_bg_img']
        ];
        return $this->renderSuccess('', compact('settings'));
    }
}