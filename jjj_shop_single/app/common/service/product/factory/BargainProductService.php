<?php

namespace app\common\service\product\factory;

use app\common\enum\product\DeductStockTypeEnum;
use app\common\model\plus\bargain\BargainSku as ProductSkuModel;
use app\common\model\plus\bargain\Product as ProductModel;
use app\common\model\plus\bargain\Task as TaskModel;
/**
 * 商品來源-普通商品擴充套件類
 */
class BargainProductService extends ProductService
{
    /**
     * 更新商品庫存 (針對下單減庫存的商品)
     */
    public function updateProductStock($productList)
    {
        foreach ($productList as $product) {
            // 下單減庫存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::CREATE) {
                $sku = ProductSkuModel::detail($product['sku_source_id']);
                // 主庫存減少
                (new ProductModel)->where('bargain_product_id', '=', $sku['bargain_product_id'])->dec('stock', $product['total_num'])->update();
                // 下單減庫存
                (new ProductSkuModel)->where('bargain_product_sku_id', '=', $sku['bargain_product_sku_id'])->dec('bargain_stock', $product['total_num'])->update();
            }
        }
    }

    /**
     * 更新商品庫存銷量（訂單付款後）
     */
    public function updateStockSales($productList)
    {
        foreach ($productList as $product) {
            $sku = ProductSkuModel::detail($product['sku_source_id']);
            // 記錄商品的銷量
            (new ProductModel)->where('bargain_product_id', '=', $sku['bargain_product_id'])->inc('total_sales', $product['total_num'])->update();
            // 付款減庫存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::PAYMENT) {
                // 主庫存減少
                (new ProductModel)->where('bargain_product_id', '=', $sku['bargain_product_id'])->dec('stock', $product['total_num'])->update();
                // 下單減庫存
                (new ProductSkuModel)->where('bargain_product_sku_id', '=', $sku['bargain_product_sku_id'])->dec('bargain_stock', $product['total_num'])->update();
            }
            //修改訂單為已購買,砍價成功
            (new TaskModel)->where('bargain_task_id', '=', $product['bill_source_id'])->data([
                'is_buy' => 1,
                'status' => 1
            ])->update();
        }
        return true;
    }

    /**
     * 回退商品庫存
     */
    public function backProductStock($productList, $isPayOrder = false)
    {
        foreach ($productList as $product) {
            // 未付款訂單並且建立時減庫存，回退庫存
            if (!$isPayOrder && $product['deduct_stock_type'] == DeductStockTypeEnum::CREATE) {
                $point_sku = ProductSkuModel::detail($product['sku_source_id']);
                // 回退主庫存
                (new ProductModel)->where('bargain_product_id', '=', $point_sku['bargain_product_id'])->inc('stock')->update();
                // 回退sku庫存
                (new ProductSkuModel)->where('bargain_product_sku_id', '=', $point_sku['bargain_product_sku_id'])->inc('bargain_stock')->update();
            }
        }
    }

}