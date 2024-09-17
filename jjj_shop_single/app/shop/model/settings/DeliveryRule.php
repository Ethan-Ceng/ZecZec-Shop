<?php

namespace app\shop\model\settings;

use app\common\model\settings\DeliveryRule as DeliveryRuleModel;

/**
 * 配送模板區域及運費模型
 */
class DeliveryRule extends DeliveryRuleModel
{
//    protected $append = ['region_content'];
//
//    static $regionAll;
//    static $regionTree;
//
//    /**
//     * 可配送區域
//     */
//    public function getRegionContentAttr($value, $data)
//    {
//        // 當前區域記錄轉換為陣列
//        $regionIds = explode(',', $data['region']);
//
//        if (count($regionIds) === 373) return '全國';
//
//        // 所有地區
//        if (empty(self::$regionAll)) {
//            self::$regionAll = Region::getCacheAll();
//            self::$regionTree = Region::getCacheTree();
//        }
//        // 將當前可配送區域格式化為樹狀結構
//        $alreadyTree = [];
//        foreach ($regionIds as $regionId)
//            $alreadyTree[self::$regionAll[$regionId]['pid']][] = $regionId;
//        $str = '';
//        foreach ($alreadyTree as $provinceId => $citys) {
//            $str .= self::$regionTree[$provinceId]['name'];
//            if (count($citys) !== count(self::$regionTree[$provinceId]['city'])) {
//                $cityStr = '';
//                foreach ($citys as $cityId)
//                    $cityStr .= self::$regionTree[$provinceId]['city'][$cityId]['name'];
//                $str .= ' (<span class="am-link-muted">' . mb_substr($cityStr, 0, -1, 'utf-8') . '</span>)';
//            }
//            $str .= '、';
//        }
//        return mb_substr($str, 0, -1, 'utf-8');
//    }

}
