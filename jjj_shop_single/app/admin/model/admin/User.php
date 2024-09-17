<?php

namespace app\admin\model\admin;

use app\common\model\admin\User as UserModel;
use think\facade\Cache;

/**
 * 超管後臺使用者模型
 */
class User extends UserModel
{
    /**
     * 超管後臺使用者登入
     */
    public function login($data)
    {
        // 驗證使用者名稱密碼是否正確
        if (!$user = self::where([
            'user_name' => $data['username'],
            'password' => salt_hash($data['password'])
        ])->find()
        ) {
            $this->error = '登入失敗, 使用者名稱或密碼錯誤';
            return false;
        }
        // 儲存登入狀態
        $user['token'] = signToken($user['admin_user_id'], 'admin');
        Cache::tag('cache')->set('admin_token_' . $user['token'], $user['admin_user_id'], 86400 * 30);
        return $user;
    }

    /**
     * 超管使用者資訊
     */
    public static function detail($admin_user_id)
    {
        return (new static())->find($admin_user_id);
    }

    /**
     * 更新當前管理員資訊
     */
    public function renew($data)
    {
        if ($data['pass'] !== $data['checkPass']) {
            $this->error = '確認密碼不正確';
            return false;
        }
        return $this->save([
            'password' => salt_hash($data['pass']),
        ]);
    }

    /**
     * 獲取使用者資訊
     */
    public static function getUser($data)
    {
        return (new static())->where(['admin_user_id' => $data['uid']])->find();
    }
}