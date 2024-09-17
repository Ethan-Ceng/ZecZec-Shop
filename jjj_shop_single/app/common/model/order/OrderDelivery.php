<?php

namespace app\common\model\order;

use app\common\model\BaseModel;

/**
 * Class OrderAddress
 */
class OrderDelivery extends BaseModel
{
    protected $name = 'order_delivery';
    protected $pk = 'order_delivery_id';

    /**
     * 關聯物流公司表
     * @return \think\model\relation\BelongsTo
     */
    public function express()
    {
        return $this->belongsTo('app\\common\\model\\settings\\Express', 'express_id', 'express_id');
    }

    /**
     * 獲取包裹資訊
     * @param $value
     * @return array
     */
    public function getDeliveryDataAttr($value, $data)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * 設定包裹資訊
     * @param $value
     * @return array
     */
    public function setDeliveryDataAttr($value, $data)
    {
        return $value ? json_encode($value) : '';
    }

    /**
     * 詳情
     */
    public static function detail($order_delivery_id)
    {
        return (new static())->find($order_delivery_id);
    }

}