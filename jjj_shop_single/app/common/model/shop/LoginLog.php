<?php

namespace app\common\model\shop;

use app\common\model\BaseModel;

/**
 * 登入日誌模型
 */
class LoginLog extends BaseModel
{
    protected $name = 'shop_login_log';
    protected $pk = 'login_log_id';

    /**
     * 新增登入日誌
     */
    public static function add($username, $ip, $result, $app_id)
    {
        $model = new self();
        $model->save([
            'username' => $username,
            'ip' => $ip,
            'result' => $result,
            'app_id' => $app_id
        ]);
    }
}