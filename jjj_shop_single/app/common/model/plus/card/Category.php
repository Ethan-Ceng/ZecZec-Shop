<?php

namespace app\common\model\plus\card;

use app\common\model\BaseModel;

/**
 * 文章分類模型
 */
class Category extends BaseModel
{
    protected $name = 'card_category';
    protected $pk = 'category_id';

    /**
     * 所有分類
     */
    public static function getALL()
    {
        $model = new static;
        return $model->order(['sort' => 'asc', 'create_time' => 'asc'])->select();
    }

}