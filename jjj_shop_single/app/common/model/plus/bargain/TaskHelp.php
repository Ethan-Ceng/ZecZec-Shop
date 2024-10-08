<?php

namespace app\common\model\plus\bargain;

use app\common\model\BaseModel;

/**
 * 砍價任務助力記錄模型
 * Class TaskHelp
 * @package app\common\model\bargain
 */
class TaskHelp extends BaseModel
{
    protected $name = 'bargain_task_help';
    protected $pk = 'bargain_task_help_id';
    protected $updateTime = false;

    /**
     * 關聯使用者表
     */
    public function user()
    {
        return $this->BelongsTo('app\\common\\model\\user\\User', 'user_id', 'user_id')
            ->field(['user_id', 'nickName', 'avatarUrl']);
    }

}