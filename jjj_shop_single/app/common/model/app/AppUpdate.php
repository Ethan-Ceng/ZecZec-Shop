<?php

namespace app\common\model\app;

use app\common\model\BaseModel;

/**
 * app升級模型
 */
class AppUpdate extends BaseModel
{
    protected $name = 'app_update';
    protected $pk = 'update_id';

    /**
     * 詳情
     */
    public static function detail($update_id)
    {
        return (new static())->find($update_id);
    }

    /**
     * 最新一個詳情
     */
    public static function getLast()
    {
        return (new static())->where('is_delete', '=', 0)
            ->order(['create_time' => 'desc'])->find();
    }
}
