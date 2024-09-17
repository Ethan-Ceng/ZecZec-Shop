<?php

namespace app\api\model\plus\bargain;

use app\common\model\plus\bargain\Active as ActiveModel;

/**
 * 砍價模型
 */
class Active extends ActiveModel
{
    /**
     * 獲取砍價活動詳情
     */
    public function getDetail($activeId)
    {
        $model = static::detail($activeId, 'product.sku');
        if (empty($model) || $model['is_delete'] == true || $model['status'] == false) {
            $this->error = '很抱歉，該砍價商品不存在或已下架';
            return false;
        }
        return $model;
    }

    /**
     * 取最近要結束的一條記錄
     */
    public static function getActive()
    {
        return (new static())->where('start_time', '<', time())
            ->where('end_time', '>', time())
            ->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->find();
    }

    /**
     * 獲取砍價商品列表
     */
    public function activityList()
    {
        return  $this->where('start_time', '<=', time())
            ->where('end_time', '>=', time())
            ->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }
}
