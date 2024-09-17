<?php

namespace app\shop\controller\plus\card;

use app\shop\controller\Controller;
use app\shop\model\plus\card\Order as OrderModel;
use app\shop\model\settings\Express as ExpressModel;
use app\shop\model\plus\card\Category as CategoryModel;

/**
 * 訂單控制器
 */
class Order extends Controller
{
    /**
     * 訂單列表
     */
    public function index($dataType = 'all')
    {
        // 訂單列表
        $model = new OrderModel();
        $list = $model->getList($dataType, $this->postData());
        $order_count = [
            'wait' => $model->getCount('wait', $this->postData()),
        ];
        $categoryList = CategoryModel::getALL();
        return $this->renderSuccess('', compact('list', 'order_count', 'categoryList'));
    }

    /**
     * 訂單詳情
     */
    public function detail($order_id)
    {
        // 訂單詳情
        $detail = OrderModel::detail($order_id, ['user', 'express']);
        if (isset($detail['delivery_time']) && $detail['delivery_time'] != '') {
            $detail['delivery_time'] = date('Y-m-d H:i:s', $detail['delivery_time']);
        }
        // 物流公司列表
        $model = new ExpressModel();
        $expressList = $model->getAll();
        return $this->renderSuccess('', compact('detail', 'expressList'));
    }

    /**
     * 確認發貨
     */
    public function delivery($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->delivery($this->postData('param'))) {
            return $this->renderSuccess('發貨成功');
        }
        return $this->renderError('發貨失敗');
    }

    /**
     * 修改訂單價格
     */
    public function updatePrice($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->updatePrice($this->postData('order'))) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     * 取消訂單
     */
    public function orderCancel($order_no)
    {
        // 訂單資訊
        $model = OrderModel::detail(['order_no' => $order_no]);
        if ($model->orderCancel($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 虛擬商品發貨
     */
    public function virtual($order_id)
    {
        // 訂單資訊
        $model = OrderModel::detail($order_id);
        if ($model->virtual($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

}