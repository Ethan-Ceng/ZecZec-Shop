<?php

namespace app\api\controller;

use app\api\model\user\User as UserModel;
use app\api\model\App as AppModel;
use app\common\exception\BaseException;
use app\common\library\easywechat\AppMp;
use app\JjjController;
use think\facade\Env;
use think\facade\Cache;

/**
 * API控制器基類
 */
class Controller extends JjjController
{

    // app_id
    protected $app_id;

    /**
     * 後臺初始化
     */
    public function initialize()
    {
        // 當前小程式id
        $this->app_id = $this->getAppId();
        // 驗證當前小程式狀態
        $this->checkWxapp();
    }

    /**
     * 獲取當前應用ID
     */
    private function getAppId()
    {
        if (!$app_id = $this->request->param('app_id')) {
            throw new BaseException(['msg' => '缺少必要的引數：app_id']);
        }
        return $app_id;
    }

    /**
     * 驗證當前小程式狀態
     */
    private function checkWxapp()
    {
        $app = AppModel::detail($this->app_id);
        if (empty($app)) {
            throw new BaseException(['msg' => '當前應用資訊不存在']);
        }
        if ($app['is_recycle'] || $app['is_delete']) {
            throw new BaseException(['msg' => '當前應用已刪除']);
        }
        if ($app['expire_time'] != 0 && $app['expire_time'] < time()) {
            throw new BaseException(['msg' => '當前應用已過期']);
        }
    }

    /**
     * 獲取當前使用者資訊
     */
    protected function getUser($is_force = true)
    {
        $token = $this->request->param('token') ? $this->request->param('token') : $this->request->header('token');

        if (!$token) {
            if ($is_force) {
                throw new BaseException(['msg' => '缺少必要的引數：token', 'code' => 401]);
            }
            return false;
        }
        $tokenStatus = Cache::get($token);
        if ($is_force && !$tokenStatus) {
            throw new BaseException(['msg' => 'token失效', 'code' => -1]);
        }
        if (!$user = UserModel::getUser($token)) {
            if ($is_force) {
                throw new BaseException(['msg' => '沒有找到使用者資訊', 'code' => -1]);
            }
            return false;
        }
        if ($user['is_delete'] == 1) {
            Cache::delete($token);
            throw new BaseException(['msg' => '沒有找到使用者資訊', 'code' => -2]);
        }
        return $user;
    }

    protected function getShareParams($url, $title = '', $desc = '', $link = '', $imgUrl = '')
    {
        $signPackage = '';
        $shareParams = '';
        if (Env::get('APP_DEBUG')) {
            return [
                'signPackage' => $signPackage,
                'shareParams' => $shareParams
            ];
        }
        if ($url != '') {
            $app = AppMp::getApp($this->app_id);
            $utils = $app->getUtils();
            $signPackage = $utils->buildJsSdkConfig(
                url: $url,
                jsApiList: ['updateAppMessageShareData', 'updateTimelineShareData'],
                openTagList: [],
                debug: false,
            );
            $shareParams = [
                'title' => $title,
                'desc' => $desc,
                'link' => $link,
                'imgUrl' => $imgUrl,
            ];
            $signPackage = json_encode($signPackage);
        }
        return [
            'signPackage' => $signPackage,
            'shareParams' => $shareParams
        ];
    }

    protected function getScanParams($url)
    {
        $signPackage = '';
        if (Env::get('APP_DEBUG')) {
            return [
                'signPackage' => $signPackage
            ];
        }
        if ($url != '') {
            $app = AppMp::getApp($this->app_id);
            $utils = $app->getUtils();
            $signPackage = $utils->buildJsSdkConfig(
                url: $url,
                jsApiList: ['scanQRCode'],
                openTagList: [],
                debug: false,
            );
            $signPackage = json_encode($signPackage);
        }
        return [
            'signPackage' => $signPackage
        ];
    }

    protected function getMessageParams($url)
    {
        $signPackage = '';
        if (Env::get('APP_DEBUG')) {
            return [
                'signPackage' => $signPackage
            ];
        }
        if ($url != '') {
            $app = AppMp::getApp($this->app_id);
            $utils = $app->getUtils();
            $signPackage = $utils->buildJsSdkConfig(
                url: $url,
                jsApiList: ['updateAppMessageShareData', 'updateTimelineShareData'],
                openTagList: ['wx-open-subscribe'],
                debug: false,
            );
        }
        return [
            'signPackage' => $signPackage
        ];
    }
}
