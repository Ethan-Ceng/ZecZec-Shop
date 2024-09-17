<?php

namespace app\job\event;

use think\facade\Cache;
use app\job\model\plus\agent\Order as AgentOrderModel;

/**
 * 分銷商訂單事件管理
 */
class AgentOrder
{
    // 模型
    private $model;

    /**
     * 執行函式
     */
    public function handle()
    {
        try {
            $this->model = new AgentOrderModel();
            $cacheKey = "task_space_AgentOrder";
            if (!Cache::has($cacheKey)) {
                $this->model->startTrans();
                try {
                    // 發放分銷訂單佣金
                    $this->grantMoney();
                    $this->model->commit();
                } catch (\Exception $e) {
                    $this->model->rollback();
                }
                Cache::set($cacheKey, time(), 60);
            }
        } catch (\Throwable $e) {
            echo 'ERROR AgentOrder: ' . $e->getMessage() . PHP_EOL;
            log_write('AgentOrder TASK : ' . '__ ' . $e->getMessage(), 'task');
        }
        return true;
    }

    /**
     * 發放分銷訂單佣金
     */
    private function grantMoney()
    {
        // 獲取未結算佣金的訂單列表
        $list = $this->model->getUnSettledList();
        if ($list->isEmpty()) return false;

        // 整理id集
        $invalidIds = [];
        $grantIds = [];
        // 發放分銷訂單佣金
        foreach ($list->toArray() as $item) {
            // 已失效的訂單
            if ($item['order_master']['order_status']['value'] == 20) {
                $invalidIds[] = $item['id'];
            }
            // 已完成的訂單
            if ($item['order_master']['order_status']['value'] == 30) {
                $grantIds[] = $item['id'];
                AgentOrderModel::grantMoney($item['order_master'], $item['order_type']['value']);
            }
        }

        // 標記已失效的訂單
        $this->model->setInvalid($invalidIds);

        // 記錄日誌
        $this->dologs('invalidIds', ['Ids' => $invalidIds]);
        $this->dologs('grantMoney', ['Ids' => $grantIds]);
        return true;
    }

    /**
     * 記錄日誌
     */
    private function dologs($method, $params = [])
    {
        $value = 'behavior AgentOrder --' . $method;
        foreach ($params as $key => $val) {
            $value .= ' --' . $key . ' ' . (is_array($val) ? json_encode($val) : $val);
        }
        return log_write($value, 'task');
    }

}