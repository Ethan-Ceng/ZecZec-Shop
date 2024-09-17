<?php

namespace app\common\service\delivery;

use app\common\library\helper;
use app\common\model\settings\Setting as SettingModel;
use app\common\enum\order\OrderTypeEnum;

/**
 * 快遞配送服務類
 */
class ExpressService
{
    private $appId;   // 商城id
    private $cityId;    // 使用者收貨城市id
    private $productList;  // 訂單商品列表
    private $orderType;  // 訂單型別 (主商城、拼團)

    /**
     * 配送服務類構造方法
     */
    public function __construct(
        $appId,
        $cityId,
        $productList,
        $orderType = OrderTypeEnum::MASTER
    )
    {
        $this->appId = $appId;
        $this->cityId = $cityId;
        $this->productList = $productList;
        $this->orderType = $orderType;
    }

    /**
     * 根據使用者收貨城市id 驗證是否在商品配送規則中
     */
    public function getNotInRuleProduct()
    {
        if ($this->cityId) {
            foreach ($this->productList as $product) {
                if($product['is_virtual'] == 1){
                    continue;
                }
                $cityIds = [];
                foreach ($product['delivery']['rule'] as $item)
                    $cityIds = array_merge($cityIds, $item['region_data']);
                if (!in_array($this->cityId, $cityIds))
                    return $product;
            }

        }
        return false;
    }

    /**
     * 設定訂單商品的運費
     */
    public function setExpressPrice()
    {
        // 訂單商品總金額
        $orderTotalPrice = helper::getArrayColumnSum($this->productList, 'total_price');
        foreach ($this->productList as &$product) {
            // 如果是虛擬物品，則為0
            if($product['is_virtual'] == 1){
                $product['express_price'] = 0;
            }else{
                $product['express_price'] = $this->onCalcProductfreight($product, $orderTotalPrice);
            }
        }
        return true;
    }

    /**
     * 獲取訂單最終運費
     */
    public function getTotalFreight()
    {
        if (empty($this->productList)) {
            return 0.00;
        }
        // 所有商品的運費金額
        $expressPriceArr = helper::getArrayColumn($this->productList, 'express_price');
        if (empty($expressPriceArr)) {
            return 0.00;
        }
        // 計算最終運費
        return $this->freightRule($expressPriceArr);
    }

    /**
     * 計算商品的配送費用
     */
    private function onCalcProductfreight(&$product, $orderTotalPrice)
    {
        // 判斷是否滿足滿額包郵條件
        if ($this->isFullFree($product['product_id'], $orderTotalPrice)) {
            return 0.00;
        }
        // 當前收貨城市配送規則
        $rule = $this->getCityDeliveryRule($product);
        if(!$rule){
            return 0.00;
        }
        // 商品總重量
        $totalWeight = helper::bcmul($product['product_sku']['product_weight'], $product['total_num']);
        // 商品總數量or總重量
        $total = $product['delivery']['method']['value'] == 10 ? $product['total_num'] : $totalWeight;
        if ($total <= $rule['first']) {
            return helper::number2($rule['first_fee']);
        }
        // 續件or續重 數量
        $additional = $total - $rule['first'];
        if ($additional <= $rule['additional']) {
            return helper::number2(helper::bcadd($rule['first_fee'], $rule['additional_fee']));
        }
        // 計算續重/件金額
        if ($rule['additional'] < 1) {
            // 配送規則中續件為0
            $additionalFee = 0.00;
        } else {
            $additionalFee = helper::bcdiv($rule['additional_fee'], $rule['additional']) * $additional;
        }
        return helper::number2(helper::bcadd($rule['first_fee'], $additionalFee));
    }

    /**
     * 判斷是否滿足滿額包郵條件
     */
    private function isFullFree($productId, $orderTotalPrice)
    {
        // 非商城主訂單不參與滿額包郵
        if ($this->orderType !== OrderTypeEnum::MASTER) {
            return false;
        }
        // 獲取滿額包郵設定
        $options = SettingModel::getItem('full_free', $this->appId);
        if (
            $options['is_open'] == false
            || $orderTotalPrice < $options['money']
            /*|| in_array($productId, $options['notin_product'])
            || in_array($this->cityId, $options['notin_region']['citys'])*/
        ) {
            return false;
        }
        return true;
    }

    /**
     * 根據城市id獲取規則資訊
     */
    private function getCityDeliveryRule($product)
    {
        foreach ($product['delivery']['rule'] as $item) {
            if (in_array($this->cityId, $item['region_data'])) {
                return $item;
            }
        }
        return false;
    }

    /**
     * 根據運費組合策略 計算最終運費
     */
    private function freightRule($expressPriceArr)
    {
        $expressPrice = 0.00;
        switch (SettingModel::getItem('trade', $this->appId)['freight_rule']) {
            case '10':    // 策略1: 疊加
                $expressPrice = array_sum($expressPriceArr);
                break;
            case '20':    // 策略2: 以最低運費結算
                $expressPrice = min($expressPriceArr);
                break;
            case '30':    // 策略3: 以最高運費結算
                $expressPrice = max($expressPriceArr);
                break;
        }
        return $expressPrice;
    }

}