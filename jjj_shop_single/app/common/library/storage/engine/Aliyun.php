<?php

namespace app\common\library\storage\engine;

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * 阿里雲端儲存引擎 (OSS)
 */
class Aliyun extends Server
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
        try {
            $ossClient = new OssClient(
                $this->config['access_key_id'],
                $this->config['access_key_secret'],
                $this->config['domain'],
                true
            );
            $ossClient->uploadFile(
                $this->config['bucket'],
                $this->fileName,
                $this->getRealPath()
            );
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 刪除檔案
     */
    public function delete($fileName)
    {
        try {
            $ossClient = new OssClient(
                $this->config['access_key_id'],
                $this->config['access_key_secret'],
                $this->config['domain'],
                true
            );
            $ossClient->deleteObject($this->config['bucket'], $fileName);
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 返回檔案路徑
     */
    public function getFileName()
    {
        return $this->fileName;
    }

}
