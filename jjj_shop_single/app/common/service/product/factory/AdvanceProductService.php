<?php

namespace app\common\service\product\factory;

use app\common\enum\product\DeductStockTypeEnum;
use app\common\model\plus\advance\AdvanceSku as AdvanceProductSkuModel;
use app\common\model\plus\advance\Product as AdvanceProductModel;
use app\common\model\product\Product as ProductModel;
use app\common\model\product\ProductSku as ProductSkuModel;

/**
 * 商品來源-普通商品擴充套件類
 */
class AdvanceProductService extends ProductService
{
    /**
     * 更新商品庫存 (針對下單減庫存的商品)
     */
    public function updateAdvanceProductStock($productList)
    {
        foreach ($productList as $product) {
            // 下單減庫存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::CREATE) {
                $sku = AdvanceProductSkuModel::detail($product['sku_source_id']);
                // 主庫存減少
                (new AdvanceProductModel)->where('advance_product_id', '=', $sku['advance_product_id'])->dec('stock', $product['total_num'])->update();
                // 下單減庫存
                (new AdvanceProductSkuModel)->where('advance_product_sku_id', '=', $sku['advance_product_sku_id'])->dec('advance_stock', $product['total_num'])->update();
            }
        }
    }

    /**
     * 更新商品庫存銷量（訂單付款後）
     */
    public function updateAdvanceStockSales($productList)
    {
        foreach ($productList as $product) {
            $sku = AdvanceProductSkuModel::detail($product['sku_source_id']);
            // 記錄商品的銷量
            (new AdvanceProductModel)->where('advance_product_id', '=', $sku['advance_product_id'])->inc('total_sales', $product['total_num'])->update();
            // 付款減庫存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::PAYMENT) {
                // 主庫存減少
                (new AdvanceProductModel)->where('advance_product_id', '=', $sku['advance_product_id'])->dec('stock', $product['total_num'])->update();
                // 付款減庫存
                (new AdvanceProductSkuModel)->where('advance_product_sku_id', '=', $sku['advance_product_sku_id'])->dec('advance_stock', $product['total_num'])->update();
            }
        }
        return true;
    }

    /**
     * 更新商品庫存 (針對下單減庫存的商品)
     */
    public function updateProductStock($productList)
    {
        $productData = [];
        $productSkuData = [];
        foreach ($productList as $product) {
            // 下單減庫存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::CREATE) {
                // 總庫存
                $productData[] = [
                    'data' => ['product_stock' => ['dec', $product['total_num']]],
                    'where' => [
                        'product_id' => $product['product_id'],
                    ],
                ];
                $productSkuData[] = [
                    'data' => ['stock_num' => ['dec', $product['total_num']]],
                    'where' => [
                        'product_id' => $product['product_id'],
                        'spec_sku_id' => $product['spec_sku_id'],
                    ],
                ];
            }
        }
        // 更新商品銷量
        !empty($productData) && $this->updateProduct($productData);
        // 更新商品sku庫存
        !empty($productSkuData) && $this->updateProductSku($productSkuData);
        return true;
    }

    /**
     * 更新商品庫存銷量（訂單付款後）
     */
    public function updateStockSales($productList)
    {
        $productData = [];
        $productSkuData = [];
        foreach ($productList as $product) {
            // 記錄商品的銷量
            $product_data = [
                'data' => ['sales_actual' => ['inc', $product['total_num']]],
                'where' => [
                    'product_id' => $product['product_id']
                ],
            ];
            // 付款減庫存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::PAYMENT) {
                //總庫存
                $product_data['data']['product_stock'] = ['dec', $product['total_num']];
                //sku庫存
                $productSkuData[] = [
                    'data' => ['stock_num' => ['dec', $product['total_num']]],
                    'where' => [
                        'product_id' => $product['product_id'],
                        'spec_sku_id' => $product['spec_sku_id'],
                    ],
                ];
            }
            $productData[] = $product_data;
        }
        // 更新商品銷量
        !empty($productData) && $this->updateProduct($productData);
        // 更新商品sku庫存
        !empty($productSkuData) && $this->updateProductSku($productSkuData);
        return true;
    }

    /**
     * 回退商品庫存
     */
    public function backProductStock($productList, $isPayOrder = false)
    {
        $productData = [];
        $productSkuData = [];
        foreach ($productList as $product) {
            $product_item = [
                'where' => [
                    'product_id' => $product['product_id'],
                ],
                'data' => [
                    'product_stock' => ['inc', $product['total_num']],
                ],
            ];
            $sku_item = [
                'where' => [
                    'product_id' => $product['product_id'],
                    'spec_sku_id' => $product['spec_sku_id'],
                ],
                'data' => ['stock_num' => ['inc', $product['total_num']]],
            ];
            if ($isPayOrder == true) {
                // 付款訂單全部庫存
                $productData[] = $product_item;
                $productSkuData[] = $sku_item;
            } else {
                // 未付款訂單，判斷必須為下單減庫存時才回退
                $product['deduct_stock_type'] == DeductStockTypeEnum::CREATE && $productData[] = $product_item;
                $product['deduct_stock_type'] == DeductStockTypeEnum::CREATE && $productSkuData[] = $sku_item;
            }

            $sku = AdvanceProductSkuModel::detail($product['sku_source_id']);
            // 回退主庫存
            (new AdvanceProductModel)->where('advance_product_id', '=', $sku['advance_product_id'])->inc('stock', $product['total_num'])->update();
            // 回退sku庫存
            (new AdvanceProductSkuModel)->where('advance_product_sku_id', '=', $sku['advance_product_sku_id'])->inc('advance_stock', $product['total_num'])->update();
        }
        // 更新商品庫存
        !empty($productData) && $this->updateProduct($productData);
        // 更新商品sku庫存
        !empty($productSkuData) && $this->updateProductSku($productSkuData);
        return true;
    }

    /**
     * 更新商品資訊
     */
    private function updateProduct($data)
    {
        return (new ProductModel)->updateAll($data);
    }

    /**
     * 更新商品sku資訊
     */
    private function updateProductSku($data)
    {
        return (new ProductSkuModel)->updateAll($data);
    }

}