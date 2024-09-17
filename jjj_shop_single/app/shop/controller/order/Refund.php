<?php

namespace app\shop\controller\order;

use app\shop\controller\Controller;
use app\shop\model\order\Order as OrderModel;
use app\shop\model\order\OrderRefund as OrderRefundModel;
use app\shop\model\settings\ReturnAddress as ReturnAddressModel;
use app\shop\model\settings\Express as ExpressModel;

/**
 * 售後管理
 */
class Refund extends Controller
{
    /**
     * 售後列表
     */
    public function index()
    {
        $model = new OrderRefundModel;
        $params = $this->postData();
        //列表資料
        $list = $model->getList($params);
        //重要數字
        $num_arr = $model->groupCount($params);
        $arr = [];
        foreach ($num_arr as $key => $val) {
            $k = $val['status']['value'];
            $arr[$k] = $val;
        }

        return $this->renderSuccess('', compact('list', 'arr'));
    }

    /**
     * 售後單詳情
     */
    public function detail($order_refund_id)
    {
        // 售後單詳情
        $detail = OrderRefundModel::detail($order_refund_id);
        if (isset($detail['send_time']) && $detail['send_time'] > 0) {
            $detail['send_time'] = date('Y-m-d H:i:s', $detail['send_time']);
        }
        if (isset($detail['deliver_time']) && $detail['deliver_time'] > 0) {
            $detail['deliver_time'] = date('Y-m-d H:i:s', $detail['deliver_time']);
        }
        // 訂單詳情
        $order = OrderModel::detail($detail['order_id']);
        $detail['orderproduct']['max_refund_money'] = $detail['orderproduct']['total_pay_price'];
        if ($order['order_source'] == 80) {
            $detail['orderproduct']['total_pay_price'] = round($detail['orderproduct']['total_pay_price'] + $order['advance']['pay_price'], 2);
            if ($order['advance']['money_return'] == 1) {
                $detail['orderproduct']['max_refund_money'] = round($detail['orderproduct']['max_refund_money'] + $order['advance']['pay_price'], 2);
            }
        }
        // 退貨地址
        $address = (new ReturnAddressModel)->getAll();
        // 物流公司列表
        $model = new ExpressModel();
        $expressList = $model->getAll();
        return $this->renderSuccess('', compact('detail', 'order', 'address', 'expressList'));
    }

    /**
     * 商家稽核
     */
    public function audit($order_refund_id)
    {
        $model = OrderRefundModel::detail($order_refund_id);
        if ($model->audit($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 確認收貨並退款
     */
    public function receipt($order_refund_id)
    {
        if (!$this->request->isPost()) {
            return false;
        }
        $model = OrderRefundModel::detail($order_refund_id);
        if ($model->receipt($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

}