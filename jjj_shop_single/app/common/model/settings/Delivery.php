<?php

namespace app\common\model\settings;

use app\common\model\BaseModel;

/**
 * 配送模板模型
 */
class Delivery extends BaseModel
{
    protected $name = 'delivery';
    protected $pk = 'delivery_id';

    /**
     * 關聯配送模板區域及運費
     */
    public function rule()
    {
        return $this->hasMany('DeliveryRule');
    }

    /**
     * 計費方式
     */
    public function getMethodAttr($value)
    {
        $method = [10 => '按件數', 20 => '按重量'];
        return ['text' => $method[$value], 'value' => $value];
    }

    /**
     * 獲取全部
     */
    public static function getAll()
    {
        $model = new static;
        return $model->order(['sort' => 'asc'])->select();
    }

    /**
     * 獲取列表
     */
    public function getList($limit = 10)
    {
        return $this->with(['rule'])
            ->order(['sort' => 'asc'])
            ->paginate($limit);
    }

    /**
     * 運費模板詳情
     * @param $delivery_id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detail($delivery_id)
    {
        return (new static())->with(['rule'])->find($delivery_id);
    }

}
