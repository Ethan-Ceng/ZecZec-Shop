<?php

namespace app\shop\model\shop;

use app\common\model\shop\LoginLog as LoginLogModel;
use app\common\model\shop\User as UserModel;
use think\facade\Cache;

/**
 * 後臺管理員登入模型
 */
class User extends UserModel
{
    /**
     *檢查登入
     */
    public function checkLogin($user)
    {
        $where['user_name'] = $user['username'];
        $where['password'] = $user['password'];
        $where['is_delete'] = 0;

        if (!$user = $this->where($where)->with(['app'])->find()) {
            return false;
        }
        if (empty($user['app'])) {
            $this->error = '登入失敗, 未找到應用資訊';
            return false;
        }
        if ($user['app']['is_delete']) {
            $this->error = '登入失敗, 當前應用已刪除';
            return false;
        }
        if ($user['app']['is_recycle']) {
            $this->error = '登入失敗, 當前應用已停用';
            return false;
        }
        if ($user['app']['expire_time'] != 0 && $user['app']['expire_time'] < time()) {
            $this->error = '登入失敗, 當前應用已過期，請聯絡平臺續費';
            return false;
        }
        // 儲存登入狀態
        $user['token'] = signToken($user['shop_user_id'], 'shop');
        Cache::tag('cache')->set('shop_token_' . $user['token'], $user['shop_user_id'], 86400 * 30);
        // 寫入登入日誌
        LoginLogModel::add($where['user_name'], \request()->ip(), '登入成功', $user['app']['app_id']);
        return $user;
    }


    /*
    * 修改密碼
    */
    public function editPass($data, $user)
    {
        $user_info = User::detail($user['shop_user_id']);
        if ($data['password'] != $data['confirmPass']) {
            $this->error = '密碼錯誤';
            return false;
        }
        if ($user_info['password'] != salt_hash($data['oldpass'])) {
            $this->error = '兩次密碼不相同';
            return false;
        }
        $date['password'] = salt_hash($data['password']);
        $user_info->save($date);
        return true;
    }

    /**
     * 獲取使用者資訊
     */
    public static function getUser($data)
    {
        return (new static())->where(['shop_user_id' => $data['uid']])->with(['app'])->find();
    }

}