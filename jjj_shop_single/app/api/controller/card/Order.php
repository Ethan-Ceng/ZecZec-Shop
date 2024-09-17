<?php

namespace app\api\controller\card;

use app\api\controller\Controller;
use app\api\model\plus\card\Order as OrderModel;
use app\api\model\settings\Setting as SettingModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\settings\SettingEnum;
use app\common\model\settings\Setting;
use app\common\service\qrcode\ExtractService;

/**
 * 我的訂單
 */
class Order extends Controller
{
    // user
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
     * 我的訂單列表
     */
    public function lists($dataType)
    {
        $data = $this->postData();
        $model = new OrderModel;
        $list = $model->getList($this->user['user_id'], $dataType, $data);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 訂單詳情資訊
     */
    public function detail($order_id)
    {
        // 訂單詳情
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        return $this->renderSuccess('', [
            'order' => $model,  // 訂單詳情
        ]);
    }

    /**
     * 獲取物流資訊
     */
    public function express($order_id)
    {
        // 訂單資訊
        $order = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        if (!$order['express_no']) {
            return $this->renderError('沒有物流資訊');
        }
        // 獲取物流資訊
        $model = $order['express'];
        $express = $model->dynamic($model['express_name'], $model['express_code'], $order['express_no'], $order['mobile']);
        if ($express === false) {
            return $this->renderError($model->getError());
        }
        return $this->renderSuccess('', compact('express'));
    }

    /**
     * 取消訂單
     */
    public function cancel($order_id)
    {
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        if ($model->cancel($this->user)) {
            return $this->renderSuccess('訂單取消成功');
        }
        return $this->renderError($model->getError() ?: '訂單取消失敗');
    }

    /**
     * 確認收貨
     */
    public function receipt($order_id)
    {
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        if ($model->receipt()) {
            return $this->renderSuccess('收貨成功');
        }
        return $this->renderError($model->getError() ?: '收貨失敗');
    }

    /**
     * 立即支付
     */
    public function pay($order_id, $payType = OrderPayTypeEnum::WECHAT, $pay_source = 'wx')
    {
        // 獲取訂單詳情
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 訂單支付事件
        if (!$model->onPay($payType, $pay_source)) {
            return $this->renderError($model->getError() ?: '訂單支付失敗');
        }
        // 構建微信支付請求
        $payment = $model->onOrderPayment($this->user, $model, $payType, $pay_source);
        // 支付狀態提醒
        return $this->renderSuccess('', [
            'order_id' => $model['order_id'],   // 訂單id
            'pay_type' => $payType,             // 支付方式
            'payment' => $payment               // 微信支付引數
        ]);
    }

    /**
     * 獲取訂單核銷二維碼
     */
    public function qrcode($order_id, $source)
    {
        // 訂單詳情
        $order = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 判斷是否為待核銷訂單
        if (!$order->checkExtractOrder($order)) {
            return $this->renderError($order->getError());
        }
        $Qrcode = new ExtractService(
            $this->app_id,
            $this->user,
            $order_id,
            $source,
            $order['order_no']
        );
        return $this->renderSuccess('', [
            'qrcode' => $Qrcode->getImage(),
        ]);
    }

    private function formatPayEndTime($leftTime)
    {
        if ($leftTime <= 0) {
            return '';
        }

        $str = '';
        $day = floor($leftTime / 86400);
        $hour = floor(($leftTime - $day * 86400) / 3600);
        $min = floor((($leftTime - $day * 86400) - $hour * 3600) / 60);

        if ($day > 0) $str .= $day . '天';
        if ($hour > 0) $str .= $hour . '小時';
        if ($min > 0) $str .= $min . '分鐘';
        return $str;
    }
}