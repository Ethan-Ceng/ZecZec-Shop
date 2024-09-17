<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\Order as OrderModel;
use app\api\model\settings\Setting as SettingModel;
use app\common\enum\settings\SettingEnum;
use app\common\model\settings\Express as ExpressModel;
use app\common\model\settings\Setting;
use app\common\service\qrcode\ExtractService;
use app\common\model\app\App as AppModel;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\plus\table\Record as TableRecordModel;

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
        $h5_alipay = Setting::getItem(SettingEnum::H5ALIPAY)['is_open'];
        $app = AppModel::detail();
        $mch_id = $app['mchid'];
        $is_send_wx = Setting::getItem('store')['is_send_wx'];
        return $this->renderSuccess('', compact('list', 'h5_alipay', 'mch_id', 'is_send_wx'));
    }

    /**
     * 訂單詳情資訊
     */
    public function detail($order_id)
    {
        // 訂單詳情
        $model = OrderModel::getOrderDetail($order_id, $this->user['user_id']);
        // 剩餘支付時間
        if ($model['pay_status']['value'] == 10 && $model['order_status']['value'] != 20 && $model['pay_end_time'] != 0) {
            $model['pay_end_time'] = $this->formatPayEndTime($model['pay_end_time'] - time());
        } else {
            $model['pay_end_time'] = '';
        }
        if (isset($model['pay_time']) && $model['pay_time']) {
            $model['pay_time'] = date('Y-m-d H:i:s', $model['pay_time']);
        }
        if (isset($model['receipt_time']) && $model['receipt_time']) {
            $model['receipt_time'] = date('Y-m-d H:i:s', $model['receipt_time']);
        }
        if (isset($model['delivery_time']) && $model['delivery_time']) {
            $model['delivery_time'] = date('Y-m-d H:i:s', $model['delivery_time']);
        }
        // 是否提示填寫表單
        $show_table = false;
        foreach ($model['product'] as &$item) {
            if ($item['table_id'] > 0) {
                $show_table = true;
                // 表單值
                if ($item['table_record_id'] > 0) {
                    $record = TableRecordModel::detail($item['table_record_id']);
                    $item['tableData'] = json_decode($record['content'], true);
                }
            }
        }
        // 該訂單是否允許申請售後
        $model['isAllowRefund'] = $model->isAllowRefund();
        $app = AppModel::detail();
        $mch_id = $app['mchid'];
        $is_send_wx = Setting::getItem('store')['is_send_wx'];
        return $this->renderSuccess('', [
            'order' => $model,  // 訂單詳情
            'show_table' => $show_table,
            'setting' => [
                // 積分名稱
                'points_name' => SettingModel::getPointsName(),
            ],
            'mch_id' => $mch_id,
            'is_send_wx' => $is_send_wx
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
        $express = $model->dynamic($model['express_name'], $model['express_code'], $order['express_no'], $order['address']['phone']);
        if ($express === false) {
            return $this->renderError($model->getError());
        }
        return $this->renderSuccess('', compact('express'));
    }

    /**
     * 獲取多包裹物流資訊
     */
    public function multiExpress($order_id, $express_no, $express_id)
    {
        if (!$order_id || !$express_no || !$express_id) {
            return $this->renderError('引數錯誤');
        }
        $detail = ExpressModel::detail($express_id);
        // 訂單資訊
        $order = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        if (!$order) {
            return $this->renderError('訂單不存在');
        }
        if (!$detail) {
            return $this->renderError('沒有物流資訊');
        }
        // 獲取物流資訊
        $model = new ExpressModel();
        $express = $model->dynamic($detail['express_name'], $detail['express_code'], $express_no, $order['address']['phone']);
        if ($express === false) {
            return $this->renderError($model->getError());
        }
        $data['expressName'] = $detail['express_name'];
        $data['expressNo'] = $express_no;
        $data['expressId'] = $express_id;
        return $this->renderSuccess('', compact('data', 'express'));
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
    public function pay($order_id)
    {
        // 獲取訂單詳情
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        $params = $this->postData();
        if ($this->request->isGet()) {
            // 開啟的支付型別
            $payTypes = AppModel::getPayType($model['app_id'], $params['pay_source'], $this->user);
            // 支付金額
            $payPrice = $model['pay_price'];
            $balance = $this->user['balance'];
            return $this->renderSuccess('', compact('payTypes', 'payPrice', 'balance'));
        }
        // 訂單支付事件
        if (!$model->onPay()) {
            return $this->renderError($model->getError() ?: '訂單支付失敗');
        }
        $OrderModel = new OrderModel;
        // 構建微信支付請求
        $payInfo = $OrderModel->OrderPay($params, $model, $this->getUser());
        if (!$payInfo) {
            return $this->renderError($OrderModel->getError() ?: '訂單支付失敗');
        }
        // 支付狀態提醒
        return $this->renderSuccess('', [
            'order_id' => $order_id,   // 訂單id
            'pay_type' => $payInfo['payType'],  // 支付方式
            'payment' => $payInfo['payment'],   // 微信支付引數
            'order_type' => OrderTypeEnum::MASTER, //訂單型別
            'return_Url' => $params['pay_source'] == 'h5' ? urlencode(base_url() . "h5/pages/order/myorder") : '', //h5支付跳轉地址
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