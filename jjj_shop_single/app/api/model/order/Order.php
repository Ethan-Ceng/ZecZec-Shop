<?php

namespace app\api\model\order;

use app\api\model\product\Product as ProductModel;
use app\api\service\order\paysuccess\type\MasterPaySuccessService;
use app\api\service\order\PaymentService;
use app\api\model\settings\Setting as SettingModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderSourceEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\enum\order\OrderStatusEnum;
use app\common\exception\BaseException;
use app\common\service\order\OrderCompleteService;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\library\helper;
use app\common\model\order\Order as OrderModel;
use app\api\service\order\checkpay\CheckPayFactory;
use app\common\service\product\factory\ProductFactory;
use app\common\model\plus\coupon\UserCoupon as UserCouponModel;
use app\common\service\order\OrderRefundService;

/**
 * 普通訂單模型
 */
class Order extends OrderModel
{
    /**
     * 隱藏欄位
     * @var array
     */
    protected $hidden = [
        'update_time'
    ];

    /**
     * 訂單支付事件
     * 返回0，失敗 1，需要繼續支付 2，無需繼續支付，餘額支付成功
     */
    public function onPay()
    {
        // 判斷訂單狀態
        $checkPay = CheckPayFactory::getFactory($this['order_source']);
        if (!$checkPay->checkOrderStatus($this)) {
            $this->error = $checkPay->getError();
            return false;
        }
        return true;
    }

    /**
     * 使用者中心訂單列表
     */
    public function getList($user_id, $type, $params)
    {
        // 篩選條件
        $model = $this;
        $filter = [];
        // 訂單資料型別
        switch ($type) {
            case 'all':
                break;
            case 'payment';
                $filter['pay_status'] = OrderPayStatusEnum::PENDING;
                $filter['order_status'] = 10;
                break;
            case 'delivery';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['order_status'] = 10;
                $model = $model->where('delivery_status', '<>', 20);
                break;
            case 'received';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'comment';
                $filter['is_comment'] = 0;
                $filter['order_status'] = 30;
                break;
            case 'cancel';
                $filter['order_status'] = 20;
                break;
        }
        $list = $model->with(['product.image', 'advance'])
            ->where('user_id', '=', $user_id)
            ->where($filter)
            ->where('is_delete', '=', 0)
            ->order(['create_time' => 'desc'])
            ->paginate($params);
        foreach ($list as &$item) {
            if ($item['pay_status']['value'] == 10 && $item['order_status']['value'] != 20 && $item['pay_end_time'] != 0) {
                $item['pay_end_time_format'] = $this->formatPayEndTime($item['pay_end_time'] - time());
            } else {
                $item['pay_end_time_format'] = '';
            }
        }
        return $list;
    }

