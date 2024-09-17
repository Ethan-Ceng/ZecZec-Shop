<?php

namespace app\shop\model\plus\table;

use app\common\model\plus\table\Table as TableModel;

/**
 * 表單模型
 */
class Table extends TableModel
{
    /**
     * 獲取列表
     */
    public function getList($data)
    {
        return $this->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($data);
    }

    /**
     * 獲取優惠券列表
     */
    public function getAll()
    {
        return $this->field(['table_id', 'name'])->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->select();
    }

    /**
     * 新增新記錄
     */
    public function add($data)
    {
        $data['content'] = json_encode($data['tableData'], JSON_UNESCAPED_UNICODE);
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 更新記錄
     */
    public function edit($data)
    {
        $data['content'] = json_encode($data['tableData'], JSON_UNESCAPED_UNICODE);
        return $this->save($data);
    }

    /**
     * 刪除記錄 (軟刪除)
     */
    public function setDelete()
    {
        return $this->save([
            'is_delete' => 1
        ]);
    }

}
