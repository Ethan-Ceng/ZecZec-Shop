<?php

namespace app\common\library\storage\engine;

use think\facade\Filesystem;

/**
 * 本地檔案驅動
 */
class Local extends Server
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 上傳圖片檔案
     */
    public function upload()
    {
        return $this->isInternal ? $this->uploadByInternal() : $this->uploadByExternal();
    }

    /**
     * 外部上傳(指使用者上傳,需驗證檔案型別、大小)
     */
    private function uploadByExternal()
    {
        $saveName = '';
        // 驗證檔案並上傳
        try {
            $saveName = Filesystem::disk('public')->putFile('', $this->file);
        } catch (\Exception $e) {
            log_write('檔案上傳異常:' . $e->getMessage());
        }
        return $saveName;
    }

    /**
     * 內部上傳(指系統上傳,信任模式)
     */
    private function uploadByInternal()
    {
        // 上傳目錄
        $uplodDir = public_path() . '/uploads';
        // 要上傳圖片的本地路徑
        $realPath = $this->getRealPath();
        if (!rename($realPath, "{$uplodDir}/$this->fileName")) {
            $this->error = 'upload write error';
            return false;
        }
        return true;
    }

    /**
     * 刪除檔案
     */
    public function delete($fileName)
    {
        // 檔案所在目錄
        $filePath = public_path() . "/uploads/{$fileName}";
        return !file_exists($filePath) ?: unlink($filePath);
    }

    /**
     * 返回檔案路徑
     */
    public function getFileName()
    {
        return $this->fileName;
    }

}
