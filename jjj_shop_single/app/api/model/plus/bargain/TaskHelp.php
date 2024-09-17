<?php

namespace app\api\model\plus\bargain;

use app\common\model\plus\bargain\TaskHelp as TaskHelpModel;

/**
 * 砍價任務助力記錄模型
 */
class TaskHelp extends TaskHelpModel
{
    /**
     * 隱藏的欄位
     */
    protected $hidden = [
        'app_id',
        'create_time',
    ];

    /**
     * 獲取助力列表記錄
     */
    public static function getListByTaskId($bargain_task_id)
    {
        // 獲取列表資料
        return (new static())->with(['user'])
            ->where('bargain_task_id', '=', $bargain_task_id)
            ->order(['create_time' => 'desc'])
            ->select();
    }

    /**
     * 新增記錄
     */
    public function add($task, $userId, $cutMoney, $isCreater = false)
    {
        return $this->save([
            'bargain_task_id' => $task['bargain_task_id'],
            'bargain_activity_id' => $task['bargain_activity_id'],
            'user_id' => $userId,
            'cut_money' => $cutMoney,
            'is_creater' => $isCreater,
            'app_id' => static::$app_id,
        ]);
    }

}