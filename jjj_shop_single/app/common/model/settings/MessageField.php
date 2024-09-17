<?php

namespace app\common\model\settings;

use app\common\model\BaseModel;

/**
 * 訊息欄位模型
 */
class MessageField extends BaseModel
{
    protected $name = 'message_field';
    protected $pk = 'message_field_id';

    /**
     * 詳情
     */
    public static function detail($message_field_id)
    {
        return (new static())->find($message_field_id);
    }


}
