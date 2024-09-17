<?php

namespace app\common\library\storage;

use think\Exception;

/**
 * 儲存模組驅動
 */
class Driver
{
    private $config;    // upload 配置
    private $engine;    // 當前儲存引擎類
    protected $error;

    /**
     * 構造方法
     */
    public function __construct($config, $storage = null)
    {
        $this->config = $config;
        // 例項化當前儲存引擎
        $this->engine = $this->getEngineClass($storage);
    }

    public function validate($name, $fileInfo, $sence = 'image'){
        if($sence == 'image'){
            // 檔案校驗
            try{
                validate([$name=>[
                    'fileSize' => $this->config['max_image'] * 1024 * 1024, //2M
                    'fileExt' => 'jpg,jpeg,png,gif,bmp',
                    'fileMime' => 'image/jpeg,image/png,image/gif,image/bmp',
                ]],
                    [
                        $name.'.fileSize' => '最大可上傳'.$this->config['max_image'].'M圖片',
                        $name.'.fileExt' => '只能上傳jpg,jpeg,png,gif,bmp格式圖片',
                        $name.'.fileMime' => '只能上傳jpg,jpeg,png,gif,bmp格式圖片'
                    ]
                )->check([$name => $fileInfo]);
                return true;
            }catch (\Exception $e){
                $this->engine->error = $e->getMessage();
                return false;
            }
        }
        if($sence == 'video'){
            // 檔案校驗
            try{
                validate([$name=>[
                    'fileSize' => $this->config['max_video'] * 1024 * 1024, //20M
                    'fileExt' => 'mp4',
                    'fileMime' => 'video/mp4',
                ]],
                    [
                        $name.'.fileSize' => '最大可上傳'.$this->config['max_video'].'M影片',
                        $name.'.fileExt' => '只能上傳mp4格式影片',
                        $name.'.fileMime' => '只能上傳mp4格式影片'
                    ]
                )->check([$name => $fileInfo]);
                return true;
            }catch (\Exception $e){
                $this->engine->error = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    /**
     * 設定上傳的檔案資訊
     */
    public function setUploadFile($name = 'iFile')
    {
        return $this->engine->setUploadFile($name);
    }

    /**
     * 設定上傳的檔案資訊
     */
    public function setUploadFileByReal($filePath)
    {
        return $this->engine->setUploadFileByReal($filePath);
    }

    /**
     * 執行檔案上傳
     */
    public function upload()
    {
        return $this->engine->upload();
    }

    /**
     * 執行檔案刪除
     */
    public function delete($fileName)
    {
        return $this->engine->delete($fileName);
    }

    /**
     * 獲取錯誤資訊
     */
    public function getError()
    {
        return $this->engine->getError();
    }

    /**
     * 獲取檔案路徑
     */
    public function getFileName()
    {
        return $this->engine->getFileName();
    }

    /**
     * 返回檔案資訊
     */
    public function getFileInfo()
    {
        return $this->engine->getFileInfo();
    }

    /**
     * 獲取當前的儲存引擎
     */
    private function getEngineClass($storage = null)
    {
        $engineName = is_null($storage) ? $this->config['default'] : $storage;
        $classSpace = __NAMESPACE__ . '\\engine\\' . ucfirst($engineName);
        if (!class_exists($classSpace)) {
            throw new Exception('未找到儲存引擎類: ' . $engineName);
        }
        return new $classSpace($this->config['engine'][$engineName]);
    }

}
