<?php

namespace app\common\model\plus\card;

use app\common\model\BaseModel;

/**
 * 文章模型
 */
class Card extends BaseModel
{
    protected $name = 'card';
    protected $pk = 'card_id';

    /**
     * 關聯封面圖
     * @return \think\model\relation\HasOne
     */
    public function image()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }

    /**
     * 關聯分類表
     * @return \think\model\relation\BelongsTo
     */
    public function category()
    {
        return $this->BelongsTo('app\\common\\model\\plus\\card\\Category', 'category_id', 'category_id');
    }



    /**
     * 詳情
     */
    public static function detail($card_id)
    {
        return (new static())->with(['image', 'category'])->find($card_id);
    }

    /**
     * 所有分類
     */
    public static function getALL()
    {
        $model = new static;
        return $model->where('card_status', '=', 10)
            ->where('is_delete', '=', 0)
            ->order(['card_sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }
}
