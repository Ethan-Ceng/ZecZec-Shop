<?php

namespace app\api\model\plus\lottery;

use app\common\enum\order\OrderSourceEnum;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\model\order\Order as OrderModel;
use app\api\model\order\OrderAddress as OrderAddress;
use app\api\model\order\Order as OrdersModel;
use app\api\service\user\UserService;
use app\api\model\order\OrderExtract as OrderExtractModel;
use app\api\model\product\Product as ProductModel;
use app\api\model\order\OrderProduct;
use app\api\model\product\Product;
use app\common\model\store\Store as StoreModel;
use app\common\model\settings\Setting as SettingModel;
use app\common\service\product\factory\ProductFactory;
use think\Exception;

/**
 * 轉盤兌換訂單模型
 */
class Order extends OrderModel
{
    //獲取資料
    public function getOrderData($param, $user)
    {
        $data = [];
        $delivery_type = SettingModel::getItem('store')['delivery_type'];
        sort($delivery_type);
        if (!$param['delivery_type']) {
            $param['delivery_type'] = $delivery_type[0];
        }
        $data['delivery_type'] = $param['delivery_type'];
        $data['extract_store'] = '';
        //配送方式
        $deliveryType = SettingModel::getItem('store')['delivery_type'];
        $data['deliverySetting'] = $deliveryType;
        // 處理配送方式
        if ($param['delivery_type'] == DeliveryTypeEnum::EXPRESS && $user) {
            $data['address'] = $user['address_default'];
            $data['exist_address'] = $user['address_id'] > 0;
        } elseif ($param['delivery_type'] == DeliveryTypeEnum::EXTRACT && $param['store_id'] > 0 && $user) {
            $data['last_extract'] = UserService::getLastExtract($user['user_id']);
            $data['extract_store'] = StoreModel::detail($param['store_id']);
        }
        $data['delivery'] = $delivery_type;
        return $data;
    }

    //新增訂單
    public function addOrder($param, $user)
    {
        // 開啟事務
        $this->startTrans();
        try {
            $detail = (new Record)->detail($param['record_id']);
            if ($detail['prize_type'] != 3) {
                $this->error = '禮品型別錯誤';
                return false;
            }
            if ($detail['status'] == 1) {
                $this->error = '禮品已兌換';
                return false;
            }
            $app_id = self::$app_id;
            $extract = json_decode($param['extract'], true);
            if ($param['delivery_type'] == 10) {
                if (empty($user['address_default'])) {
                    $this->error = '請先選擇收貨地址';
                    return false;
                }
            } elseif ($param['delivery_type'] == 20) {
                if (empty($extract)) {
                    $this->error = '請新增自提資訊';
                    return false;
                }
                if (empty($extract['store_id'])) {
                    $this->error = '請先選擇自提門店';
                    return false;
                }
                if (empty($extract['linkman']) || empty($extract['phone'])) {
                    $this->error = '請填寫聯絡人和電話';
                    return false;
                }
            }
            // 商品詳情
            $product = ProductModel::detail($detail['award_id']);
            if ($product['spec_type'] == 20 && $param['product_sku_id'] <= 0) {
                $this->error = '請選擇商品規格';
                return false;
            }
            // 商品sku資訊
            $product['product_sku'] = ProductModel::getProductSku($product, $param['product_sku_id']);
            // 訂單資料
            $data = [
                'user_id' => $user['user_id'],
                'order_no' => $this->orderNo(),
                'total_price' => $product['spec_type'] == 10 ? $product['product_price'] : $product['product_sku']['product_price'],
                'order_price' => $product['spec_type'] == 10 ? $product['product_price'] : $product['product_sku']['product_price'],
                'coupon_id' => 0,
                'coupon_money' => 0,
                'points_money' => 0,
                'points_num' => 0,
                'pay_price' => 0,
                'delivery_type' => $param['delivery_type'],
                'pay_type' => 10,
                'buyer_remark' => '',
                'order_source' => OrderSourceEnum::LOTTERY,
                'activity_id' => $param['record_id'],
                'points_bonus' => 0,
                'is_agent' => 0,
                'app_id' => $app_id,
                'pay_status' => 20,
                'pay_time' => time(),
                'pay_source' => 'wx',
                'extract_store_id' => $param['delivery_type'] == 20 ? $extract['store_id'] : 0,
            ];
            // 儲存訂單記錄
            $OrdersModel = new OrdersModel;
            $OrdersModel->save($data);
            $new_order_id = $OrdersModel->order_id;
            if ($param['delivery_type'] == 10) {
                // 記錄收貨地址
                $this->saveOrderAddress($user['address_default'], $new_order_id, $user['user_id'], $app_id);
            } elseif ($param['delivery_type'] == 20) {
                // 記錄自提資訊
                $this->saveOrderExtract($extract, $new_order_id, $user['user_id'], $app_id);
            }
            //新增商品
            $this->saveOrderProduct($user, $new_order_id, $product, $app_id);
            // 更新商品庫存 (針對下單減庫存的商品)
            ProductFactory::getFactory($data['order_source'])->updateProductStock([$product]);
            // 更新商品庫存、銷量
            ProductFactory::getFactory($data['order_source'])->updateStockSales([$product]);
            //更新為已兌換
            (new Record())->where('record_id', '=', $param['record_id'])->update(['status' => 1]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 儲存訂單商品資訊
     */
    private function saveOrderProduct($user, $status, $product, $app_id = null)
    {
        is_null($app_id) && $app_id = self::$app_id;
        // 訂單商品列表
        $goods = [
            'order_id' => $status,
            'user_id' => $user['user_id'],
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
        ];
        // 記錄訂單商品來源id
        $goods['product_source_id'] = isset($product['product_source_id']) ? $product['product_source_id'] : 0;
        // 記錄訂單商品sku來源id
        $goods['sku_source_id'] = isset($product['sku_source_id']) ? $product['sku_source_id'] : 0;
        // 記錄拼團類的商品來源id
        $goods['bill_source_id'] = isset($product['bill_source_id']) ? $product['bill_source_id'] : 0;

        $model = new OrderProduct();
        return $model->save($goods);
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
}