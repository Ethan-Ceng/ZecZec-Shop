<?php

namespace app\api\model\user;

use app\common\model\settings\Region;
use app\common\model\user\UserAddress as UserAddressModel;

/**
 * 使用者收貨地址模型
 */
class UserAddress extends UserAddressModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * 獲取列表
     */
    public function getList($user_id)
    {
        return $this->where('user_id', '=', $user_id)->select();
    }

    /**
     * 新增收貨地址
     */
    public function add($user, $data)
    {
        // 新增收貨地址
        $this->startTrans();
        try {
            $address_id = $this->insertGetId([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'province_id' => $data['province_id'],
                'city_id' => $data['city_id'],
                'region_id' => $data['region_id'],
                'detail' => $data['detail'],
                'user_id' => $user['user_id'],
                'app_id' => self::$app_id
            ]);
            // 設為預設收貨地址
            if (isset($data['is_default']) && $data['is_default'] == 1) {
                $user->save(['address_id' => $address_id]);
            } else {
                !$user['address_id'] && $user->save(['address_id' => $address_id]);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 編輯收貨地址
     */
    public function edit($user, $data)
    {
        // 新增收貨地址
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $user->save(['address_id' => $this['address_id']]);
        }
        return $this->save([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'province_id' => $data['province_id'],
                'city_id' => $data['city_id'],
                'region_id' => $data['region_id'],
                'detail' => $data['detail'],
            ]) !== false;
    }

    /**
     * 設為預設收貨地址
     */
    public function setDefault($user)
    {
        // 設為預設地址
        return $user->save(['address_id' => $this['address_id']]);
    }

    /**
     * 刪除收貨地址
     * @return bool
     * @throws \Exception
     */
    public function remove($user)
    {
        // 查詢當前是否為預設地址
        $user['address_id'] == $this['address_id'] && $user->save(['address_id' => 0]);
        return $this->delete();
    }

    /**
     * 收貨地址詳情
     */
    public static function detail($user_id, $address_id)
    {
        $where = ['user_id' => $user_id, 'address_id' => $address_id];
        return (new static())->where($where)->find();
    }

}