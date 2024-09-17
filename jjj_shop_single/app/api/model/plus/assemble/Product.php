<?php

namespace app\api\model\plus\assemble;

use app\common\exception\BaseException;
use app\common\model\plus\assemble\Product as AssembleProductModel;
use app\api\model\product\Product as ProductModel;

/**
 * 限時拼團模型
 */
class Product extends AssembleProductModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'sales_initial',
        'total_sales',
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * 獲取首頁拼團商品顯示
     */
    public function getProductList($assemble_activity_id, $limit)
    {
        // 獲取列表資料
        $list = $this->alias('a')
            ->with(['product.image.file', 'assembleSku'])
            ->join('product product', 'product.product_id=a.product_id')
            ->where('assemble_activity_id', '=', $assemble_activity_id)
            ->where('a.is_delete', '=', 0)
            ->where('product.is_delete', '=', 0)
            ->where('product.product_status', '=', 10)
            ->field('a.*')
            ->limit($limit)
            ->visible(['product.product_id', 'product.product_name', 'product.file_path'])
            ->select();
        foreach ($list as $product) {
            $assemble_arr = array_column($product['assembleSku']->toArray(), 'assemble_price');
            $product_arr = array_column($product['assembleSku']->toArray(), 'product_price');
            sort($assemble_arr);
            sort($product_arr);
            $product['assemble_price'] = current($assemble_arr);
            $product['product_price'] = current($product_arr);
            $real_product = $product['product'];
            $real_product['file_path'] = $product['product']['image'][0]['file_path'];
            unset($product['assembleSku']);
            unset($real_product['image']);
        }
        return $list;
    }

    /**
     * 獲取列表頁拼團資料
     * 目前未分頁，後續有可能會分頁
     */
    public function getActivityList($assemble_activity_id)
    {
        // 獲取列表資料
        $list = $this->alias('a')
            ->with(['product.image.file', 'assembleSku'])
            ->join('product product', 'product.product_id=a.product_id')
            ->where('assemble_activity_id', '=', $assemble_activity_id)
            ->where('a.is_delete', '=', 0)
            ->where('product.is_delete', '=', 0)
            ->where('product.product_status', '=', 10)
            ->field('a.*')
            ->visible(['product.product_id', 'product.product_name', 'product.file_path'])
            ->select();

        foreach ($list as $product) {
            $assemble_arr = array_column($product['assembleSku']->toArray(), 'assemble_price');
            $product_arr = array_column($product['assembleSku']->toArray(), 'product_price');
            sort($assemble_arr);
            sort($product_arr);
            $product['assemble_price'] = current($assemble_arr);
            $product['product_price'] = current($product_arr);
            $product['product']['file_path'] = $product['product']['image'][0]['file_path'];
            unset($product['assembleSku']);
            unset($product['product']['image']);
        }
        return $list;
    }

    /**
     * 獲取拼團商品列表
     */
    public static function getAssembleProduct($params)
    {
        // 拼團詳情
        $assemble = self::detail($params['assemble_product_id'], ['assembleSku']);

        if (empty($assemble)) {
            throw new BaseException(['msg' => '拼團商品不存在或已結束']);
        }
        // 拼團商品詳情
        $product = ProductModel::detail($assemble['product_id']);
        // 拼團商品sku資訊
        $assemble_sku = null;
        if ($product['spec_type'] == 10) {
            $assemble_sku = $assemble['assembleSku'][0];
        } else {
            //多規格
            foreach ($assemble['assembleSku'] as $sku) {
                if ($sku['assemble_product_sku_id'] == $params['assemble_product_sku_id']) {
                    $assemble_sku = $sku;
                    break;
                }
            }
        }
        if ($assemble_sku == null) {
            throw new BaseException(['msg' => '拼團商品規格不存在']);
        }

        // 拼團商品sku資訊
        $product['product_sku'] = ProductModel::getProductSku($product, $params['product_sku_id']);
        $product['assemble_sku'] = $assemble_sku;
        // 拼團商品列表
        $productList = [$product->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($productList as &$item) {
            // 商品單價
            $item['product_price'] = $assemble_sku['assemble_price'];
            // 商品購買數量
            $item['total_num'] = $params['product_num'];
            $item['spec_sku_id'] = $item['product_sku']['spec_sku_id'];
            // 商品購買總金額
            $item['total_price'] = $assemble_sku['assemble_price'] * $item['total_num'];
            $item['point_num'] = $assemble_sku['point_num'];
            $item['assemble_product_sku_id'] = $assemble_sku['assemble_product_sku_id'];
            $item['product_sku_id'] = $params['product_sku_id'];
            $item['product_source_id'] = $assemble_sku['assemble_product_id'];
            $item['sku_source_id'] = $assemble_sku['assemble_product_sku_id'];
            // 拼團活動id
            $item['activity_id'] = $assemble['assemble_activity_id'];
            // 拼團訂單id
            $item['bill_source_id'] = $params['assemble_bill_id'];
            // 砍價最大購買數
            $item['assemble_product'] = [
                'limit_num' => $assemble['limit_num']
            ];
        }
        return $productList;
    }

    /**
     * 拼團商品詳情
     */
    public function getAssembleDetail($assemble_product_id)
    {
        $result = $this->with(['product' => ['image.file', 'contentImage.file'], 'assembleSku.productSku.image'])
            ->where('assemble_product_id', '=', $assemble_product_id)->find();

        if (!empty($result)) {
            $assemble_arr = array_column($result->toArray()['assembleSku'], 'assemble_price');
            $product_arr = array_column($result->toArray()['assembleSku'], 'product_price');
            sort($assemble_arr);
            sort($product_arr);
            $result['assemble_price'] = current($assemble_arr);
            $result['line_price'] = current($product_arr);
            if (count($assemble_arr) > 1) {
                $res['assemble_high_price'] = end($assemble_arr);
                $res['line_high_price'] = end($product_arr);
            }
        }
        return $result;
    }
}
