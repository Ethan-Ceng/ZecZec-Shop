<?php

namespace app\common\model\shop;


use app\common\model\BaseModel;

/**
 * 應用模型
 */
class Role extends BaseModel
{
    protected $name = 'shop_role';
    protected $pk = 'role_id';

    /**
     * 關聯許可權
     */
    public function access()
    {
        return $this->hasMany('RoleAccess', 'role_id', 'role_id');
    }

    /**
     * 獲取詳情
     * @param $where
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detail($role_id)
    {
        return (new static())->with(['access'])->find($role_id);
    }

}
