<?php

namespace app\api\model\plus\giftpackage;

use app\api\service\order\PaymentService;
use app\api\service\order\paysuccess\type\GiftPaySuccessService;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderSourceEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\exception\BaseException;
use app\common\model\plus\giftpackage\Order as OrderModel;
use app\common\model\plus\giftpackage\GiftPackage as GiftPackageModel;
use app\api\model\order\OrderAddress as OrderAddress;
use app\api\model\order\Order as OrdersModel;
use app\api\service\user\UserService;
use app\api\model\order\OrderExtract as OrderExtractModel;
use app\api\model\product\Product as ProductModel;
use app\api\model\order\OrderProduct;
use app\api\model\plus\coupon\Coupon;
use app\api\model\product\Product;
use app\common\model\plus\giftpackage\Code as GiftCodeModel;
use app\common\service\product\factory\ProductFactory;

/**
 * 禮包購模型
 */
class Order extends OrderModel
{

    /**
     * 建立禮包購訂單
     * 返回訂單id
     */
    public function createOrder($user, $gift_package_id, $params)
    {
        $detail = GiftPackageModel::detail($gift_package_id);
        if ($detail['status'] == 1) {
            $this->error = '活動已終止';
            return false;
        }
        if ($detail['start_time']['value'] > time()) {
            $this->error = '活動還未開始';
            return false;
        }
        if ($detail['end_time']['value'] < time()) {
            $this->error = '活動已結束';
            return false;
        }
        if ($detail['is_times'] == 1) {
            $where = [
                'user_id' => $user['user_id'],
                'pay_status' => 20,
                'gift_package_id' => $gift_package_id,
            ];
            //統計購買數量
            $count = $this->where($where)->count('order_id');
            //判斷購買次數
            if ($count >= $detail['times']) {
                $this->error = '已超過限購數量';
                return false;
            }
        }
        //二維碼型別10，一碼，20，不同碼
        switch ($detail['code_type']) {
            case '10':
                //統計已購買數量
                $totalcount = $this->where('gift_package_id', '=', $gift_package_id)
                    ->where('pay_status', '=', 20)
                    ->where('order_status', '<>', 20)
                    ->count();
                if ($totalcount >= $detail['total_num']) {
                    $this->error = '禮包數量不足';
                    return false;
                }
                break;
            case '20':
                //查詢碼是否使用
                $code = $this->where('gift_package_id', '=', $gift_package_id)
                    ->where('code', '=', $params['code'])
                    ->count();
                if ($code > 0) {
                    $this->error = '優惠碼已使用';
                    return false;
                }
                break;
        }
        //選購商品數量
        $product_num = count(json_decode($params['product_ids'], true));
        if ($product_num > $detail['product_num']) {
            $this->error = '商品選購數量超過最大值' . $detail['product_num'];
            return false;
        }
        //判斷是否符合等級
        if ($detail['is_grade'] == 1 && !in_array($user['grade_id'], explode(',', $detail['grade_ids']))) {
            return false;
        }
        if ($params['delivery_type'] == DeliveryTypeEnum::EXPRESS) {
            if (empty($params['address']) || $params['address'] == 'null') {
                $this->error = '請先選擇收貨地址';
                return false;
            }
        }
        if ($params['delivery_type'] == DeliveryTypeEnum::EXTRACT) {
            $extract = json_decode($params['extract'], true);
            if ($extract['store_id'] == 0) {
                $this->error = '請先選擇自提門店';
                return false;
            }
            if (empty($extract['linkman']) || empty($extract['phone'])) {
                $this->error = '請填寫聯絡人和電話';
                return false;
            }
        }
        $code = isset($params['code']) ? $params['code'] : '';
        // 如果是通碼
        if ($detail['code_type'] == 10) {
            $code = GiftCodeModel::detail($gift_package_id)['code'];
        }
        //寫入order表
        $status = $this->save([
            'gift_package_id' => $gift_package_id,
            'order_no' => $this->orderNo(),
            'total_price' => $detail['money'],
            'order_price' => $detail['money'],
            'pay_price' => $detail['money'],
            'user_id' => $user['user_id'],
            'app_id' => self::$app_id,
            'product_ids' => $product_num > 0 ? $params['product_ids'] : '',
            'delivery_type' => $params['delivery_type'],
            'address' => isset($params['address']) ? $params['address'] : '',
            'extract' => isset($params['extract']) ? $params['extract'] : '',
            'coupon_ids' => $detail['coupon_ids'],
            'point' => $detail['point'],
            'code' => $code,
        ]);
        return $status;
    }

    /**
     * 餘額支付標記訂單已支付
     */
    public function onPaymentByBalance($orderNo, $data)
    {
        // 獲取訂單詳情
        $PaySuccess = new GiftPaySuccessService($orderNo);
        // 發起餘額支付
        return $PaySuccess->onPaySuccess(OrderPayTypeEnum::BALANCE, $data);
    }

