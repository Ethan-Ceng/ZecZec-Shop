<?php

 
namespace app\common\model\user;

use think\Exception;

/**
 * 簡訊通知模組驅動
 */
class Driver
{
    private $config;    // 配置資訊
    private $engine;    // 當前簡訊引擎類
    private $engineName;    // 當前簡訊引擎名稱

    /**
     * 構造方法
     */
    public function __construct($config)
    {
        // 配置資訊
        $this->config = $config;
        // 當前引擎名稱
        $this->engineName = $this->config['default'];
        // 例項化當前儲存引擎
        $this->engine = $this->getEngineClass();
    }

    /**
     * 傳送簡訊通知
     */
    public function sendSms($mobile, $template_code, $templateParams)
    {
        return $this->engine->sendSms($mobile, $template_code, $templateParams);
    }

    /**
     * 獲取錯誤資訊
     */
    public function getError()
    {
        return $this->engine->getError();
    }

    /**
     * 獲取當前的儲存引擎
     */
    private function getEngineClass()
    {
        $classSpace = __NAMESPACE__ . '\\engine\\' . ucfirst($this->engineName);
        if (!class_exists($classSpace)) {
            throw new Exception('未找到儲存引擎類: ' . $this->engineName);
        }
        return new $classSpace($this->config['engine'][$this->engineName]);
    }

}
