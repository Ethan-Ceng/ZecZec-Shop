<?php

namespace app\api\model\plus\bargain;

use app\common\enum\order\OrderSourceEnum;
use app\common\exception\BaseException;
use app\common\library\helper;
use app\common\model\plus\bargain\Product as BargainProductModel;
use app\api\model\product\Product as ProductModel;
use app\api\model\order\OrderProduct as OrderProductModel;

/**
 * 砍價商品模型
 */
class Product extends BargainProductModel
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
     * 獲取首頁砍價商品顯示
     */
    public function getProductList($bargain_activity_id, $limit)
    {
        // 獲取列表資料
        $list = $this->alias('a')
            ->with(['product.image.file', 'bargainSku'])
            ->join('product product', 'product.product_id=a.product_id')
            ->where('bargain_activity_id', '=', $bargain_activity_id)
            ->where('a.is_delete', '=', 0)
            ->where('product.is_delete', '=', 0)
            ->where('product.product_status', '=', 10)
            ->field('a.*')
            ->limit($limit)
            ->visible(['product.product_id', 'product.product_name', 'product.file_path'])
            ->select();

        foreach ($list as $product) {
            $bargain_arr = array_column($product['bargainSku']->toArray(), 'bargain_price');
            $product_arr = array_column($product['bargainSku']->toArray(), 'product_price');
            sort($bargain_arr);
            sort($product_arr);
            $product['bargain_price'] = current($bargain_arr);
            $product['product_price'] = current($product_arr);
            $real_product = $product['product'];
            $real_product['file_path'] = $product['product']['image'][0]['file_path'];
            unset($product['bargainSku']);
            unset($real_product['image']);
        }
        return $list;
    }

    /**
     * 獲取商品列表
     */
    public static function getBargainProduct($params, $user)
    {
        // 砍價任務詳情
        $bargain = self::detail($params['bargain_product_id'], ['bargainSku']);
        $task = Task::detail($params['bargain_task_id']);
        $order = OrderProductModel::alias('op')
            ->join('order o', 'o.order_id=op.order_id')
            ->where('o.user_id', '=', $user['user_id'])
            ->where('o.order_source', '=', OrderSourceEnum::BARGAIN)
            ->where('bill_source_id', '=', $params['bargain_task_id'])
            ->find();
        if ($order) {
            throw new BaseException(['msg' => '訂單已生成' . $order['order_product_id']]);
        }
        if (empty($task)) {
            throw new BaseException(['msg' => '任務不存在']);
        }
        if ($task['is_buy'] == 1) {
            throw new BaseException(['msg' => '當前砍價任務商品已購買']);
        }
        if (empty($bargain)) {
            throw new BaseException(['msg' => '商品不存在或已結束']);
        }
        // 商品詳情
        $product = ProductModel::detail($bargain['product_id']);
        // 商品sku資訊
        $bargain_sku = null;
        if ($product['spec_type'] == 10) {
            $bargain_sku = $bargain['bargainSku'][0];
        } else {
            //多規格
            foreach ($bargain['bargainSku'] as $sku) {
                if ($sku['bargain_product_sku_id'] == $params['bargain_product_sku_id']) {
                    $bargain_sku = $sku;
                    break;
                }
            }
        }
        if ($bargain_sku == null) {
            throw new BaseException(['msg' => '商品規格不存在']);
        }
        // 商品sku資訊
        $product['product_sku'] = self::getProductSku($product, $params['product_sku_id']);
        $product['bargain_sku'] = $bargain_sku;
        // 商品列表
        $productList = [$product->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($productList as &$item) {
            // 商品單價
            $item['product_price'] = $task['actual_price'];
            // 商品購買數量
            $item['total_num'] = 1;
            $item['spec_sku_id'] = $item['product_sku']['spec_sku_id'];
            // 商品購買總金額
            $item['total_price'] = $task['actual_price'];
            $item['bargain_product_sku_id'] = $bargain_sku['bargain_product_sku_id'];
            $item['product_sku_id'] = $params['product_sku_id'];
            $item['product_source_id'] = $bargain_sku['bargain_product_id'];
            $item['sku_source_id'] = $bargain_sku['bargain_product_sku_id'];
            // 砍價活動id
            $item['activity_id'] = $bargain['bargain_activity_id'];
            // 砍價訂單id
            $item['bill_source_id'] = $params['bargain_task_id'];
            // 砍價最大購買數
            $item['bargain_product'] = [
                'limit_num' => $bargain['limit_num']
            ];
        }
        return $productList;
    }


    /**
     * 指定的商品規格資訊
     */
    private static function getProductSku($product, $product_sku_id)
    {
        // 獲取指定的sku
        $productSku = [];
        foreach ($product['sku'] as $item) {
            if ($item['product_sku_id'] == $product_sku_id) {
                $productSku = $item;
                break;
            }
        }
        if (empty($productSku)) {
            return false;
        }
        // 多規格文字內容
        $productSku['product_attr'] = '';
        if ($product['spec_type'] == 20) {
            $specRelData = helper::arrayColumn2Key($product['spec_rel'], 'spec_value_id');
            $attrs = explode('_', $productSku['spec_sku_id']);
            foreach ($attrs as $specValueId) {
                $productSku['product_attr'] .= $specRelData[$specValueId]['spec']['spec_name'] . ':'
                    . $specRelData[$specValueId]['spec_value'] . '; ';
            }
        }
        return $productSku;
    }

    /**
     * 獲取列表頁砍價資料
     * 目前未分頁，後續有可能會分頁
     */
    public function getActivityList($bargain_activity_id)
    {
        // 獲取列表資料
        $list = $this->alias('a')
            ->with(['product.image.file', 'bargainSku'])
            ->join('product product', 'product.product_id=a.product_id')
            ->where('bargain_activity_id', '=', $bargain_activity_id)
            ->where('a.is_delete', '=', 0)
            ->where('product.is_delete', '=', 0)
            ->where('product.product_status', '=', 10)
            ->field('a.*')
            ->visible(['product.product_id', 'product.product_name', 'product.file_path'])
            ->select();
        if (!empty($list)) {
            foreach ($list as $product) {
                $bargain_arr = array_column($product['bargainSku']->toArray(), 'bargain_price');
                $product_arr = array_column($product['bargainSku']->toArray(), 'product_price');
                sort($bargain_arr);
                sort($product_arr);
                $product['bargain_price'] = current($bargain_arr);
                $product['product_price'] = current($product_arr);
                $product['product']['file_path'] = $product['product']['image'][0]['file_path'];
                unset($product['bargainSku']);
                unset($product['product']['image']);
            }
        }
        return $list;
    }


    /**
     * 拼團商品詳情
     */
    public function getBargainDetail($bargain_product_id)
    {
        $result = $this->with(['product' => ['image.file', 'contentImage.file'], 'bargainSku.productSku.image'])
            ->where('bargain_product_id', '=', $bargain_product_id)->find();

        if (!empty($result)) {
            $bargain_arr = array_column($result->toArray()['bargainSku'], 'bargain_price');
            $product_arr = array_column($result->toArray()['bargainSku'], 'product_price');
            sort($bargain_arr);
            sort($product_arr);
            $result['bargain_price'] = current($bargain_arr);
            $result['line_price'] = current($product_arr);
            if (count($bargain_arr) > 1) {
                $result['bargain_high_price'] = end($bargain_arr);
                $result['line_high_price'] = end($product_arr);
            }
        }
        return $result;
    }
}