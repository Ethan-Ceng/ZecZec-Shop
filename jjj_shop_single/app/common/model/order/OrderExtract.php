<?php

namespace app\common\model\order;
use app\common\model\BaseModel;
/**
 * 自提訂單聯絡方式記錄模型
 */
class OrderExtract extends BaseModel
{
    protected $name = 'order_extract';
    protected $pk = 'id';
    protected $updateTime = false;
}