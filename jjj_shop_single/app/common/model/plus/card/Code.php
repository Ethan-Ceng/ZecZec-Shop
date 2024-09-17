<?php

namespace app\common\model\plus\card;

use app\common\model\BaseModel;

/**
 * 文章模型
 */
class Code extends BaseModel
{
    protected $name = 'card_code';
    protected $pk = 'code_id';

    /**
     * 詳情
     */
    public static function detail($code_id)
    {
        return (new static())->with(['card'])->find($code_id);
    }

    /**
     * 關聯分類表
     * @return \think\model\relation\BelongsTo
     */
    public function card()
    {
        return $this->BelongsTo('app\\common\\model\\plus\\card\\Card', 'card_id', 'card_id');
    }
}
