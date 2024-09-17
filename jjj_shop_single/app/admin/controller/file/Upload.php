<?php

namespace app\admin\controller\file;

use app\JjjController;
use app\common\model\file\UploadImage;
use app\common\library\storage\Driver as StorageDriver;
use app\common\model\settings\Setting as SettingModel;

/**
 * 檔案庫管理
 */
class Upload extends JjjController
{
    /**
     * 圖片上傳介面
     */
    public function image($category_id, $file_type = 'image')
    {
        // 例項化儲存驅動
        $config = SettingModel::getSysConfig()['storage'];
        $StorageDriver = new StorageDriver($config);
        // 圖片資訊
        $fileInfo = request()->file('iFile');
        if (!$StorageDriver->validate('iFile', $fileInfo, $file_type)) {
            return json(['code' => 0, 'msg' => $StorageDriver->getError()]);
        }
        // 設定上傳檔案的資訊
        $StorageDriver->setUploadFile('iFile');
        // 上傳圖片
        $saveName = $StorageDriver->upload();
        if ($saveName == '') {
            return json(['code' => 0, 'msg' => '圖片上傳失敗' . $StorageDriver->getError()]);
        }
        $saveName = str_replace('\\', '/', $saveName);
        // 圖片上傳路徑
        $fileName = $StorageDriver->getFileName();
        // 新增檔案庫記錄
        $uploadFile = $this->addUploadFile($category_id, $fileName, $fileInfo, $file_type, $saveName);
        // 圖片上傳成功
        return json(['code' => 1, 'msg' => '圖片上傳成功', 'data' => $uploadFile]);
    }

    /**
     * 新增檔案庫上傳記錄
     */
    private function addUploadFile($group_id, $fileName, $fileInfo, $fileType, $savename)
    {
        // 儲存引擎
        $config = SettingModel::getSysConfig()['storage'];
        $storage = $config['default'];
        // 儲存域名
        if ($storage === 'local') {
            $image = base_url() . 'uploads/' . $savename;
        } else {
            $fileUrl = isset($config['engine'][$storage]['domain'])
                ? $config['engine'][$storage]['domain'] : '';
            $image = $fileUrl . '/' . $fileName;
        }
        // 新增檔案庫記錄
        $model = new UploadImage;
        $model->save([
            'parent_id' => $group_id > 0 ? (int)$group_id : 0,
            'image' => $image,
            'name' => $fileInfo->getOriginalName(),
        ]);
        return $model;
    }
}
