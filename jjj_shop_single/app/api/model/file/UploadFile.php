<?php

namespace app\api\model\file;

use app\common\model\file\UploadFile as UploadFileModel;

/**
 * 檔案庫模型
 */
class UploadFile extends UploadFileModel
{
    /**
     * 隱藏欄位
     * @var array
     */
    protected $hidden = [
        'app_id',
        'create_time',
    ];

}
