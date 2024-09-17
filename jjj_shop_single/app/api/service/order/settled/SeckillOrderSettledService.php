<?php

namespace app\api\service\order\settled;

use app\api\model\order\Order as OrderModel;
use app\api\model\plus\seckill\Active as ActiveModel;
use app\api\model\plus\seckill\Product as ProductModel;
use app\common\enum\order\OrderSourceEnum;
use app\common\model\settings\Setting as SettingModel;

/**
 * 秒殺訂單結算服務類
 */
class SeckillOrderSettledService extends OrderSettledService
{
    private $config;

    /**
     * 建構函式
     */
    public function __construct($user, $productList, $params)
    {

        parent::__construct($user, $productList, $params);

        // 訂單來源
        $this->orderSource = [
            'source' => OrderSourceEnum::SECKILL,
            'activity_id' => $productList[0]['activity_id']
        ];
        $this->config = SettingModel::getItem('seckill');
        // 自身構造,差異化規則
        $this->settledRule = array_merge($this->settledRule, [
            'is_coupon' => $this->config['is_coupon'],
            'is_agent' => $this->config['is_agent'],
            'is_point' => $this->config['is_point'],
            'is_user_grade' => false,     // 會員等級折扣
        ]);
    }

    /**
     * 驗證訂單商品的狀態
     */
    public function validateProductList()
    {
        foreach ($this->productList as $product) {
            // 判斷商品是否下架
            if ($product['product_status']['value'] != 10) {
                $this->error = "很抱歉，秒殺商品已下架";
                return false;
            }
            // 判斷商品庫存
            if ($product['total_num'] > $product['seckill_sku']['seckill_stock']) {
                $this->error = "很抱歉，秒殺商品庫存不足";
                return false;
            }
            //是否在秒殺時間段
            $seckill_model = ProductModel::detail($product['seckill_sku']['seckill_product_id'], ['active']);
            $res = (new ActiveModel())->checkOrderTime($seckill_model['active']);
            if ($res['code'] != 0) {
                $this->error = $res[$res['code']];
                return false;
            }
            // 是否超過購買數
            $hasNum = OrderModel::getPlusOrderNum($this->user['user_id'], $product['product_source_id'], OrderSourceEnum::SECKILL);
            if($hasNum + $product['total_num'] > $product['seckill_product']['limit_num']){
                $this->error = "很抱歉，你已購買或超過此商品最大秒殺數量";
                return false;
            }
        }
        return true;
    }
}