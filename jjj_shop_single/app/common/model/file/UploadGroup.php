<?php

namespace app\common\model\file;

use app\common\model\BaseModel;

/**
 * 檔案庫分組模型
 */
class UploadGroup extends BaseModel
{
    protected $name = 'upload_group';
    protected $pk = 'group_id';

    /**
     * 分組詳情
     */
    public static function detail($group_id) {
        return (new static())->find($group_id);
    }

}
