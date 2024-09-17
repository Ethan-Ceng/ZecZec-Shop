<?php


namespace app\common\model\store;

use app\common\model\BaseModel;

/**
 * 門店店員模型
 */
class Clerk extends BaseModel
{
    protected $pk = 'clerk_id';
    protected $name = 'store_clerk';

    /**
     * 關聯使用者表
     */
    public function user()
    {
        return $this->BelongsTo('app\common\model\user\User', 'user_id', 'user_id');
    }

    /**
     * 關聯門店表
     */
    public function store()
    {
        return $this->BelongsTo('app\\common\\model\\store\\Store', 'store_id', 'store_id');
    }

    /**
     * 店員詳情
     */
    public static function detail($where)
    {
        $filter = is_array($where) ? $where : ['clerk_id' => $where];
        return (new static())->where(array_merge(['is_delete' => 0], $filter))->find();
    }

    /**
     * 狀態
     */
    public function getStatusAttr($value)
    {
        $status = [0 => '停用', 1 => '啟用'];
        return ['text' => $status[$value], 'value' => $value];
    }
}