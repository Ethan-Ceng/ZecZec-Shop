<?php

namespace app\api\service\order\settled;

use app\api\model\order\Order as OrderModel;
use app\api\model\order\OrderAdvance as OrderAdvanceModel;
use app\api\model\order\OrderProduct;
use app\api\model\order\OrderAddress as OrderAddress;
use app\api\model\plus\coupon\UserCoupon as UserCouponModel;
use app\api\model\product\Category;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderSourceEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\settings\Setting as SettingModel;
use app\api\service\points\PointsDeductService;
use app\api\service\coupon\ProductDeductService;
use app\common\model\store\Store as StoreModel;
use app\api\service\user\UserService;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\library\helper;
use app\common\service\delivery\ExpressService;
use app\common\service\BaseService;
use app\common\service\product\factory\ProductFactory;
use app\api\model\shop\FullReduce as FullReduceModel;
use app\api\service\fullreduce\FullDeductService;
use app\api\model\product\Product as ProductModel;
use think\response\Json;

/**
 * 預售訂單結算服務基類
 */
abstract class AdvanceSettledService extends BaseService
{
    /* $model OrderModel 訂單模型 */
    public $model;

    //定金訂單模型
    public $advanceModel;

    // 當前應用id
    protected $app_id;

    protected $user;

    // 訂單結算商品列表
    protected $productList = [];

    protected $params;

    /**
     * 訂單結算的規則
     * 主商品預設規則
     */
    protected $settledRule = [
        'is_coupon' => true,        // 優惠券抵扣
        'force_points' => false,     // 強制使用積分，積分兌換
        'is_user_grade' => true,     // 會員等級折扣
        'is_agent' => true,     // 商品是否開啟分銷,最終還是支付成功後判斷分銷活動是否開啟
        'is_point' => true,
    ];

    /**
     * 訂單結算資料
     */
    protected $orderData = [];
    /**
     * 訂單來源
     */
    protected $orderSource;

    /**
     * 建構函式
     */
    public function __construct($user, $productList, $params)
    {
        $this->model = new OrderModel;
        $this->advanceModel = new OrderAdvanceModel;
        $this->app_id = OrderModel::$app_id;
        $this->user = $user;
        $this->productList = $productList;
        $this->params = $params;
    }

    /**
     * 定金訂單確認-結算臺
     */
    public function paySettlement()
    {
        // 整理訂單資料
        $this->orderData = $this->getOrderData();
        // 驗證商品狀態, 是否允許購買
        $this->validateProductList();
        // 訂單商品總數量
        $orderTotalNum = helper::getArrayColumnSum($this->productList, 'total_num');
        // 設定訂單商品定金總金額
        $this->setOrderTotalFrontPrice();
        // 計算訂單商品的實際付款金額
        $this->setOrderPayProductPayPrice();
        // 設定預設配送方式
        !$this->params['delivery'] && $this->params['delivery'] = current(SettingModel::getItem('store')['delivery_type']);
        // 處理配送方式
        if ($this->params['delivery'] == DeliveryTypeEnum::EXPRESS) {

        } elseif ($this->params['delivery'] == DeliveryTypeEnum::EXTRACT) {
            $this->params['store_id'] > 0 && $this->orderData['extract_store'] = StoreModel::detail($this->params['store_id']);
        }
        // 返回訂單資料
        return array_merge([
            'product_list' => array_values($this->productList),   // 商品資訊
            'order_total_num' => $orderTotalNum,        // 商品總數量
        ], $this->settledRule, $this->orderData);
    }


    /**
     * 驗證訂單商品的狀態
     * @return bool
     */
    abstract function validateProductList();


    /**
     * 建立定金訂單
     */
    public function createPayOrder($order)
    {
        // 表單驗證
        if (!$this->validateOrderForm($order)) {
            return false;
        }
        // 建立新的訂單
        $this->model->transaction(function () use ($order) {
            // 建立訂單事件
            return $this->createPayOrderEvent($order);
        });
        return $this->advanceModel['order_advance_id'];
    }

    /**
     * 設定訂單的商品定金金額
     */
    private function setOrderTotalFrontPrice()
    {
        // 訂單商品的總金額
        $this->orderData['order_total_price'] = helper::number2(helper::getArrayColumnSum($this->productList, 'total_price'));
        //尾款立減金額
        $this->orderData['reduce_money'] = helper::number2(helper::getArrayColumnSum($this->productList, 'reduce_money'));
        // 訂單商品的支付金額
        $this->orderData['order_total_pay_price'] = helper::number2(helper::bcsub($this->orderData['order_total_price'], $this->orderData['reduce_money']));
        // 訂單商品的定金總金額
        $this->orderData['order_total_front_price'] = helper::number2(helper::getArrayColumnSum($this->productList, 'total_front_price'));
    }

