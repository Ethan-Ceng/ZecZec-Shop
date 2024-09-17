<?php

namespace app\common\model\shop;

use app\common\model\BaseModel;

/**
 * 商家使用者模型
 */
class User extends BaseModel
{
    protected $name = 'shop_user';
    protected $pk = 'shop_user_id';

    /**
     * 關聯應用表
     */
    public function app()
    {
        return $this->belongsTo('app\\common\\model\\app\\App', 'app_id', 'app_id');
    }

    /**
     * 關聯使用者角色表表
     */
    public function role()
    {
        return $this->belongsToMany('app\\common\\model\\auth\\Role', 'app\\common\\model\\auth\\UserRole');
    }

    public function userRole()
    {
        return $this->hasMany('app\\common\\model\\shop\\UserRole', 'shop_user_id', 'shop_user_id');
    }

    /**
     * 驗證使用者名稱是否重複
     */
    public static function checkExist($user_name)
    {
        return !!static::withoutGlobalScope()
            ->where('user_name', '=', $user_name)
            ->value('shop_user_id');
    }

    /**
     * 商家使用者詳情
     */
    public static function detail($where, $with = [])
    {
        !is_array($where) && $where = ['shop_user_id' => (int)$where];
        return (new static())->where(array_merge(['is_delete' => 0], $where))->with($with)->find();
    }
}