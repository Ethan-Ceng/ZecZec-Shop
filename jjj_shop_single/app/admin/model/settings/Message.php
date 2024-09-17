<?php

namespace app\admin\model\settings;

use app\common\model\settings\Message as MessageModel;

class Message extends MessageModel
{
    /**
     * 獲取全部
     */
    public static function getAll()
    {
        $model = new static;
        return $model->where('is_delete', '=', 0)->order(['sort' => 'asc'])->select();
    }

    /**
     * 新增
     */
    public function add($data)
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

    /**
     * 更新記錄
     */
    public function edit($data)
    {
        return $this->save($data);
    }

}