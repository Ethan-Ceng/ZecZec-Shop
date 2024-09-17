<?php

namespace app\shop\model\file;

use app\common\library\storage\Driver as StorageDriver;
use app\common\model\file\UploadFile as UploadFileModel;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 圖片模型
 */
class UploadFile extends UploadFileModel
{

    /**
     * 獲取列表記錄
     */
    public function getList($groupId = 0, $fileType = '', $pageSize = 30, $isRecycle = -1)
    {
        $model = $this;
        // 檔案分組
        if ($groupId != 0) {
            $model = $model->where('group_id', '=', (int)$groupId);
        }
        // 檔案型別
        !empty($fileType) && $model = $model->where('file_type', '=', trim($fileType));
        // 是否在回收站
        $isRecycle > -1 && $model = $model->where('is_recycle', '=', (int)$isRecycle);
        // 查詢列表資料
        return $model->with(['upload_group'])
            ->where(['is_user' => 0, 'is_delete' => 0])
            ->order(['file_id' => 'desc'])
            ->paginate($pageSize);
    }

    /**
     * 軟刪除
     */
    public function softDelete($fileIds)
    {
        $list = $this->where('file_id', 'in', $fileIds)->select();
        foreach ($list as $item) {
            if ($item['storage'] == 'local') {
                $file = 'uploads/' . $item['save_name'];
                if (file_exists($file)) {
                    unlink($file);
                }
            } else {
                $config = SettingModel::getItem('storage');
                $config['default'] = $item['storage'];
                $StorageDriver = new StorageDriver($config);
                $StorageDriver->delete($item['file_name']);
            }
            $this->where('file_id', '=', $item['file_id'])->update(['is_delete' => 1]);
        }
        return true;
    }

    /**
     * 批次移動檔案分組
     */
    public function moveGroup($group_id, $fileIds)
    {
        return $this->where('file_id', 'in', $fileIds)->update(compact('group_id'));
    }
}
