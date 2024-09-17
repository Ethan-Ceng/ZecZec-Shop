<?php

namespace app\shop\model\auth;

use app\common\model\shop\RoleAccess as RoleAccessModel;


/**
 * 角色模型
 */
class RoleAccess extends RoleAccessModel
{
    /**
     * 獲取指定角色的所有許可權id
     * @param int|array $role_id 角色id (支援陣列)
     */
    public static function getAccessIds($role_id)
    {
        $roleIds = is_array($role_id) ? $role_id : [(int)$role_id];
        return (new self)->where('role_id', 'in', $roleIds)->column('access_id');
    }
}
