<?php

namespace app\api\service\fullreduce;

use app\common\library\helper;

/**
 * 滿減抵扣金額
 */
class FullDeductService
{
    private $actualReducedMoney;

    public function setProductFullreduceMoney($productList, $reducedMoney)
    {
        // 統計訂單商品總金額,(單位分)
        $orderTotalPrice = 0;
        foreach ($productList as &$product) {
            $product['total_price'] *= 100;
            $orderTotalPrice += $product['total_price'];
        }
        // 計算實際抵扣金額
        $this->setActualReducedMoney($reducedMoney, $orderTotalPrice);
        // 實際抵扣金額為0，
        if ($this->actualReducedMoney > 0) {
            // 計算商品的價格權重
            $productList = $this->getProductListWeight($productList, $orderTotalPrice);
            // 計算商品優惠券抵扣金額
            $this->setProductListMoney($productList);
            // 總抵扣金額
            $totalReduceMoney = helper::getArrayColumnSum($productList, 'fullreduce_money');
            $this->setProductListMoneyFill($productList, $totalReduceMoney);
            $this->setProductListMoneyDiff($productList, $totalReduceMoney);
        }
        return $productList;
    }

    public function getActualReducedMoney()
    {
        return $this->actualReducedMoney;
    }

    private function setActualReducedMoney($reducedMoney, $orderTotalPrice)
    {
        $reducedMoney *= 100;
        $this->actualReducedMoney = ($reducedMoney >= $orderTotalPrice) ? $orderTotalPrice - 1 : $reducedMoney;
    }

    private function arraySortByWeight($productList)
    {
        return array_sort($productList, 'weight', true);
    }

    private function getProductListWeight($productList, $orderTotalPrice)
    {
        foreach ($productList as &$product) {
            $product['weight'] = $product['total_price'] / $orderTotalPrice;
        }
        return $this->arraySortByWeight($productList);
    }

    private function setProductListMoney(&$productList)
    {
        foreach ($productList as &$product) {
            $product['fullreduce_money'] = bcmul($this->actualReducedMoney, sprintf("%.20f", $product['weight']));
        }
        return true;
    }

    private function setProductListMoneyFill(&$productList, $totalReduceMoney)
    {
        if ($totalReduceMoney === 0) {
            $temReducedMoney = $this->actualReducedMoney;
            foreach ($productList as &$product) {
                if ($temReducedMoney === 0) break;
                $product['fullreduce_money'] = 1;
                $temReducedMoney--;
            }
        }
        return true;
    }

    private function setProductListMoneyDiff(&$productList, $totalReduceMoney)
    {
        $tempDiff = $this->actualReducedMoney - $totalReduceMoney;
        foreach ($productList as &$product) {
            if ($tempDiff < 1) break;
            $product['fullreduce_money']++ && $tempDiff--;
        }
        return true;
    }

}