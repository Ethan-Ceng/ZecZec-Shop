<?php

namespace app\shop\controller\order;

use app\shop\controller\Controller;
use app\shop\model\order\Order as OrderModel;

/**
 * 訂單操作
 * @package app\shop\controller\order
 */
class Operate extends Controller
{
    /**
     * 訂單匯出
     */
    public function export($dataType)
    {
        $model = new OrderModel();
        return $model->exportList($dataType, $this->postData());
    }

    /**
     * 稽核：使用者取消訂單
     */
    public function confirmCancel($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->confirmCancel($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?:'操作失敗');
    }

    /**
     * 門店自提核銷
     */
    public function extract()
    {
        $params = $this->postData('extract_form');
        $model = OrderModel::detail($params['order_id']);
        if ($model->verificationOrder($params['order']['extract_clerk_id'])) {
            return $this->renderSuccess('核銷成功');
        }
        return $this->renderError($model->getError() ?: '核銷失敗');
    }

    /**
     * 批次發貨
     */
    public function batchDelivery(){
        // 檔案資訊
        $fileInfo = request()->file('iFile');
        $model = new OrderModel();
        if($model->batchDelivery($fileInfo)){
            return $this->renderSuccess('批次發貨成功');
        }
        return $this->renderError($model->getError() ?: '批次發貨失敗');
    }
}