<?php

namespace app\api\model\store;

use app\common\exception\BaseException;
use app\common\model\store\Clerk as ClerkModel;

/**
 * 商家門店店員模型
 */
class Clerk extends ClerkModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * 店員詳情
     */
    public static function detail($where)
    {
        $model = parent::detail($where);
        if (!$model) {
            throw new BaseException(['msg' => '未找到店員資訊']);
        }
        return $model;
    }

    /**
     * 驗證使用者是否為核銷員
     */
    public function checkUser($store_id)
    {
        if ($this['is_delete']) {
            $this->error = '未找到店員資訊';
            return false;
        }
        if ($this['store_id'] != $store_id) {
            $this->error = '當前店員不屬於該門店，沒有核銷許可權';
            return false;
        }
        if (!$this['status']) {
            $this->error = '當前店員狀態已被停用';
            return false;
        }
        return true;
    }

}