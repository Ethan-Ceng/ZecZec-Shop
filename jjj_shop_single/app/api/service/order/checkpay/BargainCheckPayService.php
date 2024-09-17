<?php

namespace app\api\service\order\checkpay;

use app\common\enum\product\DeductStockTypeEnum;
use app\common\model\plus\bargain\BargainSku as BargainSkuModel;

/**
 * 砍價訂單支付檢查服務類
 */
class BargainCheckPayService extends CheckPayService
{
    /**
     * 判斷訂單是否允許付款
     */
    public function checkOrderStatus($order)
    {
        // 判斷訂單狀態
        if (!$this->checkOrderStatusCommon($order)) {
            return false;
        }
        // 判斷商品狀態、庫存
        if (!$this->checkProductStatus($order['product'])) {
            return false;
        }
        return true;
    }

    /**
     * 判斷商品狀態、庫存 (未付款訂單)
     */
    protected function checkProductStatus($productList)
    {
        foreach ($productList as $product) {
            // 拼團商品sku資訊
            $bargainProductSku = BargainSkuModel::detail($product['sku_source_id'], ['product']);
            $bargainProduct = $bargainProductSku['product'];
            // sku是否存在
            if (empty($bargainProductSku)) {
                $this->error = "很抱歉，商品 [{$product['product_name']}] sku已不存在，請重新下單";
                return false;
            }
            // 判斷商品是否下架
            if (empty($bargainProduct)) {
                $this->error = "很抱歉，商品 [{$product['product_name']}] 不存在或已刪除";
                return false;
            }
            // 付款減庫存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::PAYMENT && $product['total_num'] > $bargainProductSku['bargain_stock']) {
                $this->error = "很抱歉，商品 [{$product['product_name']}] 庫存不足";
                return false;
            }
        }
        return true;
    }

}