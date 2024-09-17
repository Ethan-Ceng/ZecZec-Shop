<?php

namespace app\common\library\storage\engine;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

/**
 * 七牛雲端儲存引擎
 */
class Qiniu extends Server
{
    private $config;

    /**
     * 構造方法
     */
    public function __construct($config)
    {
        parent::__construct();
        $this->config = $config;
    }

    /**
     * 執行上傳
     */
    public function upload()
    {
        // 要上傳圖片的本地路徑
        $realPath = $this->getRealPath();

        // 構建鑑權物件
        $auth = new Auth($this->config['access_key'], $this->config['secret_key']);

        // 要上傳的空間
        $token = $auth->uploadToken($this->config['bucket']);

        // 初始化 UploadManager 物件並進行檔案的上傳
        $uploadMgr = new UploadManager();

        // 呼叫 UploadManager 的 putFile 方法進行檔案的上傳
        list(, $error) = $uploadMgr->putFile($token, $this->fileName, $realPath);

        if ($error !== null) {
            $this->error = $error->message();
            return false;
        }
        return true;
    }

    /**
     * 刪除檔案
     */
    public function delete($fileName)
    {
        // 構建鑑權物件
        $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
        // 初始化 UploadManager 物件並進行檔案的上傳
        $bucketMgr = new BucketManager($auth);
        $error = $bucketMgr->delete($this->config['bucket'], $fileName);
        if (is_array($error) && count($error) >= 2 && $error[1] == NULL) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 返回檔案路徑
     */
    public function getFileName()
    {
        return $this->fileName;
    }

}
