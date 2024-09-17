<?php

namespace app\common\service\product\factory;

use app\common\enum\product\DeductStockTypeEnum;
use app\common\model\plus\seckill\SeckillSku as ProductSkuModel;
use app\common\model\plus\seckill\Product as ProductModel;

/**
 * 商品來源-普通商品擴充套件類
 */
class SeckillProductService extends ProductService
{
    /**
     * 更新商品庫存 (針對下單減庫存的商品)
     */
    public function updateProductStock($productList)
    {
        foreach ($productList as $product) {
            // 下單減庫存
            $sku = ProductSkuModel::detail($product['sku_source_id']);
            // 參與人數
            (new ProductModel)->where('seckill_product_id', '=', $sku['seckill_product_id'])->inc('join_num')->update();
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::CREATE) {
                // 主庫存減少
                (new ProductModel)->where('seckill_product_id', '=', $sku['seckill_product_id'])->dec('stock', $product['total_num'])->update();
                // 下單減庫存
                (new ProductSkuModel)->where('seckill_product_sku_id', '=', $sku['seckill_product_sku_id'])->dec('seckill_stock', $product['total_num'])->update();
            }
        }
    }

    public function updateStockSales($productList)
    {
        foreach ($productList as $product) {
            $sku = ProductSkuModel::detail($product['sku_source_id']);
            // 記錄商品的銷量
            (new ProductModel)->where('seckill_product_id', '=', $sku['seckill_product_id'])->inc('total_sales', $product['total_num'])->update();
            // 付款減庫存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::PAYMENT) {
                // 主庫存減少
                (new ProductModel)->where('seckill_product_id', '=', $sku['seckill_product_id'])->dec('stock', $product['total_num'])->update();
                // 下單減庫存
                (new ProductSkuModel)->where('seckill_product_sku_id', '=', $sku['seckill_product_sku_id'])->dec('seckill_stock', $product['total_num'])->update();
            }
        }
        return true;
    }


    public function backProductStock($productList, $isPayOrder = false)
    {
        foreach ($productList as $product) {
            // 1,未付款訂單並且建立時減庫存，回退庫存 2,已付款訂單回退
            if ((!$isPayOrder && $product['deduct_stock_type'] == DeductStockTypeEnum::CREATE)
                || $isPayOrder
            ) {
                $sku = ProductSkuModel::detail($product['sku_source_id']);
                // 回退主庫存
                (new ProductModel)->where('seckill_product_id', '=', $sku['seckill_product_id'])->inc('stock', $product['total_num'])->update();
                // 回退sku庫存
                (new ProductSkuModel)->where('seckill_product_sku_id', '=', $sku['seckill_product_sku_id'])->inc('seckill_stock', $product['total_num'])->update();
            }
        }
    }

}