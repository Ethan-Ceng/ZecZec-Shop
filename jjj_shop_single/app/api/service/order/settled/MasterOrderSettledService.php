<?php

namespace app\api\service\order\settled;

use app\api\model\order\Order as OrderModel;
use app\common\enum\order\OrderSourceEnum;

/**
 * 普通訂單結算服務類
 */
class MasterOrderSettledService extends OrderSettledService
{
    /**
     * 建構函式
     */
    public function __construct($user, $productList, $params)
    {
       parent::__construct($user, $productList, $params);
        //訂單來源
        $this->orderSource = [
            'source' => OrderSourceEnum::MASTER,
            'activity_id' => 0
        ];
       //自身構造,差異化規則
    }


    /**
     * 驗證訂單商品的狀態
     */
    public function validateProductList()
    {
        foreach ($this->productList as $product) {
            // 判斷商品是否下架
            if ($product['product_status']['value'] != 10) {
                $this->error = "很抱歉，商品 [{$product['product_name']}] 已下架";
                return false;
            }
            // 判斷商品庫存
            if ($product['total_num'] > $product['product_sku']['stock_num']) {
                $this->error = "很抱歉，商品 [{$product['product_name']}] 庫存不足";
                return false;
            }
            // 是否是會員商品
            if(count($product['grade_ids']) > 0 && $product['grade_ids'][0] != ''){
                if(!in_array($this->user['grade_id'], $product['grade_ids'])){
                    $this->error = '很抱歉，此商品僅特定會員可購買';
                    return false;
                }
            }
            // 判斷是否超過限購數量
            if($product['limit_num'] > 0){
                $hasNum = OrderModel::getHasBuyOrderNum($this->user['user_id'], $product['product_id']);
                if($hasNum + $product['total_num'] > $product['limit_num']){
                    $this->error = "很抱歉，購買超過此商品最大限購數量";
                    return false;
                }
            }
        }
        return true;
    }
}