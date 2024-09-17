<?php

namespace app\api\service\order\settled;

use app\common\enum\order\OrderSourceEnum;
use app\common\model\settings\Setting as SettingModel;
use app\api\model\order\Order as OrderModel;
use app\api\model\product\Product as ProductModel;

/**
 * 預售訂單結算服務類
 */
class AdvanceOrderSettledService extends OrderSettledService
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
            'source' => OrderSourceEnum::ADVANCE,
        ];
        $this->config = SettingModel::getItem('advance');
        // 自身構造,差異化規則
        $this->settledRule = array_merge($this->settledRule, [
            'is_point' => $this->config['is_point'],     //積分抵扣
            'is_coupon' => $this->config['is_coupon'],
            'is_agent' => $this->config['is_agent'],
            'is_user_grade' => $this->config['is_user_grade'],     // 會員等級折扣
        ]);
    }

    /**
     * 驗證訂單商品的狀態
     */
    public function validateProductList()
    {
        $advance = $this->params['order']['advance'];
        if ($advance['end_time'] > time()) {
            $this->error = "預售時間還未結束，不允許支付尾款";
            return false;
        }
        if ($this->params['order']['pay_end_time'] > 0 && $this->params['order']['pay_end_time'] < time()) {
            $this->error = "預售支付截至時間已結束，不允許支付尾款";
            return false;
        }
        $product = ProductModel::detail($this->productList[0]['product_id']);

        if ($product['product_status']['value'] != 10) {
            $this->error = "很抱歉，預售商品已下架";
            return false;
        }
        return true;
    }
}