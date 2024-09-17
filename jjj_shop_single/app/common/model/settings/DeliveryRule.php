<?php

namespace app\common\model\settings;

use app\common\model\BaseModel;

/**
 * 配送模板區域及運費模型
 */
class DeliveryRule extends BaseModel
{
    protected $name = 'delivery_rule';
    protected $pk = 'rule_id';
    protected $updateTime = false;

    /**
     * 追加欄位
     * @var array
     */
    protected $append = ['region_data'];

    /**
     * 地區集轉為陣列格式
     */
    public function getRegionDataAttr($value, $data)
    {
        return explode(',', $data['region']);
    }
}
