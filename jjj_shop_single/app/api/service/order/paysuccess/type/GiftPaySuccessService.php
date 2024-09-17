<?php

namespace app\api\service\order\paysuccess\type;

use app\api\model\plus\giftpackage\Order as OrderModel;
use app\api\model\user\User as UserModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;
use app\common\model\user\BalanceLog as BalanceLogModel;
use app\common\service\BaseService;
use app\common\model\plus\giftpackage\GiftPackage;
use app\api\model\plus\coupon\UserCoupon;

/**
 * 禮包購訂單支付成功後的回撥
 */
class GiftPaySuccessService extends BaseService
{
    // 訂單模型
    public $model;

    // 當前使用者資訊
    private $user;

    /**
     * 建構函式
     */
    public function __construct($orderNo, $pay_status = 10)
    {
        // 例項化訂單模型
        $this->model = OrderModel::getPayDetail($orderNo, $pay_status);
        if ($this->model) {
            // 獲取使用者資訊
            $this->user = UserModel::detail($this->model['user_id']);
        }
    }

    /**
     * 訂單支付成功業務處理
     */
    public function onPaySuccess($payType, $payData = [])
    {
        if (empty($this->model)) {
            $this->error = '未找到該訂單資訊';
            return false;
        }
        // 更新付款狀態
        $status = $this->updatePayStatus($payType, $payData);
        if ($status) {
            // 獲取訂單詳情
            $detail = OrderModel::getUserOrderDetail($this->model['order_id'], $this->user['user_id']);
            // 小程式虛擬商品發貨
            (new OrderModel)->sendWxExpress($detail, $this->user);
        }
        return $status;
    }

    /**
     * 更新付款狀態
     */
    private function updatePayStatus($payType, $payData = [])
    {
        // 驗證餘額支付時使用者餘額是否滿足
        if ($payType == OrderPayTypeEnum::BALANCE) {
            if ($this->user['balance'] < $this->model['pay_price']) {
                $this->error = '使用者餘額不足，無法使用餘額支付';
                return false;
            }
        }
        // 事務處理
        $this->model->transaction(function () use ($payType, $payData) {
            // 更新訂單狀態
            $this->updateOrderInfo($payType, $payData);
            // 累積使用者總消費金額
            $this->user->setIncPayMoney($this->model['pay_price']);
            // 記錄訂單支付資訊
            $this->updatePayInfo();
            //贈送禮包
            $this->sendGift();
            //贈送商品
            $this->sendProduct();
            //更新購買人數；
            $this->updatePeople();
        });
        return true;
    }

    //更新購買任務
    private function updatePeople()
    {
        $gift = GiftPackage::detail($this->model['gift_package_id']);
        if (empty($gift)) {
            return false;
        }
        return (new GiftPackage())->where('gift_package_id', '=', $this->model['gift_package_id'])->update(['people' => $gift['people'] + 1]);
    }

    //贈送商品
    private function sendProduct()
    {
        $product_ids = $this->model['product_ids'];
        if ($product_ids) {
            $OrderModel = new OrderModel;
            $OrderModel->addOrder($product_ids, $this->model['order_no'], $this->model['app_id']);
        }
    }

    /**
     * 贈送禮包
     */
    private function sendGift()
    {
        $gift = GiftPackage::detail($this->model['gift_package_id']);
        if (empty($gift)) {
            return;
        }
        if ($gift['is_coupon'] == 1) {
            $user_coupon = new UserCoupon();
            $user_coupon->addNewUserCoupon($gift['coupon_ids'], $this->model['user_id'], $this->model['app_id']);
        }
        if ($gift['is_point'] == 1) {
            $this->user->setIncPoints($gift['point'], '禮包購贈送積分');
        }
    }

    /**
     * 更新訂單記錄
     */
    private function updateOrderInfo($payType, $payData)
    {
        // 整理訂單資訊
        $pay_source = '';
        if (isset($payData['attach'])) {
            $attach = json_decode($payData['attach'], true);
            $pay_source = isset($attach['pay_source']) ? $attach['pay_source'] : '';
        }
        $order = [
            'pay_type' => $payType,
            'pay_status' => 20,
            'pay_time' => time(),
            'pay_source' => $pay_source
        ];
        if ($payType == OrderPayTypeEnum::WECHAT) {
            $order['transaction_id'] = $payData['transaction_id'];
        }
        // 更新訂單狀態
        return $this->model->save($order);
    }

    /**
     * 記錄訂單支付資訊
     */
    private function updatePayInfo()
    {
        // 餘額支付
        if ($this->model['balance'] > 0) {
            // 更新使用者餘額
            $this->user->where('user_id', '=', $this->user['user_id'])
                ->dec('balance', $this->model['balance'])
                ->update();
            BalanceLogModel::add(BalanceLogSceneEnum::CONSUME, [
                'user_id' => $this->user['user_id'],
                'money' => -$this->model['balance'],
                'app_id' => $this->model['app_id'],
            ], ['order_no' => $this->model['order_no']]);
        }
    }
}