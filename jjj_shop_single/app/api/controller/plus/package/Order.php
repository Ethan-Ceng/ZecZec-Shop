<?php

namespace app\api\controller\plus\package;

use app\api\controller\Controller;
use app\api\model\plus\giftpackage\Order as OrderModel;

/**
 *
 * 禮包購訂單控制器
 *
 */
class Order extends Controller
{
    /**
     * 購買記錄
     */
    public function orderlist()
    {   
        // 使用者資訊
        $user = $this->getUser();
        $data = $this->postData();
        $model = new OrderModel();
        $list = $model->getList($user,$data);
        return $this->renderSuccess('', compact('list'));
    }
    /**
     * 記錄詳情
     */
    public function orderdetail($order_no)
    {   
        $model = new OrderModel();
        $detail = $model->orderDetail($order_no);
        return $this->renderSuccess('', compact('detail'));
    }
}