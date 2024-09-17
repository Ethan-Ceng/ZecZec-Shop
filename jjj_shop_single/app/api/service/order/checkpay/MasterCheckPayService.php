<?php

namespace app\api\service\order\checkpay;

use app\api\model\product\ProductSku as ProductSkuModel;
use app\common\enum\product\DeductStockTypeEnum;

/**
 * 主訂單支付檢查服務類
 */
class MasterCheckPayService extends CheckPayService
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
            // 判斷商品是否下架
            if (
                empty($product['product'])
                || $product['product']['product_status']['value'] != 10
            ) {
                $this->error = "很抱歉，商品 [{$product['product_name']}] 已下架";
                return false;
            }
            // 獲取商品的sku資訊
            $productSku = $this->getOrderProductSku($product['product_id'], $product['spec_sku_id']);
            // sku已不存在
            if (empty($productSku)) {
                $this->error = "很抱歉，商品 [{$product['product_name']}] sku已不存在，請重新下單";
                return false;
            }
            // 付款減庫存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::PAYMENT && $product['total_num'] > $productSku['stock_num']) {
                $this->error = "很抱歉，商品 [{$product['product_name']}] 庫存不足";
                return false;
            }
        }
        return true;
    }

    /**
     * 獲取指定的商品sku資訊
     */
    private function getOrderProductSku($productId, $specSkuId)
    {
        return ProductSkuModel::detail($productId, $specSkuId);
    }

}