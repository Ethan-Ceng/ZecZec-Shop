<?php

namespace app\api\service\points;

use app\common\library\helper;
use app\api\model\settings\Setting as SettingModel;

/**
 * 積分抵扣類
 */
class PointsDeductService
{
    private $productList;

    public function __construct($productList)
    {
        $this->productList = $productList;
    }

    public function setProductPoints($maxPointsNumCount, $actualPointsNum)
    {
        // 計算實際積分抵扣數量
        $this->setproductListPointsNum($maxPointsNumCount, $actualPointsNum);
        // 總抵扣數量
        $totalPointsNum = helper::getArrayColumnSum($this->productList, 'points_num');
        // 填充餘數
        $this->setproductListPointsNumFill($actualPointsNum, $totalPointsNum);
        $this->setproductListPointsNumDiff($actualPointsNum, $totalPointsNum);
        // 計算實際積分抵扣金額
        $this->setproductListPointsMoney();
        return true;
    }

    /**
     * 計算實際積分抵扣數量
     */
    private function setproductListPointsNum($maxPointsNumCount, $actualPointsNum)
    {
        foreach ($this->productList as &$product) {
            if (!$product['is_points_discount']) continue;
            $product['points_num'] = round($product['max_points_num'] / $maxPointsNumCount * $actualPointsNum, 2);
        }
    }

    /**
     * 計算實際積分抵扣金額
     */
    private function setproductListPointsMoney()
    {
        $setting = SettingModel::getItem('points');
        foreach ($this->productList as &$product) {
            if (!$product['is_points_discount']) continue;
            $product['points_money'] = helper::bcmul($product['points_num'], $setting['discount']['discount_ratio']);
        }
    }

    private function setproductListPointsNumFill($actualPointsNum, $totalPointsNum)
    {
        if ($totalPointsNum === 0) {
            $temReducedMoney = $actualPointsNum;
            foreach ($this->productList as &$product) {
                if (!$product['is_points_discount']) continue;
                if ($temReducedMoney === 0) break;
                $product['points_num'] = 1;
                $temReducedMoney--;
            }
        }
        return true;
    }

    private function setproductListPointsNumDiff($actualPointsNum, $totalPointsNum)
    {
        $tempDiff = $actualPointsNum - $totalPointsNum;
        foreach ($this->productList as &$product) {
            if (!$product['is_points_discount']) continue;
            if ($tempDiff < 1) break;
            $product['points_num'] = $product['points_num'] + 1;
            $tempDiff--;
        }
        return true;
    }

}