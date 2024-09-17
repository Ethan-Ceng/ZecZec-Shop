<?php

namespace app\common\model\product;
use app\common\model\BaseModel;

/**
 * 商品評價圖片模型
 */
class CommentImage extends BaseModel
{
    protected $name = 'comment_image';
    protected $pk = 'id';
    protected $updateTime = false;

    /**
     * 關聯檔案庫
     */
    public function file()
    {
        return $this->belongsTo('app\\common\\model\\file\\UploadFile', 'image_id', 'file_id')
            ->bind(['file_path', 'file_name', 'file_url']);
    }

}
