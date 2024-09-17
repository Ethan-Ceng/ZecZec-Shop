<?php

namespace app\shop\model\settings;

use app\common\model\settings\ReturnAddress as ReturnAddressModel;

/**
 * 退貨地址模型
 */
class ReturnAddress extends ReturnAddressModel
{
    /**
     * 獲取列表
     */
    public function getList($limit = 10)
    {
        return $this->order(['sort' => 'asc'])
            ->where('is_delete', '=', 0)
            ->paginate($limit);
    }

    /**
     * 獲取全部收貨地址
     */
    public function getAll()
    {
        return $this->order(['sort' => 'asc'])
            ->where('is_delete', '=', 0)
            ->select();
    }

    /**
     * 新增新記錄
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
     * 刪除記錄
     */
    public function remove()
    {
        return $this->save(['is_delete' => 1]);
    }

}