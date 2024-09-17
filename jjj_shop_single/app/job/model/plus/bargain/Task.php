<?php

namespace app\job\model\plus\bargain;

use app\common\model\plus\bargain\Task as TaskModel;


/**
 * 砍價任務模型
 */
class Task extends TaskModel
{
    /**
     * 獲取已過期但未結束的砍價任務
     */
    public function getEndList()
    {
        return $this->where('end_time', '<=', time())
            ->where('status', '=', 0)
            ->where('is_delete', '=', 0)
            ->select();
    }

    /**
     * 將砍價任務標記為砍價失敗(批次)
     */
    public function setIsEnd($taskIds)
    {
        return $this->where('bargain_task_id' , 'in', $taskIds)->data([
            'status' => 2
        ])->update();
    }

}