<?php

namespace app\common\library\printer;

use app\common\exception\BaseException;
use app\common\enum\settings\PrinterTypeEnum;

/**
 * 小票印表機驅動
 */
class Driver
{
    private $printer;    // 當前印表機
    private $engine;     // 當前印表機引擎類

    // 印表機引擎列表
    private static $engineList = [
        PrinterTypeEnum::FEI_E_YUN => 'Feie',
        PrinterTypeEnum::PRINT_CENTER => 'PrintCenter',
        PrinterTypeEnum::XP_YUN => 'Xpyun',
    ];

    /**
     * 構造方法
     */
    public function __construct($printer)
    {
        // 當前印表機
        $this->printer = $printer;
        // 例項化當前印表機引擎
        $this->engine = $this->getEngineClass();
    }

    /**
     * 執行列印請求
     */
    public function printTicket($content)
    {
        return $this->engine->printTicket($content);
    }

    /**
     * 獲取錯誤資訊
     */
    public function getError()
    {
        return $this->engine->getError();
    }

    /**
     * 獲取當前的印表機引擎類
     */
    private function getEngineClass()
    {
        $engineName = self::$engineList[$this->printer['printer_type']['value']];
        $classSpace = __NAMESPACE__ . "\\engine\\{$engineName}";
        if (!class_exists($classSpace)) {
            throw new BaseException("未找到印表機引擎類: {$engineName}");
        }
        return new $classSpace($this->printer['printer_config'], $this->printer['print_times']);
    }

}
