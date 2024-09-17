<?php

namespace app\common\model\shop;

use app\common\model\BaseModel;

/**
 * 滿減模型
 */
class FullReduce extends BaseModel
{
    protected $pk = 'fullreduce_id';
    protected $name = 'shop_fullreduce';

    /**
     * 獲取詳情
     */
    public static function detail($fullreduce_id)
    {
        return (new static())->find($fullreduce_id);
    }

    /**
     * 列表,系統滿減
     */
    public function getAll()
    {
        return $this->where('is_delete', '=', 0)
            ->where('product_id', '=', 0)
            ->order(['create_time' => 'asc'])
            ->select();
    }

    /**
     * 列表,系統滿減
     */
    public function getProductAll($product_id)
    {
        return $this->where('is_delete', '=', '0')
            ->where('product_id', '=', $product_id)
            ->order(['create_time' => 'asc'])
            ->select();
    }
}