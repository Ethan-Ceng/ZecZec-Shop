<?php

namespace app\shop\controller\plus\agent;

use app\shop\controller\Controller;
use app\shop\model\plus\agent\Order as OrderModel;

/**
 * 分銷訂單
 */
class Order extends Controller
{

    /**
     * 分銷訂單列表
     */
    public function index()
    {
        $model = new OrderModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 訂單匯出
     */
    public function export()
    {
        $model = new OrderModel();
        return $model->exportList($this->postData());
    }

}