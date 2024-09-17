<?php

namespace app\api\service\order\settled;

use app\api\model\order\Order as OrderModel;
use app\common\enum\order\OrderSourceEnum;
use app\common\model\settings\Setting as SettingModel;
use app\api\model\plus\assemble\BillUser as BillUserModel;
/**
 * 拼團商城訂單結算服務類
 */
class AssemblelOrderSettledService extends OrderSettledService
{
    private $config;

    /**
     * 建構函式
     */
    public function __construct($user, $productList, $params)
    {

        parent::__construct($user, $productList, $params);
        // 訂單來源
        $this->orderSource = [
            'source' => OrderSourceEnum::ASSEMBLE,
            'activity_id' => $productList[0]['activity_id']
        ];
        $this->config = SettingModel::getItem('assemble');
        // 自身構造,差異化規則
        $this->settledRule = array_merge($this->settledRule, [
            'is_coupon' => $this->config['is_coupon'],
            'is_agent' => $this->config['is_agent'],
            'is_point' => $this->config['is_point'],
            'is_user_grade' => false,     // 會員等級折扣
        ]);
    }

    /**
     * 驗證訂單商品的狀態
     */
    public function validateProductList()
    {
        foreach ($this->productList as $product) {
            // 判斷商品是否下架
            if ($product['product_status']['value'] != 10) {
                $this->error = "很抱歉，拼團商品已下架";
                return false;
            }
            // 判斷商品庫存
            if ($product['total_num'] > $product['assemble_sku']['assemble_stock']) {
                $this->error = "很抱歉，拼團商品庫存不足";
                return false;
            }
            // 參與過就不要再參加了
            if($product['bill_source_id'] > 0){
                $join_count = (new BillUserModel)->where('assemble_bill_id', '=', $product['bill_source_id'])
                    ->where('user_id', '=', $this->user['user_id'])
                    ->count();
                if($join_count > 0){
                    $this->error = "您已經參與此商品拼團，請勿重複參與";
                    return false;
                }
            }
            // 是否超過購買數
            $hasNum = OrderModel::getPlusOrderNum($this->user['user_id'], $product['product_source_id'], OrderSourceEnum::ASSEMBLE);
            if($hasNum + $product['total_num'] > $product['assemble_product']['limit_num']){
                $this->error = "很抱歉，你已購買或超過此商品最大拼團數量";
                return false;
            }
        }
        return true;
    }
}