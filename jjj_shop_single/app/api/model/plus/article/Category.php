<?php

namespace app\api\model\plus\article;

use app\common\model\plus\article\Category as CategoryModel;

/**
 * 文章分類模型
 */
class Category extends CategoryModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'app_id',
        'update_time'
    ];

    public static function getList() {

    }

}
