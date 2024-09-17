<?php
namespace app\common\model\admin;

use app\common\model\BaseModel;

/**
 * 超管後臺使用者模型
 */
class User extends BaseModel
{
    protected $name = 'admin_user';
    protected $pk = 'admin_user_id';
}