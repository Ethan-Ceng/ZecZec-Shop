<?php

namespace app\api\model\plus\giftpackage;

use app\common\model\plus\giftpackage\GiftPackage as GiftPackageModel;
use app\api\model\plus\coupon\Coupon;
use app\api\model\product\Product;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\model\store\Store as StoreModel;
use app\common\model\settings\Setting as SettingModel;
use app\api\model\plus\giftpackage\Order as OrderModel;

/**
 * 禮包購模型
 */
class GiftPackage extends GiftPackageModel
{

    /**
     * 獲取資料
     */
    public function getGiftPackage($id, $params, $user = false)
    {
        $data = self::detail($id);
        if ($data['is_delete'] == 1) {
            $this->error = '活動不存在';
            return false;
        }
        $data = $data->toArray();
        if ($data['is_coupon'] == '1') {
            $data['is_coupon'] = true;
        }
        if ($data['is_point'] == '1') {
            $data['is_point'] = true;
        }

        $this->buildData($data);
        return $data;
    }

    /**
     * 獲取資料
     */
    public function checkGiftPackage($id, $params, $user)
    {
        $data = self::detail($id);
        $data = $data->toArray();
        $data['value1'][] = $data['start_time']['text'];
        $data['value1'][] = $data['end_time']['text'];
        $data['grade_ids'] = explode(',', $data['grade_ids']);
        if ($data['status'] == 1) {
            $this->error = '活動已終止';
            return false;
        }
        if ($data['is_coupon'] == '1') {
            $data['is_coupon'] = true;
        }
        if ($data['is_point'] == '1') {
            $data['is_point'] = true;
        }
        if ($data['start_time']['value'] > time()) {
            $this->error = '活動還未開始';
            return false;
        }
        if ($data['end_time']['value'] < time()) {
            $this->error = '活動已結束';
            return false;
        }
        if ($data['is_times'] == 1) {
            $where = [
                'user_id' => $user['user_id'],
                'pay_status' => 20,
                'gift_package_id' => $id,
            ];
            //統計購買數量
            $count = (new OrderModel())->where($where)->count('order_id');
            //判斷購買次數
            if ($count >= $data['times']) {
                $this->error = '已超過限購數量';
                return false;
            }
        }
        //二維碼型別10，一碼，20，不同碼
        if ($data['code_type'] == 10) {
            //統計已購買數量
            $totalcount = (new OrderModel())->where('gift_package_id', '=', $id)
                ->where('pay_status', '=', 20)
                ->where('order_status', '<>', 20)
                ->count();
            if ($totalcount >= $data['total_num']) {
                $this->error = '禮包數量不足';
                return false;
            } else if ($data['code_type'] == 20) {
                //查詢碼是否使用
                $code = (new OrderModel)->where('gift_package_id', '=', $id)
                    ->where('code', '=', $params['code'])
                    ->count();
                if ($code > 0) {
                    $this->error = '優惠碼已使用';
                    return false;
                }
            }
        }
        //判斷是否符合等級
        if ($data['is_grade'] == 1 && !in_array($user['grade_id'], $data['grade_ids'])) {
            $this->error = '會員等級不符合';
            return false;
        }
        $this->buildData($data);
        $delivery_type = SettingModel::getItem('store')['delivery_type'];
        sort($delivery_type);
        // 設定預設配送方式
        if (!$params['delivery_type']) {
            $params['delivery_type'] = $delivery_type[0];
        }
        $data['delivery_type'] = $params['delivery_type'];
        $data['extract_store'] = '';
        // 處理配送方式
        if ($params['delivery_type'] == DeliveryTypeEnum::EXPRESS && $user) {
            $data['address'] = $user['address_default'];
            $data['exist_address'] = $user['address_id'] > 0;
        } elseif ($params['delivery_type'] == DeliveryTypeEnum::EXTRACT && $params['store_id'] > 0 && $user) {
            $data['extract_store'] = StoreModel::detail($params['store_id']);
        }
        $data['delivery'] = $delivery_type;
        return $data;
    }

    private function buildData(&$data)
    {
        if ($data['coupon_ids'] != '') {
            $model = new Coupon();
            $coupon = json_decode($data['coupon_ids'], true);
            foreach ($coupon as $key => &$value) {
                $couponInfo = $model->getCouponInfo($value['coupon_id']);
                $value['name'] = $couponInfo['name'];
                $value['reduce_price'] = $couponInfo['reduce_price'];
                $value['expire_type'] = $couponInfo['expire_type'];
                $value['expire_day'] = $couponInfo['expire_day'];
                $value['start_time'] = $couponInfo['start_time'];
                $value['end_time'] = $couponInfo['end_time'];
                $value['coupon_type'] = $couponInfo['coupon_type'];
                $value['discount'] = $couponInfo['discount'];
            }
            $data['coupon_list'] = $coupon;
            $data['coupon'] = explode(',', $data['coupon_ids']);
        }
        if ($data['product_ids'] != '') {
            $ProductModel = new Product();
            $product = $ProductModel->getProduct($data['product_ids']);
            $data['product_list'] = $product->toArray();
            $data['product'] = explode(',', $data['product_ids']);
        }
    }
}