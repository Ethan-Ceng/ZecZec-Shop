<?php

namespace app\admin\model;

use app\common\exception\BaseException;
use app\common\model\shop\User as ShopModel;

class Shop extends ShopModel
{
    /**
     * 新增商家使用者記錄
     */
    public function add($app_id, $data)
    {
        if (self::checkExist($data['user_name'])) {
            $this->error = '商家使用者名稱已存在';
            return false;
        }
        return $this->save([
            'user_name' => $data['user_name'],
            'password' => salt_hash($data['password']),
            'app_id' => $app_id,
            'is_super' => 1
        ]);
    }
}