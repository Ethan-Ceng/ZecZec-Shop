<?php

namespace app\shop\controller;

use app\shop\model\settings\Setting as SettingModel;
use app\shop\model\shop\User;
use think\facade\Cache;

/**
 * 商戶認證
 */
class Passport extends Controller
{
    /**
     * 商戶後臺登入
     */
    public function login()
    {
        $user = $this->postData();
        $user['password'] = salt_hash($user['password']);
        $model = new User();
        if ($userInfo = $model->checkLogin($user)) {
            // 商城名稱
            $setting = SettingModel::getItem('store', $userInfo['app']['app_id']);
            //當前系統版本
            $version = get_version();
            return $this->renderSuccess('登入成功', [
                'app_id' => $userInfo['app']['app_id'],
                'user_name' => $userInfo['user_name'],
                'token' => $userInfo['token'],
                'shop_name' => $setting['name'],
                'version' => $version,
                'logoUrl' => $setting['logoUrl']
            ]);
        }
        return $this->renderError($model->getError() ?: '登入失敗');
    }

    /**
     * 退出登入
     */
    public function logout()
    {
        $token = Request()->header('token');
        Cache::delete('shop_token_' . $token);
        return $this->renderSuccess('退出成功');
    }

    /*
   * 修改密碼
   */
    public function editPass()
    {
        $model = new User();
        if ($model->editPass($this->postData(), $this->store['user'])) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     * 商戶後臺登入
     */
    public function saasLogin()
    {
        $store = $this->store;
        if ($store != null) {
            return $this->renderSuccess('登入成功', $store['user']['user_name']);
        }
        return $this->renderError('自動登入失敗，請手動輸入');
    }
}
