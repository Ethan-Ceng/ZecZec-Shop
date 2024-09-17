<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
/**
 * 售後單圖片模型
 */
class OrderRefundImage extends BaseModel
{
    protected $name = 'order_refund_image';
    protected $pk = 'id';
    protected $updateTime = false;

    /**
     * 關聯檔案庫
     * @return \think\model\relation\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo('app\\common\\model\\file\\UploadFile', 'image_id', 'file_id')
            ->bind(['file_path', 'file_name', 'file_url']);
    }

}
