<?php

namespace app\common\model\product;

use app\common\model\BaseModel;

/**
 * 規格/屬性(值)模型
 */
class SpecValue extends BaseModel
{
    protected $name = 'spec_value';
    protected $pk = 'spec_value_id';
    protected $updateTime = false;

    /**
     * 關聯規格組表
     */
    public function spec()
    {
        return $this->belongsTo('Spec');
    }

    public function getSpecValue($data)
    {
        return $this->with(['spec'])->where('spec_value_id', 'in', $data)->select();
    }

}