    /**
     * 計算訂單商品的實際付款金額
     */
    private function setOrderPayProductPayPrice()
    {
        // 商品總價 - 優惠抵扣
        foreach ($this->productList as &$product) {
            // 減去優惠券抵扣金額
            $value = $product['total_price'];
            // 減去立減金額
            if ($this->orderData['reduce_money'] > 0) {
                $value = helper::bcsub($value, $this->orderData['reduce_money']);
            }
            $product['total_pay_price'] = helper::number2($value);
        }

        return true;
    }

    /**
     * 整理訂單資料(結算臺初始化)
     */
    private function getOrderData()
    {
        // 系統支援的配送方式 (後臺設定)
        $deliveryType = SettingModel::getItem('store')['delivery_type'];
        sort($deliveryType);
        // 積分設定
        $pointsSetting = SettingModel::getItem('points');
        if ($this->productList[0]['is_virtual'] == 1) {
            $delivery = 30;
        } else {
            $delivery = $this->params['delivery'] > 0 ? $this->params['delivery'] : $deliveryType[0];
        }
        return [
            // 配送型別
            'delivery' => $delivery,
            // 預設地址
            'address' => $this->user['address_default'],
            // 是否存在收貨地址
            'exist_address' => $this->user['address_id'] > 0,
            // 配送費用
            'express_price' => 0.00,
            // 當前使用者收貨城市是否存在配送規則中
            'intra_region' => true,
            // 自提門店資訊
            'extract_store' => [],
            // 是否允許使用積分抵扣
            'is_point' => $this->settledRule['is_point'],
            // 是否使用積分抵扣
            'is_use_points' => $this->params['is_use_points'],
            // 支付方式
            'pay_type' => isset($this->params['pay_type']) ? $this->params['pay_type'] : OrderPayTypeEnum::WECHAT,
            // 系統設定
            'setting' => [
                'delivery' => $deliveryType,     // 支援的配送方式
                'points_name' => $pointsSetting['points_name'],      // 積分名稱
                'points_describe' => $pointsSetting['describe'],     // 積分說明
            ],
            // 記憶的自提聯絡方式
            'last_extract' => UserService::getLastExtract($this->user['user_id']),
            'deliverySetting' => $deliveryType,
        ];
    }

    /**
     * 表單驗證 (訂單提交)
     */
    private function validateOrderForm($order)
    {
        if ($order['delivery'] == DeliveryTypeEnum::EXPRESS) {
            if (empty($order['address'])) {
                $this->error = '請先選擇收貨地址';
                return false;
            }
        }
        if ($order['delivery'] == DeliveryTypeEnum::EXTRACT) {
            if (empty($order['extract_store'])) {
                $this->error = '請先選擇自提門店';
                return false;
            }
            if (empty($this->params['linkman']) || empty($this->params['phone'])) {
                $this->error = '請填寫聯絡人和電話';
                return false;
            }
        }
        //如果是積分兌換，判斷使用者積分是否足夠
        if ($this->settledRule['force_points']) {
            if ($this->user['points'] < $order['points_num']) {
                $this->error = '使用者積分不足，無法使用積分兌換';
                return false;
            }
        }
        return true;
    }

    /**
     * 建立定金訂單事件
     */
    private function createPayOrderEvent($order)
    {
        // 新增訂單記錄
        $status = $this->addPay($order, $this->params['remark']);

        if ($order['delivery'] == DeliveryTypeEnum::EXPRESS) {
            // 記錄收貨地址
            $this->saveOrderAddress($order['address'], $status);
        } elseif ($order['delivery'] == DeliveryTypeEnum::EXTRACT) {
            // 記錄自提資訊
            $this->saveOrderExtract($this->params['linkman'], $this->params['phone']);
        }
        // 儲存訂單商品資訊
        $this->saveOrderPayProduct($order, $status);

        // 儲存定金訂單資訊
        $this->saveOrderAdvancePay($order, $status);

        // 更新商品庫存 (針對下單減庫存的商品)
        ProductFactory::getFactory($this->orderSource['source'])->updateAdvanceProductStock($order['product_list']);

        return $status;
    }

