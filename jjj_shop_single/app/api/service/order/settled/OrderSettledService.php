<?php

namespace app\api\service\order\settled;

use app\api\model\order\Order as OrderModel;
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

/**
 * 訂單結算服務基類
 */
abstract class OrderSettledService extends BaseService
{
    /* $model OrderModel 訂單模型 */
    public $model;

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
        'is_point' => true,        // 是否使用積分抵扣，系統設定
        'force_points' => false,     // 強制使用積分，積分兌換
        'is_user_grade' => true,     // 會員等級折扣
        'is_agent' => true,     // 商品是否開啟分銷,最終還是支付成功後判斷分銷活動是否開啟
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
        $this->app_id = OrderModel::$app_id;
        $this->user = $user;
        $this->productList = $productList;
        $this->params = $params;
    }

    /**
     * 訂單確認-結算臺
     */
    public function settlement()
    {
        // 整理訂單資料
        $this->orderData = $this->getOrderData();
        // 驗證商品狀態, 是否允許購買
        $this->validateProductList();
        // 訂單商品總數量
        $orderTotalNum = helper::getArrayColumnSum($this->productList, 'total_num');
        // 設定訂單商品會員折扣價
        $this->setOrderGrade();
        // 設定訂單商品總金額(不含優惠折扣)
        $this->setOrderTotalPrice();
        // 先計算商品滿減
        $this->setProductReduce();
        // 自動滿減
        $reduce = FullReduceModel::getReductList($this->orderData['order_total_price'], $orderTotalNum);
        // 設定滿減
        $this->orderData['reduce'] = $reduce;
        $reduce && $this->setOrderFullreduceMoney($reduce);
        // 當前使用者可用的優惠券列表
        $couponList = $this->getUserCouponList($this->orderData['order_total_price']);
        foreach ($couponList as $i => $coupon) {
            if (!$this->checkCouponCanUse($coupon)) {
                unset($couponList[$i]);
            }
        }
        // 計算優惠券抵扣
        $this->setOrderCouponMoney($couponList, $this->params['coupon_id']);
        // 計算訂單商品的實際付款金額
        $this->setOrderProductPayPrice();
        // 計算可用積分抵扣
        $this->setOrderPoints();
        // 處理配送方式
        if ($this->orderData['delivery'] == DeliveryTypeEnum::EXPRESS) {
            $this->setOrderExpress();
        } elseif ($this->orderData['delivery'] == DeliveryTypeEnum::EXTRACT) {
            $this->params['store_id'] > 0 && $this->orderData['extract_store'] = StoreModel::detail($this->params['store_id']);
        }

        // 計算訂單最終金額
        $this->setOrderPayPrice();
        // 計算訂單積分贈送數量
        $this->setOrderPointsBonus();
        $product_list = $this->orderSource['source'] == OrderSourceEnum::ADVANCE ? $this->productList : array_values($this->productList);
        // 返回訂單資料
        return array_merge([
            'product_list' => $product_list,   // 商品資訊
            'order_total_num' => $orderTotalNum,        // 商品總數量
            'coupon_list' => $couponList
        ], $this->orderData, $this->settledRule);
    }

    /**
     * 驗證訂單商品的狀態
     * @return bool
     */
    abstract function validateProductList();

    /**
     * 建立新訂單
     */
    public function createOrder($order)
    {
        // 表單驗證
        if (!$this->validateOrderForm($order)) {
            return false;
        }
        // 建立新的訂單
        $status = $this->model->transaction(function () use ($order) {
            // 建立訂單事件
            return $this->createOrderEvent($order);
        });
        return $status;
    }

    /**
     * 設定訂單的商品總金額(不含優惠折扣)
     */
    private function setOrderTotalPrice()
    {
        // 訂單商品的總金額(不含優惠券折扣)
        $this->orderData['order_total_price'] = helper::number2(helper::getArrayColumnSum($this->productList, 'total_price'));
    }

    /**
     * 當前使用者可用的優惠券列表
     */
    private function getUserCouponList($orderTotalPrice)
    {
        // 是否開啟優惠券折扣
        if (!$this->settledRule['is_coupon']) {
            return [];
        }
        return UserCouponModel::getUserCouponList($this->user['user_id'], $orderTotalPrice);
    }

    /**
     * 設定訂單優惠券抵扣資訊
     */
    private function setOrderCouponMoney($couponList, $couponId)
    {
        // 設定預設資料：訂單資訊
        helper::setDataAttribute($this->orderData, [
            'coupon_id' => 0,       // 使用者優惠券id
            'coupon_money' => 0,    // 優惠券抵扣金額
        ], false);
        // 設定預設資料：訂單商品列表
        helper::setDataAttribute($this->productList, [
            'coupon_money' => 0,    // 優惠券抵扣金額
        ], true);
        // 是否開啟優惠券折扣
        if (!$this->settledRule['is_coupon']) {
            return false;
        }
        // 如果沒有可用的優惠券，直接返回
        if ($couponId <= 0 || empty($couponList)) {
            return true;
        }
        // 獲取優惠券資訊
        $couponInfo = helper::getArrayItemByColumn($couponList, 'user_coupon_id', $couponId);
        if ($couponInfo == false) {
            $this->error = '未找到優惠券資訊';
            return false;
        }
        // 計算訂單商品優惠券抵扣金額
        $productListTemp = helper::getArrayColumns($this->productList, ['total_price']);
        $CouponMoney = new ProductDeductService;
        $completed = $CouponMoney->setProductCouponMoney($productListTemp, $couponInfo['reduced_price']);
        // 分配訂單商品優惠券抵扣金額
        foreach ($this->productList as $key => &$product) {
            $product['coupon_money'] = $completed[$key]['coupon_money'] / 100;
        }
        // 記錄訂單優惠券資訊
        $this->orderData['coupon_id'] = $couponId;
        $this->orderData['coupon_money'] = helper::number2($CouponMoney->getActualReducedMoney() / 100);
        return true;
    }

    /**
     * 計算訂單商品的實際付款金額
     */
    private function setOrderProductPayPrice()
    {
        // 商品總價 - 優惠抵扣
        foreach ($this->productList as &$product) {
            // 減去商品滿減
            $value = helper::bcsub($product['total_price'], $product['product_reduce_money']);
            // 減去優惠券抵扣金額
            $value = helper::bcsub($value, $product['coupon_money']);
            // 減去滿減金額
            if ($this->orderData['reduce']) {
                $value = helper::bcsub($value, $product['fullreduce_money']);
            }
            if ($this->orderData['reduce_money'] > 0) {
                $value = helper::bcsub($value, $this->orderData['reduce_money']);
            }
            $total_pay_price = helper::number2($value);
            $product['total_pay_price'] = $total_pay_price > 0 ? $total_pay_price : 0;
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
            'is_allow_points' => true,
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
            //尾款立減金額
            'reduce_money' => isset($this->params['order']) ? $this->params['order']['advance']['reduce_money'] : 0,
        ];
    }

    /**
     * 訂單配送-快遞配送
     */
    private function setOrderExpress()
    {
        // 設定預設資料：配送費用
        helper::setDataAttribute($this->productList, [
            'express_price' => 0,
        ], true);
        // 當前使用者收貨城市id
        $cityId = $this->user['address_default'] ? $this->user['address_default']['city_id'] : null;

        // 初始化配送服務類
        $ExpressService = new ExpressService(
            $this->app_id,
            $cityId,
            $this->productList,
            OrderTypeEnum::MASTER
        );

        // 獲取不支援當前城市配送的商品
        $notInRuleProduct = $ExpressService->getNotInRuleProduct();

        // 驗證商品是否在配送範圍
        $this->orderData['intra_region'] = ($notInRuleProduct === false);

        if (!$this->orderData['intra_region']) {
            $notInRuleProductName = $notInRuleProduct['product_name'];
            $this->error = "很抱歉，您的收貨地址不在商品 [{$notInRuleProductName}] 的配送範圍內";
            return false;
        } else {
            // 計算配送金額
            $ExpressService->setExpressPrice();
        }

        // 訂單總運費金額
        $this->orderData['express_price'] = helper::number2($ExpressService->getTotalFreight());
        return true;
    }

    /**
     * 設定訂單的實際支付金額(含配送費)
     */
    private function setOrderPayPrice()
    {
        // 訂單金額(含優惠折扣)
        $this->orderData['order_price'] = helper::number2(helper::getArrayColumnSum($this->productList, 'total_pay_price'));
        // 訂單實付款金額(訂單金額 + 運費)
        $this->orderData['order_pay_price'] = helper::number2(helper::bcadd($this->orderData['order_price'], $this->orderData['express_price']));
        //支付金額小於0處理
        $this->orderData['order_pay_price'] = $this->orderData['order_pay_price'] > 0 ? $this->orderData['order_pay_price'] : 0;
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
     * 建立訂單事件
     */
    private function createOrderEvent($order)
    {
        // 新增訂單記錄
        $status = $this->add($order, $this->params['remark']);

        if ($this->orderSource['source'] != OrderSourceEnum::ADVANCE) {
            if ($order['delivery'] == DeliveryTypeEnum::EXPRESS) {
                // 記錄收貨地址
                $this->saveOrderAddress($order['address'], $status);
            } elseif ($order['delivery'] == DeliveryTypeEnum::EXTRACT) {
                // 記錄自提資訊
                $this->saveOrderExtract($this->params['linkman'], $this->params['phone']);
            }

            // 儲存訂單商品資訊
            $this->saveOrderProduct($order, $status);
            // 更新商品庫存 (針對下單減庫存的商品)
            ProductFactory::getFactory($this->orderSource['source'])->updateProductStock($order['product_list']);

        } else {
            // 儲存訂單商品資訊
            $this->saveOrderAdvanceProduct($order);
        }
        // 設定優惠券使用狀態
        UserCouponModel::setIsUse($this->params['coupon_id']);

        // 積分兌換扣除使用者積分
        if ($order['force_points']) {
            $describe = "使用者積分兌換消費：{$this->model['order_no']}";
            $this->user->setIncPoints(-$order['points_num'], $describe, false);
        } else {
            // 積分抵扣情況下扣除使用者積分
            if ($order['is_allow_points'] && $order['is_use_points'] && $order['points_num'] > 0) {
                $describe = "使用者消費：{$this->model['order_no']}";
                $this->user->setIncPoints(-$order['points_num'], $describe, false);
            }
        }
        return $status;
    }

    /**
     * 新增訂單記錄
     */
    private function add($order, $remark = '')
    {
        // 訂單資料
        $data = [
            'user_id' => $this->user['user_id'],
            'order_no' => $this->model->orderNo(),
            'trade_no' => $this->model->orderNo(),
            'total_price' => $order['order_total_price'],
            'order_price' => $order['order_price'],
            'coupon_id' => $order['coupon_id'],
            'coupon_money' => $order['coupon_money'],
            'points_money' => $order['is_use_points'] ? $order['points_money'] : 0,
            'points_num' => $order['is_use_points'] ? $order['points_num'] : 0,
            'pay_price' => $order['order_pay_price'],
            'delivery_type' => $order['delivery'],
            'pay_type' => $order['pay_type'],
            'buyer_remark' => trim($remark),
            'order_source' => $this->orderSource['source'],
            'activity_id' => isset($this->orderSource['activity_id']) ? $this->orderSource['activity_id'] : 0,
            'points_bonus' => isset($order['points_bonus']) ? $order['points_bonus'] : 0,
            'is_agent' => $this->settledRule['is_agent'] ? 1 : 0,
            'app_id' => $this->app_id,
            'virtual_auto' => $order['product_list'][0]['virtual_auto'],
            'product_reduce_money' => $order['product_reduce_money'],
            'custom_form' => isset($this->params['custom_form']) ? $this->params['custom_form'] : '',
        ];
        if ($order['delivery'] == DeliveryTypeEnum::EXPRESS) {
            $data['express_price'] = $order['express_price'];
        } elseif ($order['delivery'] == DeliveryTypeEnum::EXTRACT) {
            $data['extract_store_id'] = $order['extract_store']['store_id'];
        }
        // 結束支付時間
        if ($this->orderSource['source'] == OrderSourceEnum::SECKILL) {
            //如果是秒殺
            $config = SettingModel::getItem('seckill');
            $closeMinters = $config['order_close'];
            $data['pay_end_time'] = time() + ((int)$closeMinters * 60);
        } elseif ($this->orderSource['source'] != OrderSourceEnum::ADVANCE) {
            //隨主訂單配置
            $config = SettingModel::getItem('trade');
            $closeDays = $config['order']['close_days'];
            $closeDays != 0 && $data['pay_end_time'] = time() + ((int)$closeDays * 86400);
        }
        // 如果是滿減
        if ($order['reduce']) {
            $data['fullreduce_money'] = $order['reduce']['reduced_price'];
            $data['fullreduce_remark'] = $order['reduce']['active_name'];
        }
        // 儲存訂單記錄
        if ($this->orderSource['source'] == OrderSourceEnum::ADVANCE) {
            unset($data['order_no']);
            // 儲存訂單記錄
            $this->params['order']->save($data);
            return $this->params['order']['order_id'];
        } else {
            $this->model->save($data);
            return $this->model['order_id'];
        }

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
    private function saveOrderProduct($order, $status)
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
                'product_price' => $product['product_sku']['product_price'],
                'line_price' => $product['product_sku']['line_price'],
                'product_weight' => $product['product_sku']['product_weight'],
                'is_user_grade' => (int)$product['is_user_grade'],
                'grade_ratio' => $product['grade_ratio'],
                'grade_product_price' => isset($product['grade_product_price']) ? $product['grade_product_price'] : 0,
                'grade_total_money' => $product['grade_total_money'],
                'coupon_money' => $product['coupon_money'],
                'points_money' => isset($product['points_money']) ? $product['points_money'] : 0,
                'points_num' => isset($product['points_num']) ? $product['points_num'] : 0,
                'points_bonus' => $product['points_bonus'],
                'total_num' => $product['total_num'],
                'total_price' => $product['total_price'],
                'total_pay_price' => $product['total_pay_price'],
                'fullreduce_money' => isset($product['fullreduce_money']) ? $product['fullreduce_money'] : 0,
                'virtual_content' => $product['virtual_content'],
                'product_reduce_money' => $product['product_reduce_money'],
                'table_id' => $product['table_id'],
            ];
            // 記錄訂單商品來源id
            $item['product_source_id'] = isset($product['product_source_id']) ? $product['product_source_id'] : 0;
            // 記錄訂單商品sku來源id
            $item['sku_source_id'] = isset($product['sku_source_id']) ? $product['sku_source_id'] : 0;
            // 記錄拼團類的商品來源id
            $item['bill_source_id'] = isset($product['bill_source_id']) ? $product['bill_source_id'] : 0;
            $productList[] = $item;
        }
        if (isset($order['buyProduct']) && $order['buyProduct']) {
            $this->OrderBuyProduct($order['buyProduct'], $productList, $status);
        }
        $model = new OrderProduct();
        return $model->saveAll($productList);
    }

    /**
     * 儲存訂單贈送商品資訊
     */
    private function OrderBuyProduct($buyProduct, &$productList, $status)
    {
        // 訂單商品列表
        foreach ($buyProduct as $product) {
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
                'product_price' => 0,
                'line_price' => $product['product_sku']['line_price'],
                'product_weight' => $product['product_sku']['product_weight'],
                'points_bonus' => 0,
                'total_num' => $product['total_num'],
                'total_price' => $product['total_price'],
                'total_pay_price' => 0,
                'virtual_content' => $product['virtual_content'],
                'product_reduce_money' => $product['product_reduce_money'],
                'table_id' => $product['table_id'],
                'product_source_id' => $product['product_source_id'],
                'is_gift' => 1
            ];
            $productList[] = $item;
        }
    }

    /**
     * 儲存訂單商品資訊
     */
    private function saveOrderAdvanceProduct($order)
    {
        // 訂單商品列表
        $productDetail = [];
        foreach ($order['product_list'] as $product) {
            $item = [
                'order_product_id' => $product['order_product_id'],
                'is_user_grade' => (int)$product['is_user_grade'],
                'grade_ratio' => $product['grade_ratio'],
                'grade_product_price' => isset($product['grade_product_price']) ? $product['grade_product_price'] : 0,
                'grade_total_money' => $product['grade_total_money'],
                'coupon_money' => $product['coupon_money'],
                'points_money' => isset($product['points_money']) ? $product['points_money'] : 0,
                'points_num' => isset($product['points_num']) ? $product['points_num'] : 0,
                'points_bonus' => $product['points_bonus'],
                'total_num' => $product['total_num'],
                'total_price' => $product['total_price'],
                'total_pay_price' => $product['total_pay_price'],
                'fullreduce_money' => isset($product['fullreduce_money']) ? $product['fullreduce_money'] : 0,
                'product_reduce_money' => $product['product_reduce_money'],
                'content' => $product['content']
            ];
            $productDetail = $item;
        }
        $model = new OrderProduct();
        return $model->where('order_product_id', '=', $productDetail['order_product_id'])->update($productDetail);
    }

    /**
     * 計算訂單可用積分抵扣
     */
    private function setOrderPoints()
    {
        $this->orderData['points_money'] = 0;
        // 積分抵扣總數量
        $this->orderData['points_num'] = 0;
        // 允許積分抵扣
        $this->orderData['is_allow_points'] = false;
        // 積分商城兌換
        if (isset($this->settledRule['force_points']) && $this->settledRule['force_points']) {
            // 積分抵扣金額，商品價格-兌換金額
            $this->orderData['points_money'] = $this->productList[0]['points_money'];
            // 積分抵扣總數量
            $this->orderData['points_num'] = $this->productList[0]['points_num'];
            // 允許積分抵扣
            $this->orderData['is_allow_points'] = true;
            if ($this->user['points'] < $this->productList[0]['points_num']) {
                $this->error = '積分不足，去多賺點積分吧！';
                return false;
            }
            return true;
        }
        // 積分設定
        if (!$this->settledRule['is_point']) {
            return false;
        }
        $setting = SettingModel::getItem('points');
        // 條件：後臺開啟下單使用積分抵扣
        if (!$setting['is_shopping_discount']) {
            return false;
        }
        // 條件：訂單金額滿足[?]元
        if (helper::bccomp($setting['discount']['full_order_price'], $this->orderData['order_total_price']) === 1) {
            return false;
        }
        // 計算訂單商品最多可抵扣的積分數量
        $this->setOrderProductMaxPointsNum();
        // 訂單最多可抵扣的積分總數量
        $maxPointsNumCount = helper::getArrayColumnSum($this->productList, 'max_points_num');
        // 實際可抵扣的積分數量
        $actualPointsNum = min($maxPointsNumCount, $this->user['points']);
        if ($actualPointsNum < 1) {
            $this->orderData['points_money'] = 0;
            // 積分抵扣總數量
            $this->orderData['points_num'] = 0;
            // 允許積分抵扣
            $this->orderData['is_allow_points'] = false;
            return false;
        }
        // 計算訂單商品實際抵扣的積分數量和金額
        $ProductDeduct = new PointsDeductService($this->productList);
        $ProductDeduct->setProductPoints($maxPointsNumCount, $actualPointsNum);
        // 積分抵扣總金額
        $orderPointsMoney = helper::getArrayColumnSum($this->productList, 'points_money');
        $this->orderData['points_money'] = helper::number2($orderPointsMoney);
        // 積分抵扣總數量
        $this->orderData['points_num'] = $actualPointsNum;
        // 允許積分抵扣
        $this->orderData['is_allow_points'] = true;
        // 減去積分抵扣
        foreach ($this->productList as &$product) {
            $value = $product['total_pay_price'];
            // 減去積分抵扣金額
            if ($this->orderData['is_allow_points'] && $this->orderData['is_use_points']) {
                if (!isset($product['points_money'])) {
                    $product['points_money'] = 0;
                }
                // 如果不是積分兌換，則減去積分抵扣金額
                !$this->settledRule['force_points'] && $value = helper::bcsub($value, $product['points_money']);
            }
            $product['total_pay_price'] = helper::number2($value);
        }
        return true;
    }

    /**
     * 計算訂單商品最多可抵扣的積分數量
     */
    private function setOrderProductMaxPointsNum()
    {
        // 積分設定
        $setting = SettingModel::getItem('points');
        foreach ($this->productList as &$product) {
            // 積分兌換
            if ($this->settledRule['force_points']) {
                $product['max_points_num'] = $product['points_num'];
            } else {
                // 商品不允許積分抵扣
                if (!$product['is_points_discount']) continue;
                // 積分抵扣比例
                $deductionRatio = helper::bcdiv($setting['discount']['max_money_ratio'], 100);
                // 最多可抵扣的金額
                $maxPointsMoney = helper::bcmul($product['total_pay_price'], $deductionRatio);
                // 最多可抵扣的積分數量
                $product['max_points_num'] = helper::bcdiv($maxPointsMoney, $setting['discount']['discount_ratio'], 2);
                // 如果超過商品最大抵扣數量
                if ($product['max_points_discount'] != -1 && $product['max_points_num'] > $product['max_points_discount']) {
                    $product['max_points_num'] = $product['max_points_discount'];
                }
            }
        }
        return true;
    }


    /**
     * 計算訂單積分贈送數量
     */
    private function setOrderPointsBonus()
    {
        // 初始化商品積分贈送數量
        foreach ($this->productList as &$product) {
            $product['points_bonus'] = 0;
        }
        // 積分設定
        $setting = SettingModel::getItem('points');
        // 條件：後臺開啟開啟購物送積分
        if (!$setting['is_shopping_gift']) {
            return false;
        }
        // 設定商品積分贈送數量
        foreach ($this->productList as &$product) {
            // 積分贈送比例
            $ratio = $setting['gift_ratio'] / 100;
            // 計算贈送積分數量
            $product['points_bonus'] = !$product['is_points_gift'] ? 0 : helper::bcmul($product['total_pay_price'], $ratio, 0);
        }
        //  訂單積分贈送數量
        $this->orderData['points_bonus'] = helper::getArrayColumnSum($this->productList, 'points_bonus');
        return true;
    }

    /**
     * 設定訂單商品會員折扣價
     */
    private function setOrderGrade()
    {
        // 設定預設資料
        helper::setDataAttribute($this->productList, [
            // 標記參與會員折扣
            'is_user_grade' => false,
            // 會員等級抵扣的金額
            'grade_ratio' => 0,
            // 會員折扣的商品單價
            'grade_product_price' => 0.00,
            // 會員折扣的總額差
            'grade_total_money' => 0.00,
        ], true);

        // 是否開啟會員等級折扣
        if (!$this->settledRule['is_user_grade']) {
            return false;
        }
        // 計算抵扣金額
        foreach ($this->productList as &$product) {
            // 判斷商品是否參與會員折扣
            if (!$product['is_enable_grade']) {
                continue;
            }
            $alone_grade_type = 10;
            // 商品單獨設定了會員折扣
            if ($product['is_alone_grade'] && isset($product['alone_grade_equity'][$this->user['grade_id']])) {
                if ($product['alone_grade_type'] == 10) {
                    // 折扣比例
                    $discountRatio = helper::bcdiv($product['alone_grade_equity'][$this->user['grade_id']], 100);
                } else {
                    $alone_grade_type = 20;
                    $discountRatio = helper::bcdiv($product['alone_grade_equity'][$this->user['grade_id']], $product['product_price'], 2);
                }
            } else {
                // 折扣比例
                $discountRatio = helper::bcdiv($this->user['grade']['equity'], 100);
            }
            if ($discountRatio < 1) {
                // 會員折扣後的商品總金額
                if ($alone_grade_type == 20) {
                    // 固定金額
                    $gradeTotalPrice = $product['alone_grade_equity'][$this->user['grade_id']] * $product['total_num'];
                    $grade_product_price = $product['alone_grade_equity'][$this->user['grade_id']];
                } else {
                    $gradeTotalPrice = max(0.01, helper::bcmul($product['total_price'], $discountRatio));
                    $grade_product_price = helper::number2(helper::bcmul($product['product_price'], $discountRatio), true);
                }
                helper::setDataAttribute($product, [
                    'is_user_grade' => true,
                    'grade_ratio' => $discountRatio,
                    'grade_product_price' => $grade_product_price,
                    'grade_total_money' => helper::number2(helper::bcsub($product['total_price'], $gradeTotalPrice)),
                    'total_price' => $gradeTotalPrice,
                ], false);
            }
        }
        return true;
    }

    /**
     * 設定訂單滿減抵扣資訊
     */
    private function setOrderFullreduceMoney($reduce)
    {
        // 計算訂單商品滿減抵扣金額
        $productListTemp = helper::getArrayColumns($this->productList, ['total_price']);
        $service = new FullDeductService;
        $completed = $service->setProductFullreduceMoney($productListTemp, $reduce['reduced_price']);
        // 分配訂單商品滿減抵扣金額
        foreach ($this->productList as $key => &$product) {
            if (!isset($completed[$key]['fullreduce_money'])) {
                $product['fullreduce_money'] = 0;
            } else {
                $product['fullreduce_money'] = $completed[$key]['fullreduce_money'] / 100;
            }
        }
        return true;
    }

    /**
     * 檢查優惠券是否可以使用
     */
    private function checkCouponCanUse($coupon)
    {
        // 0無限制
        if ($coupon['free_limit'] == 1) {
            //不可與促銷同時,目前只有滿減
            if ($this->orderData['reduce']) {
                return false;
            }
        } else if ($coupon['free_limit'] == 2) {
            //不可與等級優惠同時
            foreach ($this->productList as $product) {
                if ($product['is_user_grade']) {
                    return false;
                }
            }
        } else if ($coupon['free_limit'] == 3) {
            //不可與促銷和等級同時
            if ($this->orderData['reduce']) {
                return false;
            }
            foreach ($this->productList as $product) {
                if ($product['is_user_grade']) {
                    return false;
                }
            }
        }
        // 是否限制商品使用
        if ($coupon['apply_range'] == 20) {
            $product_ids = explode(',', $coupon['product_ids']);
            foreach ($this->productList as $product) {
                if (!in_array($product['product_id'], $product_ids)) {
                    return false;
                }
            }
        }
        // 是否限制分類使用
        if ($coupon['apply_range'] == 30) {
            $category_ids = json_decode($coupon['category_ids'], true);
            foreach ($this->productList as $product) {
                // 如果二級分類包含
                if (in_array($product['category_id'], $category_ids['second'])) {
                    continue;
                }
                // 如果一級分類包含
                if (in_array($product['category_id'], $category_ids['first'])) {
                    continue;
                }
                // 如果分類有父類，則看一級分類是否包含
                $category = Category::detail($product['category_id']);
                if ($category['parent_id'] > 0) {
                    if (in_array($product['category_id'], $category_ids['first'])) {
                        continue;
                    }
                }
                return false;
            }
        }
        return true;
    }

    private function setProductReduce()
    {
        $total_money = 0;
        foreach ($this->productList as $key => &$product) {
            $reduce = FullReduceModel::getProductReductList($product['product_id'], $product['total_price'], $product['total_num']);
            $product['product_reduce_money'] = isset($reduce['reduced_price']) ? helper::number2($reduce['reduced_price']) : 0;
            $total_money += $product['product_reduce_money'];
        }
        $this->orderData['product_reduce_money'] = $total_money;
    }
}