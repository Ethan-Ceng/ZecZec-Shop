<?php

namespace app\job\event;

use app\common\model\settings\Setting as SettingModel;
use app\job\model\user\User as UserModel;
use app\api\model\user\TaskLog as TaskLogModel;
use think\facade\Cache;

/**
 * 任務中心管理
 */
class UserTask
{
    private $user_id;

    /**
     * 執行函式
     */
    public function handle($data)
    {
        $this->user_id = $data['user_id'];
        $this->finishTask($data);
        return true;
    }

    //執行任務
    public function finishTask($data)
    {
        if (in_array($data['task_type'], ['image', 'base'])) {
            $this->baseTask($data);
        } else {
            $this->dayTask($data);
        }
        return true;

    }

    //成長任務
    public function baseTask($data)
    {
        //判斷是否已完成
        $TaskLogModel = new TaskLogModel();
        $status = $TaskLogModel->where('user_id', '=', $this->user_id)
            ->where('task_type', '=', $data['task_type'])
            ->count();
        if ($status) {
            return false;
        }
        $user = UserModel::detail($this->user_id);
        $config = SettingModel::getItem('task', $user['app_id']);
        $task = $this->getConfig($config, $data['task_type']);
        if ($task && $task['is_open']) {
            $TaskLogModel->save([
                'user_id' => $this->user_id,
                'task_type' => $data['task_type'],
                'task_time' => date('Y-m-d'),
                'points' => $task['points'],
                'app_id' => $user['app_id'],
            ]);
            $describe = "完成任務：" . $task['name'];
            $task['points'] && $user->setIncPoints($task['points'], $describe . '獎勵');
        }
    }

    //日常任務
    public function dayTask($data)
    {
        //判斷是否已完成
        $status = Cache::get('task_' . $data['task_type'] . date('Y-m-d') . $this->user_id);
        if ($status) {
            return false;
        }
        $user = UserModel::detail($this->user_id);
        $config = SettingModel::getItem('task', $user['app_id']);
        $task = $this->getConfig($config, $data['task_type']);
        if ($task && $task['is_open']) {
            Cache::set('task_' . $data['task_type'] . date('Y-m-d') . $this->user_id, 1, 86410);
            $describe = "完成任務：" . $task['name'];
            $task['points'] && $user->setIncPoints($task['points'], $describe . '獎勵');
        }
    }

    //獲取當前任務配置
    public function getConfig($config, $task_type)
    {
        $data = '';
        if (in_array($task_type, ['image', 'base'])) {
            foreach ($config['grow_task'] as $item) {
                if ($item['task_type'] == $task_type) {
                    $data = $item;
                    break;
                }
            }
        } else {
            foreach ($config['day_task'] as $item) {
                if ($item['task_type'] == $task_type) {
                    $data = $item;
                    break;
                }
            }
        }
        return $data;
    }

}
