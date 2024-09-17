<?php

namespace app\api\model\plus\advance;

use app\common\exception\BaseException;
use app\common\model\plus\advance\Product as AdvanceProductModel;
use app\api\model\product\Product as ProductModel;
use app\api\model\order\Order as OrderModel;

/**
 * 預售商品模型
 */
class Product extends AdvanceProductModel
{
    /*
     * 獲取列表
     */
    public function getList($params)
    {
        $model = $this;
        if (isset($params['search']) && $params['search']) {
            $model = $model->where('product.product_name', 'like', '%' . $params['search'] . '%');
        }
        // 獲取列表資料
        $list = $model->alias('a')
            ->with(['product.image.file', 'sku'])
            ->join('product product', 'product.product_id=a.product_id')
            ->where('a.is_delete', '=', 0)
            ->where('a.status', '=', 10)
            ->where('product.is_delete', '=', 0)
            ->where('product.product_status', '=', 10)
            ->where('a.end_time', '>', time())
            ->field('a.*')
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->paginate($params);
        foreach ($list as $key => $val) {
            $list[$key]['product_image'] = $val['product']['image'][0]['file_path'];
        }
        return $list;
    }

    /**
     * 獲取預售商品列表（用於訂單結算）
     */
    public static function getAdvanceProduct($params)
    {
        // 預售商品詳情
        $advance = self::detail($params['advance_product_id'], ['sku']);
        if (empty($advance) || $advance['status'] == 20 || $advance['is_delete'] == 1) {
            throw new BaseException(['msg' => '預售商品不存在或已結束']);
        }
        // 商品詳情
        $product = ProductModel::detail($advance['product_id']);
        // 預售商品sku資訊
        $advance_sku = null;
        if ($product['spec_type'] == 10) {
            $advance_sku = $advance['sku'][0];
        } else {
            //多規格
            foreach ($advance['sku'] as $sku) {
                if ($sku['advance_product_sku_id'] == $params['advance_product_sku_id']) {
                    $advance_sku = $sku;
                    break;
                }
            }
        }
        if ($advance_sku == null) {
            throw new BaseException(['msg' => '預售商品規格不存在']);
        }
        // 商品sku資訊
        $product['product_sku'] = ProductModel::getProductSku($product, $params['product_sku_id']);
        $product['advance_sku'] = $advance_sku;
        $product['advance'] = $advance;
        // 商品列表
        $productList = [$product->hidden(['category', 'content', 'image', 'sku'])];
        // 只會有一個商品
        foreach ($productList as &$item) {
            // 商品定金
            $item['front_price'] = $advance['money'];
            // 商品立減金額
            $item['reduce_money'] = $advance_sku['advance_price'];
            // 商品單價
            $item['product_price'] = $advance_sku['product_price'];
            // 商品購買數量
            $item['total_num'] = $params['product_num'];
            $item['spec_sku_id'] = $item['product_sku']['spec_sku_id'];
            // 商品購買總金額
            $item['total_price'] = $advance_sku['product_price'] * $item['total_num'];
            // 商品購買總定金
            $item['total_front_price'] = $item['front_price'] * $item['total_num'];
            $item['advance_product_sku_id'] = $advance_sku['advance_product_sku_id'];
            $item['product_sku_id'] = $params['product_sku_id'];
            $item['product_source_id'] = $advance_sku['advance_product_id'];
            $item['sku_source_id'] = $advance_sku['advance_product_sku_id'];
            // 預售商品最大購買數
            $item['advance_product'] = [
                'limit_num' => $advance['limit_num']
            ];
        }
        return $productList;
    }

    /**
     * 獲取預售商品列表（用於訂單結算）
     */
    public static function getAdvanceOrderProduct($params)
    {
        // 預售商品詳情
        $order = OrderModel::detail($params['order_id']);
        if (empty($order) || $order['pay_status']['value'] == 20) {
            throw new BaseException(['msg' => '尾款訂單不存在']);
        }
        $params['delivery'] = $order['delivery_type']['value'];
        $params['order'] = $order;
        $productList = $order['product'];
        foreach ($productList as &$item) {
            $product = ProductModel::where('product_id', '=', $item['product_id'])
                ->with(['delivery' => ['rule']])
                ->find();
            // 是否允許使用會員折扣
            $item['is_enable_grade'] = $item['is_user_grade'];
            $item['delivery_id'] = $product['delivery_id'];
            $item['delivery'] = $product['delivery'];
            // 商品sku資訊
            $item['product_sku'] = ProductModel::getProductSku($product, $item['spec_sku_id']);
            $item['product_image'] = isset($item['image']['file_path']) ? $item['image']['file_path'] : '';
            $item['virtual_auto'] = $product['virtual_auto'];
            $item['virtual_content'] = $product['virtual_content'];
            $item['content'] = $product['content'];

        }
        $order['product'] = $productList;
        $order['params'] = $params;
        return $order;
    }

}
