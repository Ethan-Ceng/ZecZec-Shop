<?php

namespace app\common\model\plus\card;

use app\common\model\BaseModel;
use app\common\model\settings\Region;

/**
 * 文章模型
 */
class Order extends BaseModel
{
    protected $name = 'card_order';
    protected $pk = 'order_id';

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
    /**
     * 詳情
     */
    public static function detail($order_id, $with = [])
    {
        return (new static())->with($with)->find($order_id);
    }

    /**
     * 關聯分類表
     * @return \think\model\relation\BelongsTo
     */
    public function card()
    {
        return $this->BelongsTo('app\\common\\model\\plus\\card\\Card', 'card_id', 'card_id');
    }

    /**
     * 關聯分類表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->BelongsTo('app\\common\\model\\user\\User', 'user_id', 'user_id');
    }

    /**
     * 關聯物流公司表
     * @return \think\model\relation\BelongsTo
     */
    public function express()
    {
        return $this->belongsTo('app\\common\\model\\settings\\Express', 'express_id', 'express_id');
    }

    /**
     * 關聯物流公司表
     * @return \think\model\relation\BelongsTo
     */
    public function code()
    {
        return $this->belongsTo('app\\common\\model\\plus\\card\\Code', 'code_id', 'code_id');
    }
}
