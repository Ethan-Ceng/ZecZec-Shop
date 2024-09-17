<?php

namespace app\common\model\settings;

use think\facade\Cache;
use app\common\library\helper;
use app\common\model\BaseModel;
/**
 * 地區模型
 */
class Region extends BaseModel
{
    protected $name = 'region';
    protected $pk = 'id';
    protected $createTime = false;
    protected $updateTime = false;

    /**
     * 型別自動轉換
     * @var array
     */
    protected $type = [
        'id' => 'integer',
        'pid' => 'integer',
        'level' => 'integer',
    ];

    // 當前資料版本號
    private static $version = '1.2.3';

    // 縣級市別名 (相容微信端命名)
    private static $county = [
        '省直轄縣級行政區劃',
        '自治區直轄縣級行政區劃',
    ];

    /**
     * 根據id獲取地區名稱
     */
    public static function detail($id)
    {
        return (new static())->find($id);
    }

    /**
     * 根據id獲取地區名稱
     */
    public static function getNameById($id)
    {
        return $id > 0 ? self::getCacheAll()[$id]['name'] : '其他';
    }

    /**
     * 根據名稱獲取地區id
     */
    public static function getIdByName($name, $level = 0, $pid = 0)
    {
        // 相容：微信端"省直轄縣級行政區劃"
        if (in_array($name, static::$county)) {
            $name = '直轄縣級';
        }
        $data = self::getCacheAll();
        foreach ($data as $item) {
            if ($item['name'] == $name && $item['level'] == $level && $item['pid'] == $pid)
                return $item['id'];
        }
        return 0;
    }

    /**
     * 獲取所有地區(樹狀結構)
     */
    public static function getCacheTree()
    {
        return static::getCacheData('tree');
    }

    /**
     * 獲取所有地區列表
     */
    public static function getCacheAll()
    {
        return static::getCacheData('all');
    }

    /**
     * 獲取所有地區的總數
     */
    public static function getCacheCounts()
    {
        return static::getCacheData('counts');
    }

    /**
     * 獲取快取中的資料(存入靜態變數)
     */
    private static function getCacheData($item = null)
    {
        static $cacheData = [];
        if (empty($cacheData)) {
            $static = new static;
            $cacheData = $static->regionCache();
        }
        if (is_null($item)) {
            return $cacheData;
        }
        return $cacheData[$item];
    }

    /**
     * 獲取地區快取
     */
    private function regionCache()
    {
        // 快取的資料
        $complete = Cache::get('region');
        // 如果存在快取則返回快取的資料，否則從資料庫中查詢
        // 條件1: 獲取快取資料
        // 條件2: 資料版本號要與當前一致
        if (
            !empty($complete)
            && isset($complete['version'])
            && $complete['version'] == self::$version
        ) {
            return $complete;
        }
        // 所有地區
        $allList = $tempList = $this->getAllList();
        // 已完成的資料
        $complete = [
            'all' => $allList,
            'tree' => $this->getTreeList($allList),
            'counts' => $this->getCount($allList),
            'version' => self::$version,
        ];
        // 寫入快取
        Cache::tag('cache')->set('region', $complete);
        return $complete;
    }

    private static function getCount($allList)
    {
        $counts = [
            'total' => count($allList),
            'province' => 0,
            'city' => 0,
            'region' => 0,
        ];
        $level = [1 => 'province', 2 => 'city', 3 => 'region'];
        foreach ($allList as $item) {
            $counts[$level[$item['level']]]++;
        }
        return $counts;
    }

    /**
     * 格式化為樹狀格式
     */
    private function getTreeList($allList)
    {
        $treeList = [];
        foreach ($allList as $pKey => $province) {
            if ($province['level'] == 1) {    // 省份
                $treeList[$province['id']] = $province;
                unset($allList[$pKey]);
                foreach ($allList as $cKey => $city) {
                    if ($city['level'] == 2 && $city['pid'] == $province['id']) {    // 城市
                        $treeList[$province['id']]['city'][$city['id']] = $city;
                        unset($allList[$cKey]);
                        foreach ($allList as $rKey => $region) {
                            if ($region['level'] == 3 && $region['pid'] == $city['id']) {    // 地區
                                $treeList[$province['id']]['city'][$city['id']]['region'][$region['id']] = $region;
                                unset($allList[$rKey]);
                            }
                        }
                    }
                }
            }
        }
        return $treeList;
    }

    /**
     * 從資料庫中獲取所有地區
     */
    private function getAllList()
    {
        $list = self::withoutGlobalScope()
            ->field('id, pid, name, level')
            ->select()
            ->toArray();
        return helper::arrayColumn2Key($list, 'id');
    }

    /**
     * 地區組裝供前端使用
     */
    public static function getRegionForApi(){
        $province_arr = [];
        $city_arr = [];
        $area_arr = [];
        $region = self::getCacheTree();
        foreach ($region as $province){
            $value = [
                'label' => $province['name'],
                'value' => $province['id']
            ];
            array_push($province_arr, $value);

            $city_arr_temp = [];
            $city_area_temp = [];
            if(!isset($province['city'])){
                continue;
            }
            foreach ($province['city'] as $city){
                $value = [
                    'label' => $city['name'],
                    'value' => $city['id']
                ];
                array_push($city_arr_temp, $value);
                $area_arr_temp = [];
                if(isset($city['region'])){
                    foreach ($city['region'] as $area) {
                        $value = [
                            'label' => $area['name'],
                            'value' => $area['id']
                        ];
                        array_push($area_arr_temp, $value);
                    }
                }
                array_push($city_area_temp, $area_arr_temp);
            }
            array_push($area_arr, $city_area_temp);
            array_push($city_arr, $city_arr_temp);
        }
        return [$province_arr, $city_arr, $area_arr];
    }
}
