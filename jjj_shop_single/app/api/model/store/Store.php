<?php

namespace app\api\model\store;

use app\common\model\store\Store as StoreModel;

/**
 * 商家門店模型
 */
class Store extends StoreModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * 獲取門店列表
     */
    public function getList($is_check = null, $longitude = '', $latitude = '', $limit = false)
    {
        $model = $this;
        // 是否支援自提核銷
        $is_check && $model = $model->where('is_check', '=', $is_check);
        // 獲取數量
        $limit != false && $model = $model->limit($limit);
        // 獲取門店列表資料
        $data = $model->where('is_delete', '=', '0')
            ->where('status', '=', '1')
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->select();
        // 根據距離排序
        if (!empty($longitude) && !empty($latitude)) {
            $map = $this->mapChange($latitude, $longitude);
            return $this->sortByDistance($data, $map['lng'], $map['lat']);
        }
        return $data;
    }

    /**
     * 根據距離排序
     */
    private function sortByDistance($data, $longitude, $latitude)
    {
        // 根據距離排序
        $list = $data->isEmpty() ? [] : $data->toArray();
        $sortArr = [];
        foreach ($list as &$store) {
            // 計算距離
            $distance = self::getDistance($longitude, $latitude, $store['longitude'], $store['latitude']);
            // 排序列
            $sortArr[] = $distance;
            $store['distance'] = $distance;
            if ($distance >= 1000) {
                $distance = bcdiv($distance, 1000, 2);
                $store['distance_unit'] = $distance . 'km';
            } else
                $store['distance_unit'] = $distance . 'm';
        }
        // 根據距離排序
        array_multisort($sortArr, SORT_ASC, $list);
        return $list;
    }

    /**
     * 獲取兩個座標點的距離
     */
    private static function getDistance($ulon, $ulat, $slon, $slat)
    {
        // 地球半徑
        $R = 6378137;
        // 將角度轉為狐度
        $radLat1 = deg2rad($ulat);
        $radLat2 = deg2rad($slat);
        $radLng1 = deg2rad($ulon);
        $radLng2 = deg2rad($slon);
        // 結果
        $s = acos(cos($radLat1) * cos($radLat2) * cos($radLng1 - $radLng2) + sin($radLat1) * sin($radLat2)) * $R;
        // 精度
        $s = round($s * 10000) / 10000;
        return round($s);
    }

    /**
     * 根據門店id集獲取門店列表
     */
    public function getListByIds($storeIds)
    {
        // 獲取商品列表資料
        return $this->with(['logo'])
            ->where('is_delete', '=', '0')
            ->where('status', '=', '1')
            ->where('store_id', 'in', $storeIds)
            ->select();
    }

    /**
     * 騰訊地圖---->百度地圖
     * @param double $lat 緯度
     * @param double $lng 經度
     */
    private function mapChange($lat, $lng)
    {
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng;
        $y = $lat;
        $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);
        $lng = $z * cos($theta) + 0.0065;
        $lat = $z * sin($theta) + 0.006;
        return array('lng' => $lng, 'lat' => $lat);
    }

}