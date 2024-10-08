<?php

namespace app\common\model\order;

use app\common\model\settings\Region;
use app\common\model\BaseModel;

/**
 * Class OrderAddress
 */
class OrderAddress extends BaseModel
{
    protected $name = 'order_address';
    protected $pk = 'order_address_id';
    protected $updateTime = false;

    /**
     * 追加欄位
     * @var string[]
     */
    protected $append = ['region'];

    /**
     * 地區名稱
     * @param $value
     * @param $data
     * @return array
     */
    public function getRegionAttr($value, $data)
    {
        return [
            'province' => Region::getNameById($data['province_id']),
            'city' => Region::getNameById($data['city_id']),
            'region' => $data['region_id'] == 0 ? '' : Region::getNameById($data['region_id']),
        ];
    }

    /**
     * 獲取完整地址
     * @return string
     */
    public function getFullAddress()
    {
        return $this['region']['province'] . $this['region']['city'] . $this['region']['region'] . $this['detail'];
    }

    //修改收貨人資訊
    public function updateAddress($data)
    {
        return $this->save($data);
    }

}