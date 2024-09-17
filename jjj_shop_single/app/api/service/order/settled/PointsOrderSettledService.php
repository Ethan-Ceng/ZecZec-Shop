<?php

namespace app\api\service\order\settled;

use app\common\enum\order\OrderSourceEnum;
use app\common\model\settings\Setting as SettingModel;
use app\api\model\order\Order as OrderModel;

/**
 * 積分商城訂單結算服務類
 */
class PointsOrderSettledService extends OrderSettledService
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
            'source' => OrderSourceEnum::POINTS,
       ];
        $this->config = SettingModel::getItem('pointsmall');
        // 自身構造,差異化規則
        $this->settledRule = array_merge($this->settledRule, [
            'force_points' => true,     //強制使用積分，積分兌換
            'is_coupon' => $this->config['is_coupon'],
            'is_agent' => $this->config['is_agent'],
            'is_user_grade' => false,     // 會員等級折扣
        ]);
    }

    /**
     * 驗證訂單商品的狀態
     */
    public function validateProductList()
    {
        // 判斷活動是否開啟
        if(!$this->config['is_open']){
            $this->error = "商品積分兌換活動未開啟";
            return false;
        }
        foreach ($this->productList as $product) {
            // 判斷商品是否下架
            if ($product['product_status']['value'] != 10) {
                $this->error = "很抱歉，積分兌換商品已下架";
                return false;
            }
            // 判斷商品庫存
            if ($product['total_num'] > $product['point_sku']['point_stock']) {
                $this->error = "很抱歉，積分兌換商品庫存不足";
                return false;
            }
            // 是否超過購買數
            $hasNum = OrderModel::getPlusOrderNum($this->user['user_id'], $product['product_source_id'],OrderSourceEnum::POINTS);
            if($hasNum + $product['total_num'] > $product['point_product']['limit_num']){
                $this->error = "很抱歉，你已兌換或超過最大兌換數量";
                return false;
            }
        }
        return true;
    }
}