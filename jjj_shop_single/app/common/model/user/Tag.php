<?php

namespace app\common\model\user;

use app\common\model\BaseModel;

/**
 * 使用者等級模型
 */
class Tag extends BaseModel
{
    protected $pk = 'tag_id';
    protected $name = 'tag';

    /**
     * 獲取詳情
     */
    public static function detail($tag_id)
    {
        return self::find($tag_id);
    }

    /**
     * 獲取詳情
     */
    public static function getAll()
    {
        return self::select();
    }
}