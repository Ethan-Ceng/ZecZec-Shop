<?php

namespace app\api\model\plus\buy;

use app\api\model\product\Product as ProductModel;
use app\common\model\plus\buy\BuyActivity as BuyActivityModel;

/**
 * 買送模型
 */
class BuyActivity extends BuyActivityModel
{
    /**
     * 查詢是否存在買送活動
     */
    public function getDetail($productlist)
    {
        $productList = [];
        foreach ($productlist as $item) {
            $sendList = $this->alias('b')
                ->join('buy_activity_product p', 'p.buy_id=b.buy_id')
                ->where('start_time', '<', time())
                ->where('end_time', '>=', time())
                ->where('is_delete', '=', 0)
                ->where('status', '=', 1)
                ->where('product_id', '=', $item['product_id'])
                ->where('product_num', '<=', $item['total_num'])
                ->field('b.*,product_id,product_num')
                ->order('sort asc')
                ->select();
            if (count($sendList) <= 0) {
                continue;
            } else {
                foreach ($sendList as $value) {
                    if ($value['product_ids']) {
                        if ($value['send_type'] == 10) {
                            $num = 1;
                        } else {
                            $num = floor($item['total_num'] / $value['product_num']);
                            $num = $num > $value['max_times'] ? $value['max_times'] : $num;
                        }
                        $product_list = $value['product_ids'];
                        foreach ($product_list as $item) {
                            // 商品詳情
                            $product = ProductModel::detail($item['product_id']);
                            // 商品sku資訊
                            $product['product_sku'] = ProductModel::getProductSku($product, $item['spec_sku_id']);
                            if (!$product['product_sku']) {
                                continue;
                            }
                            if ($item['product_num'] > $product['product_sku']['stock_num']) {
                                continue;
                            }
                            $product = $product->hidden(['category', 'content', 'image', 'sku']);
                            // 商品單價
                            $product['product_price'] = 0;
                            $product['line_price'] = $product['product_sku']['line_price'];
                            // 商品購買數量
                            $product['total_num'] = $item['product_num'] * $num;
                            $product['spec_sku_id'] = $item['spec_sku_id'];
                            // 商品購買總金額
                            $product['total_price'] = 0;
                            //活動id
                            $product['product_source_id'] = $value['buy_id'];
                            $productList[] = $product;
                        }
                    }
                }
            }
        }
        $productData = [];
        if ($productList) {
            foreach ($productList as $product) {
                if (!isset($productData[$product['product_id'] . '-' . $product['spec_sku_id']])) {
                    $productData[$product['product_id'] . '-' . $product['spec_sku_id']] = $product;
                } else {
                    $productData[$product['product_id'] . '-' . $product['spec_sku_id']]['total_num'] += $product['total_num'];
                }
            }
            $productData = array_values($productData);
        }
        return $productData;
    }
}