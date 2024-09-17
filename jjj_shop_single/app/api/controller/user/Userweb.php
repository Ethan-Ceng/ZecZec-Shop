<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\Order as OrderModel;
use app\api\model\user\BalanceOrder as BalanceOrderModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\user\Sms as SmsModel;
use app\api\model\user\UserWeb as UserModel;
use app\api\model\plus\giftpackage\Order as GiftOrderModel;
use app\api\model\order\OrderAdvance as OrderAdvanceModel;

/**
 * h5 web使用者管理
 */
class Userweb extends Controller
{

    /**
     * 使用者自動登入,預設微信小程式
     */
    public function login()
    {
        $model = new UserModel;
        $user_id = $model->login($this->request->post());
        if ($user_id == 0) {
            return $this->renderError($model->getError() ?: '登入失敗');
        }
        return $this->renderSuccess('', [
            'user_id' => $user_id,
            'token' => $model->getToken()
        ]);
    }

    /**
     * 簡訊登入
     */
    public function sendCode($mobile)
    {
        $model = new SmsModel();
        if ($model->send($mobile)) {
            return $this->renderSuccess();
        }
        return $this->renderError($model->getError() ?: '傳送失敗');
    }


    public function payH5($order_id, $order_type = OrderTypeEnum::MASTER)
    {
        $user = $this->getUser();
        if ($order_type == OrderTypeEnum::MASTER) {
            // 訂單詳情
            $model = OrderModel::getUserOrderDetail($order_id, $user['user_id']);
            // 構建支付請求
            $payment = OrderModel::onOrderPayment($user, $model, OrderPayTypeEnum::WECHAT, 'payH5');
            $return_Url = urlencode(base_url() . "h5/pages/order/myorder");
        } else if ($order_type == OrderTypeEnum::GIFT) {
            // 訂單詳情
            $model = GiftOrderModel::getUserOrderDetail($order_id, $user['user_id']);
            // 構建支付請求
            $payment = GiftOrderModel::onOrderPayment($user, $model, OrderPayTypeEnum::WECHAT, 'payH5');
            $return_Url = urlencode(base_url() . "h5/pages/user/index/index");
        } else if ($order_type == OrderTypeEnum::BALANCE) {
            // 訂單詳情
            $model = BalanceOrderModel::getUserOrderDetail($order_id, $user['user_id']);
            // 構建支付請求
            $payment = BalanceOrderModel::onOrderPayment($user, $model, OrderPayTypeEnum::WECHAT, 'payH5');
            $return_Url = urlencode(base_url() . "h5/pages/user/my-wallet/my-wallet");
        } else if ($order_type == OrderTypeEnum::FRONT) {
            // 訂單詳情
            $model = OrderAdvanceModel::getUserOrderDetail($order_id, $user['user_id']);
            // 構建支付請求
            $payment = OrderAdvanceModel::onOrderPayment($user, $model, OrderPayTypeEnum::WECHAT, 'payH5');
            $return_Url = urlencode(base_url() . "h5/pages/order/myorder");
        }

        return $this->renderSuccess('', [
            'order' => $model,  // 訂單詳情
            'mweb_url' => $payment['mweb_url'],
            'return_Url' => $return_Url
        ]);
    }

    public function bindMobile()
    {
        $model = new UserModel;
        if ($model->bindMobile($this->getUser(), $this->request->post())) {
            return $this->renderSuccess('繫結成功');
        }
        return $this->renderError($model->getError() ?: '繫結失敗');
    }

    /**
     * h5下支付寶支付
     */
    public function alipayH5($order_id, $order_type = OrderTypeEnum::MASTER)
    {
        $user = $this->getUser();
        $payment = [];
        if ($order_type == OrderTypeEnum::MASTER) {
            // 訂單詳情
            $model = OrderModel::getUserOrderDetail($order_id, $user['user_id']);
            // 構建支付請求
            $payment = OrderModel::onOrderPayment($user, $model, OrderPayTypeEnum::ALIPAY, 'payH5');
        } else if ($order_type == OrderTypeEnum::GIFT) {
            // 訂單詳情
            $model = GiftOrderModel::getUserOrderDetail($order_id, $user['user_id']);
            // 構建支付請求
            $payment = GiftOrderModel::onOrderPayment($user, $model, OrderPayTypeEnum::ALIPAY, 'payH5');
        } else if ($order_type == OrderTypeEnum::BALANCE) {
            // 訂單詳情
            $model = BalanceOrderModel::getUserOrderDetail($order_id, $user['user_id']);
            // 構建支付請求
            $payment = BalanceOrderModel::onOrderPayment($user, $model, OrderPayTypeEnum::ALIPAY, 'payH5');
        } else if ($order_type == OrderTypeEnum::FRONT) {
            // 訂單詳情
            $model = OrderAdvanceModel::getUserOrderDetail($order_id, $user['user_id']);
            // 構建支付請求
            $payment = OrderAdvanceModel::onOrderPayment($user, $model, OrderPayTypeEnum::ALIPAY, 'payH5');
        }


        return $this->renderSuccess('', [
            'payment' => $payment,
        ]);
    }
}
