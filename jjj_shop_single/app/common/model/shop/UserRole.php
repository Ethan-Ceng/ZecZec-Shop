<?php

namespace app\common\model\shop;


use app\common\model\BaseModel;

/**
 * 應用模型
 */
class UserRole extends BaseModel
{
    protected $name = 'shop_user_role';
    protected $pk = 'id';

    /**
     * 關聯角色
     * @return \think\model\relation\BelongsTo
     */
    public function role(){
        return $this->belongsTo('Role', 'role_id', 'role_id');
    }
}
