<?php

namespace app\common\model\plus\plus;

use app\common\model\BaseModel;

/**
 * 外掛模型
 */
class Category extends BaseModel
{
    protected $pk = 'plus_category_id';
    protected $name = 'plus_category';

    /**
     * 許可權資訊
     */
    public static function detail($where)
    {
        if(is_array($where)){
            return (new static())->where($where)->find();
        } else{
            return (new static())->where('plus_category_id', '=', $where)->find();
        }
    }
}
