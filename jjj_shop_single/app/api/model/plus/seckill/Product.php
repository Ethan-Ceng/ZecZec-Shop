<?php

namespace app\api\model\plus\seckill;

use app\common\exception\BaseException;
use app\common\model\plus\seckill\Product as SeckillProductModel;
use app\api\model\product\Product as ProductModel;

/**
 * 限時秒殺模型
 */
class Product extends SeckillProductModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'sales_initial',
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * 獲取商品列表（用於訂單結算）
     */
    public static function getSeckillProduct($params)
    {
        // 秒殺任務詳情
        $seckills = self::detail($params['seckill_product_id'], ['seckillSku']);
        if (empty($seckills)) {
            throw new BaseException(['msg' => '秒殺商品不存在或已結束']);
        }
        // 秒殺商品詳情
        $product = ProductModel::detail($seckills['product_id']);

        // 積分商品sku資訊
        $point_sku = null;
        if ($product['spec_type'] == 10) {
            $point_sku = $seckills['seckillSku'][0];
        } else {
            //多規格
            foreach ($seckills['seckillSku'] as $sku) {
                if ($sku['seckill_product_sku_id'] == $params['seckill_product_sku_id']) {
                    $point_sku = $sku;
                    break;
                }
            }
        }
        if ($point_sku == null) {
            throw new BaseException(['msg' => '秒殺商品規格不存在']);
        }

        // 商品sku資訊
        $product['product_sku'] = ProductModel::getProductSku($product, $params['product_sku_id']);
        $product['seckill_sku'] = $point_sku;
        // 商品列表
        $productList = [$product->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($productList as &$item) {
            // 商品單價
            $item['product_price'] = $point_sku['seckill_price'];
            // 商品購買數量
            $item['total_num'] = $params['product_num'];
            $item['spec_sku_id'] = $item['product_sku']['spec_sku_id'];
            // 商品購買總金額
            $item['total_price'] = $point_sku['seckill_price'] * $item['total_num'];
            $item['seckill_product_sku_id'] = $point_sku['seckill_product_sku_id'];
            $item['product_sku_id'] = $params['product_sku_id'];
            $item['product_source_id'] = $point_sku['seckill_product_id'];
            // 秒殺活動id
            $item['activity_id'] = $seckills['seckill_activity_id'];
            $item['sku_source_id'] = $point_sku['seckill_product_sku_id'];
            // 秒殺最大購買數
            $item['seckill_product'] = [
                'limit_num' => $seckills['limit_num']
            ];
        }
        return $productList;
    }

    /**
     * 獲取首頁秒殺商品顯示
     */
    public function getProductList($seckill_activity_id, $limit)
    {
        // 獲取列表資料
        $list = $this->alias('a')
            ->with(['product.image.file', 'seckillSku'])
            ->join('product product', 'product.product_id=a.product_id')
            ->where('seckill_activity_id', '=', $seckill_activity_id)
            ->where('a.is_delete', '=', 0)
            ->where('product.is_delete', '=', 0)
            ->where('product.product_status', '=', 10)
            ->field('a.*')
            ->limit($limit)
            ->visible(['product.product_id', 'product.product_name', 'product.file_path'])
            ->select();

        foreach ($list as $product) {
            //計算銷量百分比
            $totalStock = $product['total_sales'] + $product['stock'];
            $product['saleRate'] = $totalStock ? round($product['total_sales'] / $totalStock * 100, 2) : 0;
            $seckill_arr = array_column($product['seckillSku']->toArray(), 'seckill_price');
            $product_arr = array_column($product['seckillSku']->toArray(), 'product_price');
            sort($seckill_arr);
            sort($product_arr);
            $product['seckill_price'] = current($seckill_arr);
            $product['product_price'] = current($product_arr);
            $real_product = $product['product'];
            $real_product['file_path'] = $product['product']['image'][0]['file_path'];
            unset($product['seckillSku']);
            unset($real_product['image']);
        }
        return $list;
    }

    /**
     * 列表頁秒殺商品
     * 目前未分頁，後續有可能會分頁
     */
    public function getActivityList($seckill_activity_id)
    {
        // 獲取列表資料
        $list = $this->alias('a')
            ->with(['product.image.file', 'seckillSku'])
            ->join('product product', 'product.product_id=a.product_id')
            ->where('seckill_activity_id', '=', $seckill_activity_id)
            ->where('a.is_delete', '=', 0)
            ->where('product.is_delete', '=', 0)
            ->where('product.product_status', '=', 10)
            ->field('a.*')
            ->visible(['product.product_id', 'product.product_name', 'product.file_path'])
            ->select();

        foreach ($list as $product) {
            $seckill_arr = array_column($product['seckillSku']->toArray(), 'seckill_price');
            $product_arr = array_column($product['seckillSku']->toArray(), 'product_price');
            sort($seckill_arr);
            sort($product_arr);
            $product['seckill_price'] = current($seckill_arr);
            $product['product_price'] = current($product_arr);
            $product['product']['file_path'] = $product['product']['image'][0]['file_path'];
            unset($product['seckillSku']);
            unset($product['product']['image']);
        }
        return $list;
    }

    public function getSeckillDetail($seckill_product_id)
    {
        $result = $this->with(['product' => ['image.file', 'contentImage.file'], 'seckillSku.productSku.image'])
            ->where('seckill_product_id', '=', $seckill_product_id)->find();

        if (!empty($result)) {
            $seckill_arr = array_column($result->toArray()['seckillSku'], 'seckill_price');
            $product_arr = array_column($result->toArray()['seckillSku'], 'product_price');
            sort($seckill_arr);
            sort($product_arr);
            $result['seckill_price'] = current($seckill_arr);
            $result['line_price'] = current($product_arr);
            if (count($seckill_arr) > 1) {
                $res['seckill_high_price'] = end($seckill_arr);
                $res['line_high_price'] = end($product_arr);
            }
        }
        return $result;
    }
}
