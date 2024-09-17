<?php

namespace app\api\model\plus\agent;

use app\common\model\plus\agent\User as UserModel;

/**
 * 分銷商使用者模型
 */
class User extends UserModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'create_time',
        'update_time',
    ];

    /**
     * 資金凍結
     */
    public function freezeMoney($money)
    {
        return $this->save([
            'money' => $this['money'] - $money,
            'freeze_money' => $this['freeze_money'] + $money,
        ]);
    }


}