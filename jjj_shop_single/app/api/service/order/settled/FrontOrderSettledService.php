<?php

namespace app\api\service\order\settled;

use app\common\enum\order\OrderSourceEnum;
use app\common\model\settings\Setting as SettingModel;
use app\api\model\order\Order as OrderModel;
use app\api\model\order\OrderAdvance as OrderAdvanceModel;

/**
 * 預售訂單定金結算服務類
 */
class FrontOrderSettledService extends AdvanceSettledService
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
    }

    /**
     * 驗證訂單商品的狀態
     */
    public function validateProductList()
    {
        foreach ($this->productList as $product) {
            // 判斷商品是否開始預售
            if ($product['advance']['start_time'] > time()) {
                $this->error = "很抱歉，還未到達預售時間";
                return false;
            }
            if ($product['advance']['end_time'] < time()) {
                $this->error = "很抱歉，預售商品時間已結束";
                return false;
            }
            // 判斷商品是否下架
            if ($product['product_status']['value'] != 10) {
                $this->error = "很抱歉，預售商品已下架";
                return false;
            }
            // 判斷商品庫存
            if ($product['total_num'] > $product['advance_sku']['advance_stock']) {
                $this->error = "很抱歉，預售商品庫存不足";
                return false;
            }
            // 是否超過購買數
            if ($product['advance_product']['limit_num'] > 0) {
                $hasNum = OrderAdvanceModel::getPlusOrderNum($this->user['user_id'], $product['product_source_id']);
                if ($hasNum + $product['total_num'] > $product['advance_product']['limit_num']) {
                    $this->error = "很抱歉，你購買數量超過最大限購數量";
                    return false;
                }
            }
        }
        return true;
    }
}