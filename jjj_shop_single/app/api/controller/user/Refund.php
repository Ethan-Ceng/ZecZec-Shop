<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\Order as OrderModel;
use app\api\model\settings\Express as ExpressModel;
use app\api\model\order\OrderProduct as OrderProductModel;
use app\api\model\order\OrderRefund as OrderRefundModel;
use app\api\model\settings\Message as MessageModel;

/**
 * 訂單售後服務
 */
class Refund extends Controller
{
    // $user
    private $user;

    /**
     * 構造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->user = $this->getUser();   // 使用者資訊
    }

    /**
     * 使用者售後單列表
     */
    public function lists($state = -1)
    {
        $model = new OrderRefundModel;
        $list = $model->getList($this->user['user_id'], $state, $this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 申請售後
     */
    public function apply($order_product_id, $platform = 'wx')
    {
        // 訂單商品詳情
        $detail = OrderProductModel::detail($order_product_id);
        $refund = (new OrderRefundModel())->allowRefund($detail['order_id'], $order_product_id);
        if ($refund) {
            return $this->renderError('當前商品已申請售後');
        }
        if ($detail['orderM']['order_source'] == 80 && $detail['orderM']['advance']['money_return'] == 1) {
            $detail['total_pay_price'] = round($detail['total_pay_price'] + $detail['orderM']['advance']['pay_price'], 2);
        }
        if ($this->request->isGet()) {
            // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取售後通知.
            $template_arr = MessageModel::getMessageByNameArr($platform, ['order_refund_user']);
            return $this->renderSuccess('', compact('detail', 'template_arr'));
        }
        // 新增售後單記錄
        $model = new OrderRefundModel;
        if ($model->apply($this->user, $detail, $this->request->post())) {
            return $this->renderSuccess('提交成功');
        }
        return $this->renderError($model->getError() ?: '提交失敗');
    }

    /**
     * 售後單詳情
     */
    public function detail($order_refund_id, $platform = '')
    {
        // 售後單詳情
        $detail = OrderRefundModel::detail([
            'user_id' => $this->user['user_id'],
            'order_refund_id' => $order_refund_id
        ]);
        if (empty($detail)) {
            return $this->renderError('售後單不存在');
        }
        $detail['orderproduct']['max_refund_money'] = $detail['orderproduct']['total_pay_price'];
        if ($detail['order_master']['order_source'] == 80) {
            $detail['orderproduct']['total_pay_price'] = round($detail['orderproduct']['total_pay_price'] + $detail['order_master']['advance']['pay_price'], 2);
            if ($detail['order_master']['advance']['money_return'] == 1) {
                $detail['orderproduct']['max_refund_money'] = round($detail['orderproduct']['max_refund_money'] + $detail['order_master']['advance']['pay_price'], 2);
            }
        }
        // 物流公司列表
        $model = new ExpressModel();
        $expressList = $model->getAll();
        // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取售後通知.
        $template_arr = MessageModel::getMessageByNameArr($platform, ['order_refund_user']);
        return $this->renderSuccess('', compact('detail', 'expressList', 'template_arr'));
    }

    /**
     * 使用者發貨
     */
    public function delivery($order_refund_id)
    {
        // 售後單詳情
        $model = OrderRefundModel::detail([
            'user_id' => $this->user['user_id'],
            'order_refund_id' => $order_refund_id
        ]);
        if ($model->delivery($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '提交失敗');
    }

    /**
     * 獲取物流資訊
     */
    public function express($order_refund_id)
    {
        // 訂單資訊
        $model = OrderRefundModel::detail($order_refund_id);
        if (!$model['send_express_no']) {
            return $this->renderError('沒有物流資訊');
        }
        $order = OrderModel::getUserOrderDetail($model['order_id'], $this->user['user_id']);
        // 獲取物流資訊
        $model = $model['sendexpress'];
        $express = $model->dynamic($model['express_name'], $model['express_code'], $model['send_express_no'], $order['address']['phone']);
        if ($express === false) {
            return $this->renderError($model->getError());
        }
        return $this->renderSuccess('', compact('express'));
    }

}