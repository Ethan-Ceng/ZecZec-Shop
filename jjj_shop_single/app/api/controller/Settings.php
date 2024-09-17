<?php

namespace app\api\controller;

use app\api\model\settings\Setting as SettingModel;
use app\common\model\app\AppOpen as AppOpenModel;
use app\common\model\settings\Region as RegionModel;

/**
 * 頁面控制器
 */
class Settings extends Controller
{

    // app分享
    public function appShare()
    {
        // 分享設定
        $appshare = SettingModel::getItem('appshare');
        // logo
        $detail = AppOpenModel::detail();
        $logo = '';
        if($detail){
            $logo = $detail['logo'];
        }
        return $this->renderSuccess('', compact('appshare', 'logo'));
    }

    /**
     * 獲取省市區
     */
    public function getRegion(){
        $regionData = RegionModel::getRegionForApi();
        return $this->renderSuccess('', compact('regionData'));
    }
}
