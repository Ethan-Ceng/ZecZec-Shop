<?php

namespace app\api\model\order;

use app\api\service\order\PaymentService;
use app\api\service\order\paysuccess\type\FrontPaySuccessService;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\exception\BaseException;
use app\common\model\order\OrderAdvance as OrderAdvanceModel;
use app\api\model\order\Order as OrderModel;
use app\common\model\plus\advance\AdvanceSku as ProductSkuModel;
use app\common\enum\product\DeductStockTypeEnum;
use app\common\service\product\factory\ProductFactory;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\service\order\OrderRefundService;

/**
 * 預售定金模型
 */
class OrderAdvance extends OrderAdvanceModel
{

    /**
     * 訂單支付事件
     * 返回0，失敗 1，需要繼續支付 2，無需繼續支付，餘額支付成功
     */
    public function onPay()
    {
        if ($this['order_status'] != 10 || $this['pay_status']['value'] != 10) {
            $this->error = '很抱歉，當前訂單不合法，無法支付';
            return false;
        }
        if ($this['end_time'] < time()) {
            $this->error = "很抱歉，預售商品時間已結束";
            return false;
        }
        if ($this['pay_end_time'] < time()) {
            $this->error = "很抱歉，超過支付時間";
            return false;
        }
        $order = OrderModel::detail($this['order_id']);
        $productList = $order['product'];
        foreach ($productList as $product) {
            // 預售商品sku資訊
            $advanceProductSku = ProductSkuModel::detail($product['sku_source_id'], ['product']);
            $advanceProduct = $advanceProductSku['product'];
            // sku是否存在
            if (empty($advanceProductSku)) {
                $this->error = "很抱歉，商品 [{$product['product_name']}] sku已不存在，請重新下單";
                return false;
            }
            // 判斷商品是否下架
            if (empty($advanceProduct)) {
                $this->error = "很抱歉，商品 [{$product['product_name']}] 不存在或已刪除";
                return false;
            }
            // 付款減庫存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::PAYMENT && $product['total_num'] > $advanceProductSku['advance_stock']) {
                $this->error = "很抱歉，商品 [{$product['product_name']}] 庫存不足";
                return false;
            }
        }
        return true;
    }

    /**
     * 餘額支付標記訂單已支付
     */
    public function onPaymentByBalance($orderNo)
    {
        // 獲取訂單詳情
        $PaySuccess = new FrontPaySuccessService($orderNo);
        // 發起餘額支付
        return $PaySuccess->onPaySuccess(OrderPayTypeEnum::BALANCE);
    }

    /**
     * 待支付訂單詳情
     */
    public static function getPayDetail($orderNo, $pay_status)
    {
        $model = new static();
        $model = $model->where('trade_no', '=', $orderNo);
        if ($pay_status > 0) {
            $model = $model->where('pay_status', '=', 10);
        }
        return $model->with(['user', 'advance', 'orderM'])->find();
    }

    /**
     * 構建支付請求的引數
     */
    public static function onOrderPayment($user, $order, $payType, $pay_source)
    {
        //如果來源是h5,首次不處理，payH5再處理
        /*if ($pay_source == 'h5') {
            return [];
        }*/
        if ($payType == OrderPayTypeEnum::WECHAT) {
            return self::onPaymentByWechat($user, $order, $pay_source);
        }
        if ($payType == OrderPayTypeEnum::ALIPAY) {
            return self::onPaymentByAlipay($user, $order, $pay_source);
        }
        return [];
    }

    /**
     * 構建微信支付請求
     */
    protected static function onPaymentByWechat($user, $order, $pay_source)
    {
        return PaymentService::wechat(
            $user,
            $order['order_advance_id'],
            $order['trade_no'],
            $order['online_money'],
            OrderTypeEnum::FRONT,
            $pay_source
        );
    }

    /**
     * 構建支付寶請求
     */
    protected static function onPaymentByAlipay($user, $order, $pay_source)
    {
        return PaymentService::alipay(
            $user,
            $order['order_advance_id'],
            $order['trade_no'],
            $order['online_money'],
            OrderTypeEnum::FRONT,
            $pay_source
        );
    }

    /**
     * 訂單詳情
     */
    public static function getUserOrderDetail($order_id, $user_id)
    {
        $model = new static();
        $order = $model->with(['advance', 'orderM.product'])->where(['order_advance_id' => $order_id, 'user_id' => $user_id])->find();
        if (empty($order)) {
            throw new BaseException(['msg' => '訂單不存在']);
        }
        return $order;
    }

    /**
     * 建立新訂單
     */
    public function OrderPay($params, $order, $user)
    {
        $payType = $params['payType'];
        $payment = '';
        $online_money = 0;
        $order->save(['balance' => 0, 'online_money' => 0, 'trade_no' => $this->orderNo()]);
        if($order['pay_price'] == 0){
            $params['use_balance'] = 1;
        }
        // 餘額支付標記訂單已支付
        if ($params['use_balance'] == 1) {
            if ($user['balance'] >= $order['pay_price']) {
                $payType = 10;
                $order->save(['balance' => $order['pay_price']]);
                $this->onPaymentByBalance($order['trade_no']);
            } else {
                if ($payType <= 10) {
                    $this->error = '使用者餘額不足';
                    return false;
                }
                $online_money = round($order['pay_price'] - $user['balance'], 2);
                $order->save(['balance' => $user['balance'], 'online_money' => $online_money]);
            }
        } else {
            if ($payType <= 10) {
                $this->error = '請選擇支付方式';
                return false;
            }
            $online_money = $order['pay_price'];
            $order->save(['online_money' => $order['pay_price']]);
        }
        $online_money > 0 && $payment = self::onOrderPayment($user, $order, $payType, $params['pay_source']);

        $result['order_id'] = $order['order_id'];
        $result['payType'] = $payType;
        $result['payment'] = $payment;
        return $result;
    }

    /**
     * 獲取活動訂單
     * 已付款，未取消
     */
    public static function getPlusOrderNum($user_id, $product_id)
    {
        $model = new static();
        return $model->where('user_id', '=', $user_id)
            ->where('advance_product_id', '=', $product_id)
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->count();
    }

    /**
     * 取消訂單
     */
    public function cancel($user)
    {
        if ($this['money_return'] != 1) {
            $this->error = '訂單不允許取消';
            return false;
        }
        // 訂單取消事件
        return $this->transaction(function () use ($user) {
            // 訂單是否已支付
            $isPay = $this['pay_status']['value'] == OrderPayStatusEnum::SUCCESS;
            //主商品退回庫存
            ProductFactory::getFactory($this['orderM']['order_source'])->backProductStock($this['orderM']['product'], $isPay);
            $data['order_status'] = 20;
            // 未付款的訂單
            if ($isPay == true) {
                // 執行退款操作
                (new OrderRefundService)->execute($this);
                $data['refund_money'] = $this['pay_price'];
                $data['is_refund'] = 1;
            }
            // 更新訂單狀態
            $this['orderM']->save(['order_status' => 20]);
            return $this->save($data);
        });
    }

    /**
     * 設定錯誤資訊
     */
    protected function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 是否存在錯誤
     */
    public function hasError()
    {
        return !empty($this->error);
    }
}