<?php

namespace app\shop\model\file;

use app\common\model\file\UploadGroup as UploadGroupModel;

/**
 * 檔案庫分組模型
 */
class UploadGroup extends UploadGroupModel
{
    /**
     * 獲取列表記錄
     */
    public function getList($groupType = '')
    {
        $model = $this;
        !empty($groupType) && $model = $model->where('group_type', '=', trim($groupType));
        return $model->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->select();
    }

    /**
     * 新增新記錄
     */
    public function add($data)
    {
        return $this->save(array_merge([
            'app_id' => self::$app_id,
            'sort' => 100
        ], $data));
    }

    /**
     * 更新記錄
     */
    public function edit($data)
    {
        return $this->save($data) !== false;
    }

    /**
     * 刪除記錄
     */
    public function remove()
    {
        // 更新該分組下的所有檔案
        (new UploadFile)->where('group_id', '=', $this['group_id'])
            ->update(['group_id' => 0]);
        // 刪除分組
        return $this->save(['is_delete' => 1]);
    }

}
