<?php

namespace app\api\model\plus\points;

use app\common\exception\BaseException;
use app\common\model\plus\point\Product as PointProductModel;
use app\api\model\product\Product as ProductModel;

/**
 * 積分商城模型
 */
class Product extends PointProductModel
{
    /*
     * 獲取列表
     */
    public function getList($params)
    {
        // 獲取列表資料
        $list = $this->alias('a')
            ->with(['product.image.file','sku'])
            ->join('product product', 'product.product_id=a.product_id')
            ->where('a.is_delete', '=', 0)
            ->where('a.status', '=', 10)
            ->where('product.is_delete', '=', 0)
            ->where('product.product_status', '=', 10)
            ->field('a.*')
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->paginate($params);
        foreach ($list as $key => $val) {
            $list[$key]['product_image'] = $val['product']['image'][0]['file_path'];
        }
        return $list;
    }

    /**
     * 獲取積分任務的商品列表（用於訂單結算）
     */
    public static function getPointsProduct($params)
    {
        // 積分商品詳情
        $points = self::detail($params['point_product_id'],['sku']);
        if (empty($points) || $points['status'] == 20 || $points['is_delete'] == 1) {
            throw new BaseException(['msg' => '積分兌換商品不存在或已結束']);
        }

        // 積分商品詳情
        $product = ProductModel::detail($points['product_id']);
        // 積分商品sku資訊
        $point_sku = null;
        if($product['spec_type'] == 10){
            $point_sku = $points['sku'][0];
        }else{
            //多規格
            foreach ($points['sku'] as $sku){
                if($sku['point_product_sku_id'] == $params['point_product_sku_id']){
                    $point_sku = $sku;
                    break;
                }
            }
        }
        if ($point_sku == null) {
            throw new BaseException(['msg' => '積分兌換商品規格不存在']);
        }
        // 商品sku資訊
        $product['product_sku'] = ProductModel::getProductSku($product, $params['product_sku_id']);
        $product['point_sku'] = $point_sku;

        // 商品列表
        $productList = [$product->hidden(['category', 'content', 'image', 'sku'])];
        // 只會有一個商品
        foreach ($productList as &$item) {
            // 商品單價
            $item['product_price'] = $point_sku['point_money'];
            // 商品購買數量
            $item['total_num'] = $params['product_num'];
            $item['spec_sku_id'] = $item['product_sku']['spec_sku_id'];
            // 商品購買總金額
            $item['total_price'] =  $point_sku['point_money'] * $item['total_num'];
            $item['points_num'] = $point_sku['point_num'] * $item['total_num'];
            $item['point_product_sku_id'] = $point_sku['point_product_sku_id'];
            $item['product_sku_id'] = $params['product_sku_id'];
            $item['product_source_id'] = $point_sku['point_product_id'];
            $item['sku_source_id'] = $point_sku['point_product_sku_id'];
            // 積分商品最大兌換數
            $item['point_product'] = [
                'limit_num' => $points['limit_num']
            ];
            // 積分抵扣金額
            $item['points_money'] = ($item['product_sku']['product_price'] - $point_sku['point_money']) * $params['product_num'];
        }
        return $productList;
    }

    /**
     * 積分商品詳情
     */
    public function getPointDetail($point_product_id)
    {
        $result = self::detail($point_product_id, ['product' => ['image.file', 'contentImage.file'],'sku.productSku.image']);

        if (!empty($result)) {
            $point_arr = array_column($result->toArray()['sku'], 'assemble_price');
            $product_arr = array_column($result->toArray()['sku'], 'product_price');
            sort($point_arr);
            sort($product_arr);
            $result['point_price'] =  current($point_arr);
            $result['line_price'] = current($product_arr);
            if (count($point_arr) > 1) {
                $res['point_high_price'] = end($point_arr);
                $res['line_high_price'] = end($product_arr);
            }
        }
        return $result;
    }
}
