<?php

namespace app\api\controller;

use app\api\model\page\Page as AppPage;
use app\api\model\settings\Setting as SettingModel;
use app\common\enum\settings\SettingEnum;
use app\common\model\app\AppUpdate as AppUpdateModel;

/**
 * 頁面控制器
 */
class Index extends Controller
{
    /**
     * 首頁
     */
    public function index($page_id = null, $url = '')
    {
        // 頁面元素
        $data = AppPage::getPageData($this->getUser(false), $page_id);
        $data['setting'] = array(
            'collection' => SettingModel::getItem('collection'),
            'officia' => SettingModel::getItem('officia'),
            'homepush' => SettingModel::getItem('homepush')
        );
        // 掃一掃引數
        $data['signPackage'] = $this->getScanParams($url)['signPackage'];
        // 微信公眾號分享引數
        $data['share'] = $this->getShareParams($url, $data['page']['params']['share_title'], $data['page']['params']['share_title'], '/pages/index/index', $data['page']['params']['share_img']);
        return $this->renderSuccess('', $data);
    }

    /**
     * 首頁
     */
    public function diy($page_id = null, $url = '')
    {
        // 頁面元素
        $data = AppPage::getPageData($this->getUser(false), $page_id);
        // 微信公眾號分享引數
        $data['share'] = $this->getShareParams($url, $data['page']['params']['share_title'], $data['page']['params']['share_title'], '/pages/diy-page/diy-page', $data['page']['params']['share_img']);
        return $this->renderSuccess('', $data);
    }

    // 公眾號客服
    public function mpService()
    {
        $mp_service = SettingModel::getItem('mp_service');
        return $this->renderSuccess('', compact('mp_service'));
    }

    // app更新
    public function update($name, $version, $platform)
    {
        $result = [
            'update' => false,
            'wgtUrl' => '',
            'pkgUrl' => '',
        ];
        try {
            $model = AppUpdateModel::getLast();
            if ($platform == 'android') {
                $compare_version = $model['version_android'];
            } else {
                $compare_version = $model['version_ios'];
            }
            if ($model && str_replace('.', '', $version) < str_replace('.', '', $compare_version)) {
                $currentVersions = explode('.', $version);
                $resultVersions = explode('.', $compare_version);

                if ($currentVersions[0] < $resultVersions[0]) {
                    // 說明有大版本更新
                    $result['update'] = true;
                    $result['pkgUrl'] = $platform == 'android' ? $model['pkg_url_android'] : $model['pkg_url_ios'];
                } else {
                    // 其它情況均認為是小版本更新
                    $result['update'] = true;
                    $result['wgtUrl'] = $model['wgt_url'];
                }
            }
        } catch (\Exception $e) {

        }
        return $this->renderSuccess('', compact('result'));
    }

    public function nav()
    {
        $vars = SettingModel::getItem(SettingEnum::NAV);
        $theme = SettingModel::getItem(SettingEnum::THEME);
        $points_name = SettingModel::getPointsName();
        return $this->renderSuccess('', compact('vars', 'theme', 'points_name'));
    }

    //獲取公眾號訊息簽名
    public function getSignPackage($url = "")
    {
        // 訊息簽名
        $data['signPackage'] = $this->getMessageParams($url)['signPackage'];
        return $this->renderSuccess('', $data);
    }

    /**
     * 使用者註冊登入設定
     */
    public function loginSetting()
    {
        $settingDetail = SettingModel::getItem('store');
        $setting = [
            'name' => $settingDetail['name'],
            'sms_open' => $settingDetail['sms_open'],
            'wx_open' => $settingDetail['wx_open'],
            'mp_open' => $settingDetail['mp_open'],
            'login_logo' => $settingDetail['login_logo'],
            'login_desc' => $settingDetail['login_desc'],
            'wx_phone' => $settingDetail['wx_phone'],
        ];
        return $this->renderSuccess('', compact('setting'));
    }
}