    /**
     * 新增訂單記錄
     */
    private function addPay($order, $remark = '')
    {
        // 訂單資料
        $data = [
            'user_id' => $this->user['user_id'],
            'order_no' => $this->model->orderNo(),
            'trade_no' => $this->model->orderNo(),
            'total_price' => $order['order_total_price'],
            'order_price' => $order['order_total_pay_price'],
            'pay_price' => $order['order_total_pay_price'],
            'delivery_type' => $order['delivery'],
            'pay_type' => $order['pay_type'],
            'buyer_remark' => trim($remark),
            'order_source' => $this->orderSource['source'],
            'activity_id' => isset($this->orderSource['activity_id']) ? $this->orderSource['activity_id'] : 0,
            'virtual_auto' => $order['product_list'][0]['virtual_auto'],
            'is_agent' => $this->settledRule['is_agent'] ? 1 : 0,
            'app_id' => $this->app_id,

        ];
        if ($order['delivery'] == DeliveryTypeEnum::EXPRESS) {

        } elseif ($order['delivery'] == DeliveryTypeEnum::EXTRACT) {
            $data['extract_store_id'] = $order['extract_store']['store_id'];
        }
        // 儲存訂單記錄
        $this->model->save($data);
        return $this->model['order_id'];
    }

    /**
     * 新增定金訂單記錄
     */
    private function saveOrderAdvancePay($order, $order_id)
    {
        $advance = $order['product_list'][0]['advance'];
        $config = SettingModel::getItem('advance');
        // 訂單資料
        $data = [
            'order_id' => $order_id,
            'user_id' => $this->user['user_id'],
            'order_no' => $this->advanceModel->orderNo(),
            'trade_no' => $this->advanceModel->orderNo(),
            'pay_price' => $order['order_total_front_price'],
            'end_time' => $advance['end_time'],
            'pay_type' => $order['pay_type'],
            'advance_product_id' => $advance['advance_product_id'],
            'advance_product_sku_id' => $order['product_list'][0]['advance_sku']['advance_product_sku_id'],
            'money_return' => $config['money_return'],
            'reduce_money' => $order['reduce_money'],
            'app_id' => $this->app_id,
        ];
        // 結束支付時間
        $closeMinters = $config['end_time'];
        $config['end_time'] > 0 && $data['pay_end_time'] = time() + ($closeMinters * 60);
        // 儲存訂單記錄
        $this->advanceModel->save($data);
        return $this->advanceModel['order_advance_id'];
    }


    /**
     * 記錄收貨地址
     */
    private function saveOrderAddress($address, $order_id)
    {
        $model = new OrderAddress();
        if ($address['region_id'] == 0 && !empty($address['district'])) {
            $address['detail'] = $address['district'] . ' ' . $address['detail'];
        }
        return $model->save([
            'order_id' => $order_id,
            'user_id' => $this->user['user_id'],
            'app_id' => $this->app_id,
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
    private function saveOrderExtract($linkman, $phone)
    {
        // 記憶上門自提聯絡人(快取)，用於下次自動填寫
        UserService::setLastExtract($this->model['user_id'], trim($linkman), trim($phone));
        // 儲存上門自提聯絡人(資料庫)
        return $this->model->extract()->save([
            'linkman' => trim($linkman),
            'phone' => trim($phone),
            'user_id' => $this->model['user_id'],
            'app_id' => $this->app_id,
        ]);
    }

    /**
     * 儲存訂單商品資訊
     */
    private function saveOrderPayProduct($order, $status)
    {
        // 訂單商品列表
        $productList = [];
        foreach ($order['product_list'] as $product) {
            $item = [
                'order_id' => $status,
                'user_id' => $this->user['user_id'],
                'app_id' => $this->app_id,
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
                'product_price' => $product['product_price'],
                'line_price' => $product['product_sku']['line_price'],
                'product_weight' => $product['product_sku']['product_weight'],
                'virtual_content' => $product['virtual_content'],
                'total_num' => $product['total_num'],
                'total_price' => $product['total_price'],
                'total_pay_price' => $product['total_pay_price'],
                'is_agent' => $product['is_agent'],
                'is_ind_agent' => $product['is_ind_agent'],
                'agent_money_type' => $product['agent_money_type'],
                'first_money' => $product['first_money'],
                'second_money' => $product['second_money'],
                'third_money' => $product['third_money'],
            ];
            // 記錄訂單商品來源id
            $item['product_source_id'] = isset($product['product_source_id']) ? $product['product_source_id'] : 0;
            // 記錄訂單商品sku來源id
            $item['sku_source_id'] = isset($product['sku_source_id']) ? $product['sku_source_id'] : 0;
            $productList[] = $item;
        }
        $model = new OrderProduct();
        return $model->saveAll($productList);
    }
}