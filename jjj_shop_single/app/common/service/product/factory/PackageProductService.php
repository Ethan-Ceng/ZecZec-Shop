<?php

namespace app\common\service\product\factory;

use app\common\model\product\Product as ProductModel;
use app\common\model\product\ProductSku as ProductSkuModel;
use app\common\enum\product\DeductStockTypeEnum;

/**
 * 商品來源-禮包購商品擴充套件類
 */
class PackageProductService extends ProductService
{
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

            $sku_item = [
                'where' => [
                    'product_id' => $product['product_id'],
                    'spec_sku_id' => $product['spec_sku_id'],
                ],
                'data' => ['stock_num' => ['inc', $product['total_num']]],
            ];
            if ($isPayOrder == true) {
                $product_item = [
                    'where' => [
                        'product_id' => $product['product_id'],
                    ],
                    'data' => [
                        'product_stock' => ['inc', $product['total_num']],
                        'sales_actual' => ['dec', $product['total_num']]
                    ],
                ];
                // 付款訂單全部庫存
                $productData[] = $product_item;
                $productSkuData[] = $sku_item;
            } else {
                $product_item = [
                    'where' => [
                        'product_id' => $product['product_id'],
                    ],
                    'data' => ['product_stock' => ['inc', $product['total_num']]],
                ];
                // 未付款訂單，判斷必須為下單減庫存時才回退
                $product['deduct_stock_type'] == DeductStockTypeEnum::CREATE && $productData[] = $product_item;
                $product['deduct_stock_type'] == DeductStockTypeEnum::CREATE && $productSkuData[] = $sku_item;
            }
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