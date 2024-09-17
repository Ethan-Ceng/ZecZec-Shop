<?php

namespace app\api\controller\file;

use app\api\controller\Controller;
use app\api\model\file\UploadFile as UploadFileModel;
use app\api\model\settings\Setting as SettingModel;
use app\common\library\storage\Driver as StorageDriver;

/**
 * 檔案庫管理
 */
class Upload extends Controller
{
    protected $config;
    protected $user;

    /**
     * 構造方法
     */
    public function initialize()
    {
        parent::initialize();
        // 儲存配置資訊
        $this->config = SettingModel::getItem('storage');
        // 驗證使用者
        $this->user = $this->getUser();
    }

    /**
     * 圖片上傳介面
     */
    public function image()
    {

        // 例項化儲存驅動
        $StorageDriver = new StorageDriver($this->config);
        // 圖片資訊
        $fileInfo = request()->file('iFile');
        if(!$StorageDriver->validate('iFile', $fileInfo, 'image')){
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
        $uploadFile = $this->addUploadFile($fileName, $fileInfo, 'image', $saveName);
        $data = [
            'file_id' => $uploadFile['file_id'],
            'file_path' => $uploadFile['file_path'],
        ];
        // 圖片上傳成功
        return json(['code' => 1, 'msg' => '圖片上傳成功', 'data' => $data]);
    }

    /**
     * 新增檔案庫上傳記錄
     */
    private function addUploadFile($fileName, $fileInfo, $fileType, $savename)
    {
        // 儲存引擎
        $storage = $this->config['default'];
        // 儲存域名
        $fileUrl = isset($this->config['engine'][$storage]['domain'])
            ? $this->config['engine'][$storage]['domain'] : '';
        // 新增檔案庫記錄
        $model = new UploadFileModel;
        $data = $this->postData();
        $model->add([
            'storage' => $storage,
            'file_url' => $fileUrl,
            'file_name' => $fileName,
            'save_name' => $savename,
            'file_size' => $fileInfo->getSize(),
            'file_type' => $fileType,
            'extension' => $fileInfo->getOriginalExtension(),
            'real_name' => $fileInfo->getOriginalName(),
            'is_user' => 1,
            'app_id' => $data['app_id']
        ]);
        return $model;
    }

}