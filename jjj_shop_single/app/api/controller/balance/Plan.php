<?php

namespace app\api\controller\balance;

use app\api\controller\Controller;
use app\api\model\settings\Setting as SettingModel;
use app\api\model\user\BalancePlan as BalancePlanModel;
use app\api\model\user\BalanceOrder as BalanceOrderModel;
use app\api\service\pay\PayService;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\app\App as AppModel;

/**
 * 充值套餐
 */
class Plan extends Controller
{
    /**
     * 餘額首頁
     */
    public function index()
    {
        $params = $this->request->param();
        $user = $this->getUser();
        $list = (new BalancePlanModel)->getList();
        // 設定
        $settings = SettingModel::getItem('balance');
        // 是否開啟支付寶支付
        $show_alipay = PayService::isAlipayOpen($params['pay_source'], $user['app_id']);
        return $this->renderSuccess('', compact('list', 'settings', 'show_alipay'));
    }

    /**
     * 充值套餐
     */
    public function submit($plan_id, $user_money)
    {
        // 使用者資訊
        $user = $this->getUser();
        // 生成充值訂單
        $model = new BalanceOrderModel();
        $order_id = $model->createOrder($user, $plan_id, $user_money);
        if (!$order_id) {
            return $this->renderError($model->getError() ?: '購買失敗');
        }
        // 返回結算資訊
        return $this->renderSuccess('', [
            'order_id' => $order_id,   // 訂單id
        ]);
    }

    /**
     * 立即支付
     */
    public function pay($order_id, $payType = OrderPayTypeEnum::WECHAT)
    {
        // 使用者資訊
        $user = $this->getUser();
        // 獲取訂單詳情
        $model = BalanceOrderModel::getUserOrderDetail($order_id, $user['user_id']);
        $params = $this->postData();
        if ($this->request->isGet()) {
            // 開啟的支付型別
            $payTypes = AppModel::getPayType($model['app_id'], $params['pay_source'], $user);
            // 支付金額
            $payPrice = $model['pay_price'];
            return $this->renderSuccess('', compact('payTypes', 'payPrice'));
        }
        // 訂單支付事件
        if ($model['pay_status']['value'] != 10) {
            return $this->renderError($model->getError() ?: '訂單已支付');
        }
        // 線上支付
        $payment = BalanceOrderModel::onOrderPayment($user, $model, $payType, $params['pay_source']);
        // 返回結算資訊
        return $this->renderSuccess(['success' => '支付成功', 'error' => '訂單未支付'], [
            'order_id' => $order_id,   // 訂單id
            'pay_type' => $payType,  // 支付方式
            'payment' => $payment,               // 微信支付引數
            'order_type' => OrderTypeEnum::BALANCE, //訂單型別
            'return_Url' => $params['pay_source'] == 'h5' ? urlencode(base_url() . "h5/pages/user/my-wallet/my-wallet") : '', //h5支付跳轉地址
        ]);
    }
}