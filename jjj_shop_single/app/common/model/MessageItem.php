<?php

namespace app\common\model;


use app\common\model\BaseModel;
use app\common\model\product\Product;

class MessageItem extends BaseModel
{
    protected $name = 'message_item';
    protected $pk = 'message_item_id';


    // 獲取特定訊息盒子的所有訊息項
    public function getMessageItems($messageBoxId)
    {
        return $this->where('message_box_id', '=', $messageBoxId)
            ->order('create_time ASC')
            ->select();
    }
}