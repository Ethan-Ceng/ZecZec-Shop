<?php

namespace app\common\model\user;

use app\common\model\BaseModel;

/**
 * 使用者任務記錄模型
 */
class TaskLog extends BaseModel
{
    protected $name = 'user_task_log';
    protected $pk = 'log_id';

    /**
     * 關聯會員記錄表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User', 'user_id', 'user_id');
    }
}