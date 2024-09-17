<?php


namespace app\common\model\store;

use app\common\model\BaseModel;
use app\common\model\settings\Region as RegionModel;


/**
 * 門店訂單模型
 */
class Store extends BaseModel
{
    protected $pk = 'store_id';

    protected $name = 'store';

    protected $append = ['region'];

    /**
     * 關聯門店logo
     */
    public function logo()
    {
        return $this->hasOne("app\\common\\model\\file\\UploadFile", 'file_id', 'logo_image_id');
    }

    /**
     * 地區名稱
     */
    public function getRegionAttr($value, $data)
    {
        return [
            'province' => RegionModel::getNameById($data['province_id']),
            'city' => RegionModel::getNameById($data['city_id']),
            'region' => $data['region_id'] == 0 ? '' : RegionModel::getNameById($data['region_id']),
        ];
    }


    /**
     * 門店狀態
     */
    public function getStatusAttr($value)
    {
        $status = [0 => '停用', 1 => '啟用'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 是否支援自提核銷
     */
    public function getIsCheckAttr($value)
    {
        $status = [0 => '不支援', 1 => '支援'];
        return ['text' => $status[$value], 'value' => $value];
    }


    /**
     * 門店詳情
     */
    public static function detail($store_id)
    {
        return (new static())->with(['logo'])->where('store_id','=',$store_id)->find();
    }
}