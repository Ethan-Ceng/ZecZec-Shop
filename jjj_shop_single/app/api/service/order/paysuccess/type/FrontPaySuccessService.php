<?php

namespace app\api\service\order\paysuccess\type;

use app\api\model\user\User as UserModel;
use app\api\model\order\Order as OrderModel;
use app\api\model\order\OrderAdvance as OrderAdvanceModel;
use app\common\model\user\BalanceLog as BalanceLogModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;
use app\common\service\BaseService;
use app\common\model\settings\Setting as SettingModel;
use app\common\service\product\factory\ProductFactory;

/**
 * 訂單支付成功服務類
 */
class FrontPaySuccessService extends BaseService
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
        $this->model = OrderAdvanceModel::getPayDetail($orderNo, $pay_status);
        if($this->model) {
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
        $status = $this->updatePayStatus($payType, $payData);
        // 訂單支付成功行為
        if ($status == true && $payType == OrderPayTypeEnum::WECHAT) {
            // 獲取訂單詳情
            $detail = OrderAdvanceModel::getUserOrderDetail($this->model['order_id'], $this->user['user_id']);
            //小程式發貨
            (new OrderAdvanceModel)->sendWxExpress($detail, $this->user);
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
            // 累積使用者總消費金額
            $this->user->setIncPayMoney($this->model['pay_price']);
            // 記錄訂單支付資訊
            $this->updatePayInfo();
            // 更新主訂單
            $this->updatePayOrderInfo();
        });
        return true;
    }

    /**
     * 更新訂單記錄
     */
    private function updatePayOrderInfo()
    {
        $config = SettingModel::getItem('advance',$this->model['app_id']);
        $pay_time = $config['pay_time'];
        if ($pay_time > 0) {
            $order['pay_end_time'] = $this->model['end_time'] + ($pay_time * 3600);
            // 更新訂單狀態
            return $this->model['orderM']->save($order);
        }
    }

    /**
     * 更新訂單記錄
     */
    private function updateOrderInfo($payType, $payData)
    {
        // 更新商品庫存、銷量
        ProductFactory::getFactory($this->model['orderM']['order_source'])->updateAdvanceStockSales($this->model['orderM']['product']);
        // 更新商品庫存 (針對下單減庫存的商品)
        ProductFactory::getFactory($this->model['orderM']['order_source'])->updateProductStock($this->model['orderM']['product']);
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
            'pay_source' => $pay_source,
            'main_order_no' => $this->model['orderM']['order_no'],
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
    private function updatePayInfo()
    {
        // 餘額支付
        if ($this->model['balance'] > 0) {
            // 更新使用者餘額
            (new UserModel())->where('user_id', '=', $this->user['user_id'])
                ->dec('balance', $this->model['balance'])
                ->update();
            BalanceLogModel::add(BalanceLogSceneEnum::CONSUME, [
                'user_id' => $this->user['user_id'],
                'money' => -$this->model['balance'],
                'app_id' => $this->model['app_id']
            ], ['order_no' => $this->model['order_no']]);
        }
    }

}