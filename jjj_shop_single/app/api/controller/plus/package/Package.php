<?php

namespace app\api\controller\plus\package;


use app\api\controller\Controller;
use app\api\model\plus\giftpackage\GiftPackage as GiftPackageModel;
use app\api\model\plus\giftpackage\Order as OrderModel;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\app\App as AppModel;

/**
 * 禮包購控制器
 */
class Package extends Controller
{
    /**
     * 獲取資料
     */
    public function index($package_id)
    {
        // 使用者資訊
        $user = $this->getUser(false);
        $params = $this->request->param();
        $model = new GiftPackageModel();
        $data = $model->getGiftPackage($package_id, $params, $user);
        if (!$data) {
            return $this->renderError($model->getError() ?: '活動不存在');
        }
        return $this->renderSuccess('', compact('data'));
    }

    /**
     * 禮包購
     */
    public function buy($package_id)
    {
        // 使用者資訊
        $user = $this->getUser();
        $params = $this->request->param();
        if ($this->request->isGet()) {
            $model = new GiftPackageModel();
            $data = $model->checkGiftPackage($package_id, $params, $user);
            if ($data) {
                return $this->renderSuccess('', compact('data'));
            } else {
                return $this->renderError($model->getError() ?: '購買失敗');
            }
        }
        // 生成禮品訂單
        $model = new OrderModel;
        // 建立訂單
        if (!$model->createOrder($user, $package_id, $params)) {
            return $this->renderError($model->getError() ?: '訂單建立失敗');
        }
        // 返回結算資訊
        return $this->renderSuccess('', [
            'order_id' => $model['order_id'],   // 訂單id
        ]);
    }

    /**
     * 立即支付
     */
    public function pay($order_id)
    {
        // 使用者資訊
        $user = $this->getUser();
        // 獲取訂單詳情
        $model = OrderModel::getUserOrderDetail($order_id, $user['user_id']);
        $params = $this->postData();
        if ($this->request->isGet()) {
            // 開啟的支付型別
            $payTypes = AppModel::getPayType($model['app_id'], $params['pay_source'], $user);
            // 支付金額
            $payPrice = $model['pay_price'];
            $balance = $user['balance'];
            return $this->renderSuccess('', compact('payTypes', 'payPrice', 'balance'));
        }
        // 訂單支付事件
        if ($model['pay_status']['value'] != 10) {
            return $this->renderError($model->getError() ?: '訂單已支付');
        }
        $OrderModel = new OrderModel;
        // 構建微信支付請求
        $payInfo = $OrderModel->OrderPay($params, $model, $user);
        if (!$payInfo) {
            return $this->renderError($OrderModel->getError() ?: '訂單支付失敗');
        }
        // 支付狀態提醒
        return $this->renderSuccess('', [
            'order_id' => $order_id,   // 訂單id
            'pay_type' => $payInfo['payType'],  // 支付方式
            'payment' => $payInfo['payment'],   // 微信支付引數
            'order_type' => OrderTypeEnum::GIFT, //訂單型別
            'return_Url' => $params['pay_source'] == 'h5' ? urlencode(base_url() . "h5/pages/user/index/index") : '', //h5支付跳轉地址
        ]);
    }
}