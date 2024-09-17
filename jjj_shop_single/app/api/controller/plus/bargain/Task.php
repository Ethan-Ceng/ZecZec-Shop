<?php

namespace app\api\controller\plus\bargain;

use app\api\controller\Controller;
use app\api\model\plus\bargain\Task as TaskModel;

/**
 * 砍價任務模型
 */
class Task extends Controller
{
    /**
     * 建立砍價任務
     */
    public function add($bargain_activity_id, $bargain_product_id, $bargain_product_sku_id, $product_sku_id)
    {
        // 使用者資訊
        $user = $this->getUser();
        // 建立砍價任務
        $model = new TaskModel;
        if (!$model->add($user['user_id'], $bargain_activity_id, $bargain_product_id, $bargain_product_sku_id, $product_sku_id)) {
            return $this->renderError($model->getError() ?: '砍價任務建立失敗');
        }
        return $this->renderSuccess('', [
            'bargain_task_id' => $model['bargain_task_id']
        ]);
    }

    /**
     * 獲取砍價任務詳情
     */
    public function detail($bargain_task_id, $url = '')
    {
        $detail = (new TaskModel)->getTaskDetail($bargain_task_id, $this->getUser(false));
        //分享
        $share = $this->getShareParams($url, "發現了一個好物，快來幫我砍一刀吧", $detail['task']['product_name'], '/pages/plus/bargain/haggle/haggle', $detail['product']['product']['image'][0]['file_path']);
        return $this->renderSuccess('', array_merge($detail, compact('share')));
    }

    /**
     * 幫砍一刀
     */
    public function cut($bargain_task_id)
    {
        // 砍價任務詳情
        $model = TaskModel::detail($bargain_task_id);
        // 砍一刀的金額
        $cut_money = $model->getCutMoney();
        // 幫砍一刀事件
        $status = $model->helpCut($this->getUser());
        if ($status == true) {
            return $this->renderSuccess('砍價成功', compact('cut_money'));
        }
        return $this->renderError($model->getError() ?: '砍價失敗');
    }

}