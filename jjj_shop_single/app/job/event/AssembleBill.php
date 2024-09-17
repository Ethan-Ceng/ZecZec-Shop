<?php

namespace app\job\event;

use think\facade\Cache;
use app\job\model\plus\assemble\Bill as BillModel;
use app\common\library\helper;

/**
 * 拼團任務行為管理
 */
class AssembleBill
{
    private $model;

    /**
     * 執行函式
     */
    public function handle()
    {
        try {
            $this->model = new BillModel();
            $cacheKey = "task_space_assemble_bill_task";
            if (!Cache::has($cacheKey)) {
                // 將已過期的拼團任務標記為已結束
                $this->closeAssemble();
                Cache::set($cacheKey, time(), 10);
            }
        } catch (\Throwable $e) {
            echo 'ERROR AssembleBill: ' . $e->getMessage() . PHP_EOL;
            log_write('AssembleBill TASK : ' . '__ ' . $e->getMessage(), 'task');
        }
        return true;
    }

    /**
     * 到期訂單未拼團成功自動關閉，並退款
     */
    private function closeAssemble()
    {
        // 獲取到期未拼團成功的訂單
        $billList = $this->model->getCloseIds(0);
        $billIds = helper::getArrayColumn($billList, 'assemble_bill_id');
        if (!empty($billIds)) {
            //關閉訂單
            $this->model->close($billIds);
        }
        // 記錄日誌
        $this->dologs('closeAssemble fail', [
            'billIds' => json_encode($billIds),
            'error' => $this->model->getError()
        ]);
        // 獲取到期未拼團成功的訂單,自動成團
        $billSuccessList = $this->model->getCloseIds(1);
        $billSuccessIds = helper::getArrayColumn($billSuccessList, 'assemble_bill_id');
        if (!empty($billSuccessIds)) {
            //關閉訂單
            $this->model->success($billSuccessIds);
        }

        // 退款
        $this->model->orderRefund();
        // 記錄日誌
        $this->dologs('closeAssemble success', [
            'billIds' => json_encode($billSuccessIds),
            'error' => $this->model->getError()
        ]);
        return true;
    }

    /**
     * 記錄日誌
     */
    private function dologs($method, $params = [])
    {
        $value = 'behavior assemble_bill Task --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value, 'task');
    }

}