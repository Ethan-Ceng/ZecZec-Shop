<?php

namespace app\common\model\shop;

use app\common\model\BaseModel;

/**
 * 登入日誌模型
 */
class OptLog extends BaseModel
{
    protected $name = 'shop_opt_log';
    protected $pk = 'opt_log_id';

    /**
     * 關聯使用者表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\shop\\User', 'shop_user_id', 'shop_user_id');
    }
}