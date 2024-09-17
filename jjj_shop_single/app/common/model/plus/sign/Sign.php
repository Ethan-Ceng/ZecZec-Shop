<?php

namespace app\common\model\plus\sign;

use app\common\model\BaseModel;

/**
 * 使用者簽到模型
 */
class Sign extends BaseModel
{
    protected $name = 'user_sign';
    protected $pk = 'user_sign_id';

    /**
     * 關聯使用者
     */
    public function user()
    {
        return $this->belongsTo("app\\common\\model\\user\\User", 'user_id', 'user_id');
    }

}
