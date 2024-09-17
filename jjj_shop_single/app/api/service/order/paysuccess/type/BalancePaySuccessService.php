<?php

namespace app\api\service\order\paysuccess\type;

use app\api\model\user\BalanceOrder as OrderModel;
use app\api\model\user\User as UserModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;
use app\common\model\user\BalanceLog as BalanceLogModel;
use app\common\service\BaseService;

/**
 * 餘額充值訂單支付成功後的回撥
 */
class BalancePaySuccessService extends BaseService
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
        // 事務處理
        $this->model->transaction(function () use ($payType, $payData) {
            // 更新訂單狀態
            $this->updateOrderInfo($payType, $payData);
            // 記錄訂單支付資訊
            $this->updatePayInfo($payType);
        });
        return true;
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
        if ($payType == OrderPayTypeEnum::WECHAT || $payType == OrderPayTypeEnum::ALIPAY) {
            $order['transaction_id'] = $payData['transaction_id'];
        }
        // 更新訂單狀態
        return $this->model->save($order);
    }

    /**
     * 記錄訂單支付資訊
     */
    private function updatePayInfo($payType)
    {
        // 更新使用者餘額
        (new UserModel())->where('user_id', '=', $this->user['user_id'])
            ->inc('balance', $this->model['real_money'])
            ->update();

        BalanceLogModel::add(BalanceLogSceneEnum::RECHARGE, [
            'user_id' => $this->user['user_id'],
            'money' => $this->model['pay_price'],
            'app_id' => $this->user['app_id']
        ], ['order_no' => $this->model['order_no']]);
    }
}