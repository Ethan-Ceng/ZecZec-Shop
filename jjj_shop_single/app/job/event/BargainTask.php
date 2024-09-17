<?php

namespace app\job\event;

use think\facade\Cache;
use app\job\model\plus\bargain\Task as TaskModel;
use app\common\library\helper;

/**
 * 砍價任務行為管理
 */
class BargainTask
{
    private $model;

    /**
     * 執行函式
     */
    public function handle()
    {
        try {
            $this->model = new TaskModel();
            $cacheKey = "task_space_bargain_task";
            if (!Cache::has($cacheKey)) {
                // 將已過期的砍價任務標記為已結束
                $this->onSetIsEnd();
                Cache::set($cacheKey, time(), 10);
            }
        } catch (\Throwable $e) {
            echo 'ERROR BargainTask: ' . $e->getMessage() . PHP_EOL;
            log_write('BargainTask TASK : ' . '__ ' . $e->getMessage(), 'task');
        }
        return true;
    }

    /**
     * 將已過期的砍價任務標記為已結束
     */
    private function onSetIsEnd()
    {
        // 獲取已過期但未結束的砍價任務
        $list = $this->model->getEndList();
        $taskIds = helper::getArrayColumn($list, 'bargain_task_id');
        // 將砍價任務標記為已結束(批次)
        !empty($taskIds) && $this->model->setIsEnd($taskIds);
        // 記錄日誌
        $this->dologs('close', [
            'orderIds' => json_encode($taskIds),
        ]);
        return true;
    }

    /**
     * 記錄日誌
     */
    private function dologs($method, $params = [])
    {
        $value = 'behavior bargain Task --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value, 'task');
    }

}