    /**
     * 待支付訂單詳情
     */
    public static function getPayDetail($orderNo, $pay_status)
    {
        $model = new static();
        $model = $model->where('order_no', '=', $orderNo)->where('is_delete', '=', 0);
        if ($pay_status > 0) {
            $model = $model->where('pay_status', '=', 10);
        }
        return $model->with(['user'])->find();
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
            $order['order_id'],
            $order['order_no'],
            $order['online_money'],
            OrderTypeEnum::GIFT,
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
            $order['order_id'],
            $order['order_no'],
            $order['online_money'],
            OrderTypeEnum::GIFT,
            $pay_source
        );
    }

    //支付完成新增訂單
    public function addOrder($product_ids, $order_no, $app_id = null)
    {
        is_null($app_id) && $app_id = self::$app_id;
        $order = $this->where(['order_no' => $order_no])->find();
        $extract = json_decode($order['extract'], true);
        $productArray = json_decode($product_ids, true);
        // 訂單資料
        $data = [
            'user_id' => $order['user_id'],
            'order_no' => $this->orderNo(),
            'coupon_id' => 0,
            'coupon_money' => 0,
            'points_money' => 0,
            'points_num' => 0,
            'pay_price' => 0,
            'delivery_type' => $order['delivery_type'],
            'pay_type' => $order['pay_type']['value'],
            'buyer_remark' => '',
            'order_source' => OrderSourceEnum::GIFT,
            'activity_id' => $order['gift_package_id'],
            'points_bonus' => 0,
            'is_agent' => 0,
            'app_id' => $app_id,
            'pay_status' => 20,
            'pay_time' => time(),
            'pay_source' => 'wx',
            'extract_store_id' => $order['delivery_type'] == 20 ? $extract['store_id'] : 0,
        ];
        // 儲存訂單記錄
        $OrdersModel = new OrdersModel;
        $OrdersModel->save($data);
        $new_order_id = $OrdersModel->order_id;
        if ($order['delivery_type'] == 10) {
            $address = json_decode($order['address'], true);
            // 記錄收貨地址
            $this->saveOrderAddress($address, $new_order_id, $order['user_id'], $app_id);
        } elseif ($order['delivery_type'] == 20) {
            // 記錄自提資訊
            $this->saveOrderExtract($extract, $new_order_id, $order['user_id'], $app_id);
        }
        //新增商品
        $this->saveOrderProduct($order, $new_order_id, $productArray, $app_id);

    }

    /**
     * 儲存訂單商品資訊
     */
    private function saveOrderProduct($order, $status, $productArray, $app_id = null)
    {
        $goods = [];
        is_null($app_id) && $app_id = self::$app_id;
        $total_price = 0;
        foreach ($productArray as $value) {
            $product = ProductModel::detail($value['product_id']);
            // 商品sku資訊
            $product['product_sku'] = ProductModel::getProductSku($product, $value['product_sku_id']);
            // 訂單商品列表
            $goods[] = [
                'order_id' => $status,
                'user_id' => $order['user_id'],
                'app_id' => $app_id,
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'image_id' => $product['image'][0]['image_id'],
                'deduct_stock_type' => $product['deduct_stock_type'],
                'spec_type' => $product['spec_type'],
                'spec_sku_id' => $product['product_sku']['spec_sku_id'],
                'product_sku_id' => $product['product_sku']['product_sku_id'],
                'product_attr' => $product['product_sku']['product_attr'],
                'content' => $product['content'],
                'product_no' => $product['product_sku']['product_no'],
                'product_price' => $product['product_sku']['product_price'],
                'line_price' => $product['product_sku']['line_price'],
                'product_weight' => $product['product_sku']['product_weight'],
                'is_user_grade' => (int)$product['is_user_grade'],
                'grade_ratio' => $product['grade_ratio'] ? $product['grade_ratio'] : 0,
                'grade_product_price' => isset($product['grade_product_price']) ? $product['grade_product_price'] : 0,
                'grade_total_money' => 0,
                'coupon_money' => 0,
                'points_money' => isset($product['points_money']) ? $product['points_money'] : 0,
                'points_num' => isset($product['points_num']) ? $product['points_num'] : 0,
                'points_bonus' => 0,
                'total_num' => 1,
                'total_price' => $product['spec_type'] == 10 ? $product['product_price'] : $product['product_sku']['product_price'],
                'total_pay_price' => 0,
                'is_ind_agent' => $product['is_ind_agent'],
                'agent_money_type' => $product['agent_money_type'],
                'first_money' => 0,
                'second_money' => 0,
                'third_money' => 0,
                'fullreduce_money' => 0,
                'product_source_id' => isset($product['product_source_id']) ? $product['product_source_id'] : 0,
                'sku_source_id' => isset($product['sku_source_id']) ? $product['sku_source_id'] : 0,
                'bill_source_id' => isset($product['bill_source_id']) ? $product['bill_source_id'] : 0,
            ];
            $total_price += $product['product_sku']['product_price'];
        }
        // 更新商品庫存 (針對下單減庫存的商品)
        ProductFactory::getFactory(OrderSourceEnum::GIFT)->updateProductStock($goods);
        // 更新商品庫存、銷量
        ProductFactory::getFactory(OrderSourceEnum::GIFT)->updateStockSales($goods);
        (new OrdersModel)->where('order_id', '=', $status)->update(['total_price' => $total_price, 'order_price' => $total_price]);
        $model = new OrderProduct();
        return $model->saveAll($goods);
    }

    /**
     * 記錄收貨地址
     */
    private function saveOrderAddress($address, $order_id, $user_id, $app_id = null)
    {
        $model = new OrderAddress();
        is_null($app_id) && $app_id = self::$app_id;
        return $model->save([
            'order_id' => $order_id,
            'user_id' => $user_id,
            'app_id' => $app_id,
            'name' => $address['name'],
            'phone' => $address['phone'],
            'province_id' => $address['province_id'],
            'city_id' => $address['city_id'],
            'region_id' => $address['region_id'],
            'detail' => $address['detail'],
        ]);
    }

    /**
     * 儲存上門自提聯絡人
     */
    private function saveOrderExtract($extract, $order_id, $user_id, $app_id = null)
    {
        $OrderExtract = new OrderExtractModel;
        // 記憶上門自提聯絡人(快取)，用於下次自動填寫
        UserService::setLastExtract($user_id, trim($extract['linkman']), trim($extract['phone']));
        is_null($app_id) && $app_id = self::$app_id;
        // 儲存上門自提聯絡人(資料庫)
        return $OrderExtract->save([
            'order_id' => $order_id,
            'linkman' => trim($extract['linkman']),
            'phone' => trim($extract['phone']),
            'user_id' => $user_id,
            'app_id' => $app_id,
        ]);
    }

    //購買列表
    public function getList($user, $data)
    {
        $list = $this->with(['gift'])
            ->field('order_no,pay_price,pay_type,pay_time,coupon_ids,product_ids,gift_package_id,point')
            ->where('user_id', '=', $user['user_id'])
            ->where('pay_status', '=', 20)
            ->order('create_time', 'desc')
            ->paginate($data);
        foreach ($list as $key => &$value) {
            $value['product_num'] = $value['product_ids'] ? count(json_decode($value['product_ids'])) : 0;
            $coupon_num = 0;
            if ($value['coupon_ids']) {
                $coupon_ids = json_decode($value['coupon_ids'], true);
                foreach ($coupon_ids as $k => $v) {
                    $coupon_num += $v['coupon_num'];
                }
            }
            $value['coupon_num'] = $coupon_num;
        }
        return $list;
    }

    //訂單詳情
    public function orderDetail($order_no)
    {
        $detail = $this->where('order_no', '=', $order_no)->find();
        if ($detail['coupon_ids']) {
            $Coupon = new Coupon();
            $coupon = json_decode($detail['coupon_ids'], true);
            foreach ($coupon as $key => &$value) {
                $couponInfo = $Coupon->getCouponInfo($value['coupon_id']);
                $value['name'] = $couponInfo['name'];
                $value['reduce_price'] = $couponInfo['reduce_price'];
                $value['expire_type'] = $couponInfo['expire_type'];
                $value['expire_day'] = $couponInfo['expire_day'];
                $value['start_time'] = $couponInfo['start_time'];
                $value['end_time'] = $couponInfo['end_time'];
            }
            $detail['coupon_list'] = $coupon;
        }
        if ($detail['product_ids']) {
            $ProductModel = new Product();
            $product = $ProductModel->getProductList($detail['product_ids']);
            $detail['product_list'] = $product->toArray();
        }
        $detail['address'] = $detail['address'] ? json_decode($detail['address'], true) : '';
        $detail['extract'] = $detail['extract'] ? json_decode($detail['extract'], true) : '';
        return $detail;
    }

    /**
     * 訂單詳情
     */
    public static function getUserOrderDetail($order_id, $user_id)
    {
        $model = new static();
        $order = $model->where(['order_id' => $order_id, 'user_id' => $user_id])->find();
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
        $order->save(['balance' => 0, 'online_money' => 0, 'order_no' => $this->orderNo()]);
        if ($order['pay_price'] == 0) {
            $params['use_balance'] = 1;
            $payType = 10;
        }
        // 餘額支付標記訂單已支付
        if ($params['use_balance'] == 1) {
            if ($user['balance'] >= $order['pay_price']) {
                $payType = 10;
                $order->save(['balance' => $order['pay_price']]);
                $data['attach'] = '{"pay_source":"' . $params['pay_source'] . '"}';
                $this->onPaymentByBalance($order['order_no'], $data);
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
}