<?php

namespace app\common\library\storage\engine;

use think\facade\Request;
use think\Exception;

/**
 * 儲存引擎抽象類
 */
abstract class Server
{
    protected $file;
    public $error;
    protected $fileName;
    protected $fileInfo;

    // 是否為內部上傳
    protected $isInternal = false;

    /**
     * 建構函式
     */
    protected function __construct()
    {
    }

    /**
     * 設定上傳的檔案資訊
     */
    public function setUploadFile($name)
    {
        // 接收上傳的檔案
        $this->file = Request::file($name);
        if (empty($this->file)) {
            throw new Exception('未找到上傳檔案的資訊');
        }
        // 生成儲存檔名
        $this->fileName = $this->buildSaveName();
    }

    /**
     * 設定上傳的檔案資訊
     */
    public function setUploadFileByReal($filePath)
    {
        // 設定為系統內部上傳
        $this->isInternal = true;
        // 檔案資訊
        $this->fileInfo = [
            'name' => basename($filePath),
            'size' => filesize($filePath),
            'tmp_name' => $filePath,
            'error' => 0,
        ];
        // 生成儲存檔名
        $this->fileName = $this->buildSaveName();
    }

    /**
     * 檔案上傳
     */
    abstract protected function upload();

    /**
     * 檔案刪除
     */
    abstract protected function delete($fileName);

    /**
     * 返回上傳後文件路徑
     */
    abstract public function getFileName();

    /**
     * 返回檔案資訊
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    protected function getRealPath()
    {
        $fileInfo = request()->file('iFile');
        return $fileInfo->getRealPath();
    }

    /**
     * 返回錯誤資訊
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 生成儲存檔名
     */
    private function buildSaveName()
    {
        // 要上傳圖片的本地路徑
        $realPath = $this->file->getPathname();
        // 副檔名
        $ext = $this->file->getOriginalExtension();

        // 自動生成檔名
        return date('YmdHis') . substr(md5($realPath), 0, 5)
            . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . ".{$ext}";
    }

}
