<?php

namespace app\shop\controller\file;

use app\JjjController;
use app\shop\model\file\UploadFile;
use app\common\library\storage\Driver as StorageDriver;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 檔案庫管理
 */
class Upload extends JjjController
{
    /**
     * 圖片上傳介面
     */
    public function image($group_id = -1, $file_type = 'image')
    {
        set_time_limit(0);
        // 例項化儲存驅動
        $config = SettingModel::getItem('storage');

        $StorageDriver = new StorageDriver($config);
        // 圖片資訊
        $fileInfo = request()->file('iFile');
        // 校驗
        if(!$StorageDriver->validate('iFile', $fileInfo, $file_type)){
            return json(['code' => 0, 'msg' => $StorageDriver->getError()]);
        }
        // 設定上傳檔案的資訊
        $StorageDriver->setUploadFile('iFile');

        // 上傳圖片
        $saveName = $StorageDriver->upload();
        if ($saveName == '') {
            return json(['code' => 0, 'msg' => '檔案上傳失敗' . $StorageDriver->getError()]);
        }

        $saveName = str_replace('\\','/',$saveName);

        // 圖片上傳路徑
        $fileName = $StorageDriver->getFileName();

        // 新增檔案庫記錄
        $uploadFile = $this->addUploadFile($group_id, $fileName, $fileInfo, $file_type, $saveName);
        // 圖片上傳成功
        return json(['code' => 1, 'msg' => '檔案上傳成功', 'data' => $uploadFile]);
    }

    /**
     * 新增檔案庫上傳記錄
     */
    private function addUploadFile($group_id, $fileName, $fileInfo, $fileType, $savename)
    {
        // 儲存引擎
        $config = SettingModel::getItem('storage');
        $storage = $config['default'];
        // 儲存域名
        $fileUrl = isset($config['engine'][$storage]['domain'])
            ? $config['engine'][$storage]['domain'] : '';
        // 新增檔案庫記錄
        $model = new UploadFile;
        $model->save([
            'group_id' => $group_id > 0 ? (int)$group_id : 0,
            'storage' => $storage,
            'file_url' => $fileUrl,
            'file_name' => $fileName,
            'save_name' => $savename,
            'file_size' => $fileInfo->getSize(),
            'file_type' => $fileType,
            'extension' => $fileInfo->getOriginalExtension(),
            'real_name' => $fileInfo->getOriginalName(),
            'app_id' => $model::$app_id
        ]);
        return $model;
    }
    /**
     * 批次移動檔案分組
     */
    public function moveFiles($group_id, $fileIds)
    {
        $model = new UploadFile;
        if ($model->moveGroup($group_id, $fileIds) !== false) {
            return $this->renderSuccess('移動成功');
        }
        return $this->renderError($model->getError() ?: '移動失敗');
    }
}
