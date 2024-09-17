<?php

namespace app\api\model\product;

use app\common\model\product\CommentImage as CommentImageModel;

/**
 * 商品圖片模型
 */
class CommentImage extends CommentImageModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'app_id',
        'create_time',
    ];

}
