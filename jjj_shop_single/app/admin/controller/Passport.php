<?php

namespace app\admin\controller;

use app\admin\model\admin\User as UserModel;
use think\facade\Cache;

class Passport extends Controller
{
    /**
     * 超管後臺登入
     */
    public function login()
    {
        $model = new UserModel;
        if ($user = $model->login($this->postData())) {
            return $this->renderSuccess('登入成功', [
                'user_name' => $user['user_name'],
                'token' => $user['token']
            ]);
        }
        return $this->renderError('使用者名稱或者密碼錯誤！');
    }

    /**
     * 退出登入
     */
    public function logout()
    {
        $token = Request()->header('token');
        Cache::delete('admin_token_' . $token);
        return $this->renderSuccess('退出成功');
    }
}