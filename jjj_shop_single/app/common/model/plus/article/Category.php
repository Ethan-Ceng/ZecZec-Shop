<?php

namespace app\common\model\plus\article;

use app\common\model\BaseModel;

/**
 * 文章分類模型
 */
class Category extends BaseModel
{
    protected $name = 'article_category';
    protected $pk = 'category_id';

    /**
     * 所有分類
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getALL()
    {
        $model = new static;
        return $model->order(['sort' => 'asc', 'create_time' => 'asc'])->select();
    }

}