    /**
     * 使用者中心拼團訂單列表
     */
    public function getAssembleList($user_id, $params)
    {
        // 篩選條件
        $filter = [];
        // 訂單資料型別
        switch ($params['type']) {
            case '10'://所有訂單
                break;
            case '20';//待支付訂單
                $filter['pay_status'] = OrderPayStatusEnum::PENDING;
                $filter['order_status'] = 10;
                break;
            case '30';//拼團中
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['assemble_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case '40';//拼團成功
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['assemble_status'] = 20;
                break;
            case '50';//拼團失敗
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['assemble_status'] = 30;
                break;
        }
        $list = $this->with(['product.image', 'advance'])
            ->where('user_id', '=', $user_id)
            ->where('order_source', '=', OrderSourceEnum::ASSEMBLE)
            ->where($filter)
            ->where('is_delete', '=', 0)
            ->order(['create_time' => 'desc'])
            ->paginate($params);
        foreach ($list as &$item) {
            if ($item['pay_status']['value'] == 10 && $item['order_status']['value'] != 20 && $item['pay_end_time'] != 0) {
                $item['pay_end_time_format'] = $this->formatPayEndTime($item['pay_end_time'] - time());
            } else {
                $item['pay_end_time_format'] = '';
            }
        }
        return $list;
    }

    /**
     * 確認收貨
     */
    public function receipt()
    {
        // 驗證訂單是否合法
        // 條件1: 訂單必須已發貨
        // 條件2: 訂單必須未收貨
        if ($this['delivery_status']['value'] != 20 || $this['receipt_status']['value'] != 10) {
            $this->error = '該訂單不合法';
            return false;
        }
        return $this->transaction(function () {
            // 更新訂單狀態
            $status = $this->save([
                'receipt_status' => 20,
                'receipt_time' => time(),
                'order_status' => 30
            ]);
            // 執行訂單完成後的操作
            $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
            $OrderCompleteService->complete([$this], static::$app_id);
            return $status;
        });
    }

    /**
     * 立即購買：獲取訂單商品列表
     */
    public static function getOrderProductListByNow($params)
    {
        // 商品詳情
        $product = ProductModel::detail($params['product_id']);
        // 商品sku資訊 spec_sku_id
        $product['product_sku'] = ProductModel::getProductSku($product, $params['product_sku_id']);
        if (!$product['product_sku']) {
            throw new BaseException(['msg' => '很抱歉，商品規格不存在']);
        }
        // 商品列表
        $productList = [$product->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($productList as &$item) {
            // 商品單價
            $item['product_price'] = $item['product_sku']['product_price'];
            // 商品購買數量
            $item['total_num'] = $params['product_num'];
            $item['spec_sku_id'] = $item['product_sku']['spec_sku_id'];
            // 商品購買總金額
            $item['total_price'] = helper::bcmul($item['product_price'], $params['product_num']);
        }
        return $productList;
    }

    /**
     * 獲取訂單總數
     */
    public function getCount($user, $type = 'all')
    {
        if ($user === false) {
            return false;
        }
        // 篩選條件
        $filter = [];
        // 訂單資料型別
        switch ($type) {
            case 'all':
                break;
            case 'payment';
                $filter['pay_status'] = OrderPayStatusEnum::PENDING;
                break;
            case 'delivery';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'received';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                break;
            case 'comment';
                $filter['order_status'] = 30;
                $filter['is_comment'] = 0;
                break;
        }
        return $this->where('user_id', '=', $user['user_id'])
            ->where('order_status', '<>', 20)
            ->where($filter)
            ->where('is_delete', '=', 0)
            ->count();
    }

    /**
     * 取消訂單
     */
    public function cancel($user)
    {
        if ($this['delivery_status']['value'] == 20) {
            $this->error = '已發貨訂單不可取消';
            return false;
        }
        if ($this['order_status']['value'] != 10) {
            $this->error = '訂單狀態不允許取消';
            return false;
        }
        //進行中的拼團訂單不能取消
        if ($this['order_source'] == OrderSourceEnum::ASSEMBLE) {
            if ($this['assemble_status'] == 10) {
                $this->error = '訂單正在拼團，到期後如果訂單未拼團成功將自動退款';
                return false;
            }
        }
        // 訂單取消事件
        return $this->transaction(function () use ($user) {
            // 訂單是否已支付
            $isPay = $this['pay_status']['value'] == OrderPayStatusEnum::SUCCESS;
            // 未付款的訂單
            if ($isPay == false) {
                //主商品退回庫存
                ProductFactory::getFactory($this['order_source'])->backProductStock($this['product'], $isPay);
                // 回退使用者優惠券
                $this['coupon_id'] > 0 && UserCouponModel::setIsUse($this['coupon_id'], false);
                // 回退使用者積分
                $describe = "訂單取消：{$this['order_no']}";
                $this['points_num'] > 0 && $user->setIncPoints($this['points_num'], $describe);
                //判斷是否為預售訂單
                if ($this['order_source'] == OrderSourceEnum::ADVANCE) {
                    if ($this['advance']['money_return'] == 1) {//預售訂單退定金
                        if ((new OrderRefundService)->execute($this['advance'])) {
                            // 更新訂單狀態
                            $this['advance']->save([
                                'is_refund' => 1,
                                'refund_money' => $this['advance']['pay_price'],
                            ]);
                        }
                    }
                    $this['advance']->save(['order_status' => 20]);
                }
            }

            // 更新訂單狀態
            return $this->save(['order_status' => $isPay ? OrderStatusEnum::APPLY_CANCEL : OrderStatusEnum::CANCELLED]);
        });
    }

    /**
     * 訂單詳情
     */
    public static function getUserOrderDetail($order_id, $user_id)
    {
        $model = new static();
        $order = $model->where(['order_id' => $order_id, 'user_id' => $user_id])->with(['product' => ['image', 'refund'], 'address', 'express', 'extractStore', 'advance'])->find();
        if (empty($order)) {
            throw new BaseException(['msg' => '訂單不存在']);
        }
        return $order;
    }

    /**
     * 訂單詳情
     */
    public static function getOrderDetail($order_id, $user_id)
    {
        $model = new static();
        $order = $model->where(['order_id' => $order_id, 'user_id' => $user_id])->with(['product' => ['image', 'refund'], 'address', 'express', 'extractStore', 'advance'])->find();
        if (empty($order)) {
            throw new BaseException(['msg' => '訂單不存在']);
        }
        foreach ($order['product'] as &$item) {
            $refund = (new OrderRefund())->allowRefund($order_id, $item['order_product_id']);
            $item['allowRefund'] = $refund ? true : false;
        }
        return $order;
    }

    /**
     * 餘額支付標記訂單已支付
     */
    public function onPaymentByBalance($orderNo, $data)
    {
        // 獲取訂單詳情
        $PaySuccess = new MasterPaySuccessService($orderNo);
        // 發起餘額支付
        $status = $PaySuccess->onPaySuccess(OrderPayTypeEnum::BALANCE, $data);
        if (!$status) {
            $this->error = $PaySuccess->getError();
        }
        return $status;
    }

    /**
     * 構建微信支付請求
     */
    protected static function onPaymentByWechat($user, $order, $pay_source)
    {
        return PaymentService::wechat(
            $user,
            $order['order_id'],
            $order['trade_no'],
            $order['online_money'],
            OrderTypeEnum::MASTER,
            $pay_source
        );
    }

    /**
     * 待支付訂單詳情
     */
    public static function getPayDetail($orderNo, $pay_status)
    {
        $model = new static();
        $model = $model->where('trade_no', '=', $orderNo)->where('is_delete', '=', 0);
        if ($pay_status > 0) {
            $model = $model->where('pay_status', '=', 10);
        }
        return $model->with(['product', 'user'])->find();
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
     * 判斷當前訂單是否允許核銷
     */
    public function checkExtractOrder($order)
    {
        if (
            $order['pay_status']['value'] == OrderPayStatusEnum::SUCCESS
            && $order['delivery_type']['value'] == DeliveryTypeEnum::EXTRACT
            && $order['delivery_status']['value'] == 10
        ) {
            return true;
        }
        $this->setError('該訂單不能被核銷');
        return false;
    }

    /**
     * 當前訂單是否允許申請售後
     */
    public function isAllowRefund()
    {
        // 必須是已發貨的訂單
        if ($this['delivery_status']['value'] != 20) {
            return false;
        }
        // 允許申請售後期限(天)
        $refundDays = SettingModel::getItem('trade')['order']['refund_days'];
        // 不允許售後
        if ($refundDays == 0) {
            return false;
        }
        // 當前時間超出允許申請售後期限
        if (
            $this['receipt_status'] == 20
            && time() > ($this['receipt_time'] + ((int)$refundDays * 86400))
        ) {
            return false;
        }
        return true;
    }

    /**
     * 獲取活動訂單
     * 已付款，未取消
     */
    public static function getPlusOrderNum($user_id, $product_id, $order_source)
    {
        $model = new static();
        return $model->alias('order')->where('order.user_id', '=', $user_id)
            ->join('order_product', 'order_product.order_id = order.order_id', 'left')
            ->where('order_product.product_source_id', '=', $product_id)
            ->where('order.order_source', '=', $order_source)
            ->where('order.order_status', '<>', 20)
            ->count();
    }

    /**
     * 構建支付寶請求
     */
    protected static function onPaymentByAlipay($user, $order, $pay_source)
    {
        return PaymentService::alipay(
            $user,
            $order['order_id'],
            $order['trade_no'],
            $order['online_money'],
            OrderTypeEnum::MASTER,
            $pay_source
        );
    }

    /**
     * 主訂單購買的數量
     * 未取消的訂單
     */
    public static function getHasBuyOrderNum($user_id, $product_id)
    {
        $model = new static();
        return $model->alias('order')->where('order.user_id', '=', $user_id)
            ->join('order_product', 'order_product.order_id = order.order_id', 'left')
            ->where('order_product.product_id', '=', $product_id)
            ->where('order.order_source', '=', OrderSourceEnum::MASTER)
            ->where('order.order_status', '<>', 20)
            ->sum('total_num');
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
        if ($order['pay_price'] == 0) {
            $params['use_balance'] = 1;
        }
        // 餘額支付標記訂單已支付
        if ($params['use_balance'] == 1) {
            if ($user['balance'] >= $order['pay_price']) {
                $payType = 10;
                $order->save(['balance' => $order['pay_price']]);
                $data['attach'] = '{"pay_source":"' . $params['pay_source'] . '"}';
                $this->onPaymentByBalance($order['trade_no'], $data);
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