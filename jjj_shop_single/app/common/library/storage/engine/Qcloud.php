<?php

namespace app\common\library\storage\engine;

use Qcloud\Cos\Client;

/**
 * 騰訊雲端儲存引擎 (COS)
 */
class Qcloud extends Server
{
    private $config;
    private $cosClient;

    /**
     * 構造方法
     */
    public function __construct($config)
    {
        parent::__construct();
        $this->config = $config;
        // 建立COS控制類
        $this->createCosClient();
    }

    /**
     * 建立COS控制類
     */
    private function createCosClient()
    {
        $this->cosClient = new Client([
            'region' => $this->config['region'],
            'credentials' => [
                'secretId' => $this->config['secret_id'],
                'secretKey' => $this->config['secret_key'],
            ],
        ]);
    }

    /**
     * 執行上傳
     */
    public function upload()
    {
        // 上傳檔案
        // putObject(上傳介面，最大支援上傳5G檔案)
        try {
            $this->cosClient->putObject([
                'Bucket' => $this->config['bucket'],
                'Key' => $this->fileName,
                'Body' => fopen($this->getRealPath(), 'rb')
            ]);
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 刪除檔案
     */
    public function delete($fileName)
    {
        try {
            $this->cosClient->deleteObject(array(
                'Bucket' => $this->config['bucket'],
                'Key' => $fileName
            ));
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
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
