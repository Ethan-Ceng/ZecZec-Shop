<?php

namespace app\api\model\product;

use app\common\model\product\Category as CategoryModel;

/**
 * 商品分類模型
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

}