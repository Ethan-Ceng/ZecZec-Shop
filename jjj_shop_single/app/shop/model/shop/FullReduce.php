<?php

namespace app\shop\model\shop;

use app\common\model\shop\FullReduce as FullReduceModel;

/**
 * 滿減模型
 */
class FullReduce extends FullReduceModel
{
    /**
     * 獲取列表記錄
     */
    public function getList($data)
    {
        return $this->where('is_delete', '=', 0)
            ->where('product_id', '=', 0)
            ->order(['create_time' => 'asc'])
            ->paginate($data);
    }

    /**
     * 新增記錄
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        return $this->save($data);
    }

    /**
     * 軟刪除
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }

}