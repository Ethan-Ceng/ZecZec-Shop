<?php

namespace app\common\library\printer\engine;

/**
 * 小票印表機驅動基類
 */
abstract class Basics
{
    protected $config;  // 印表機配置
    protected $times;   // 列印聯數(次數)
    protected $error;   // 錯誤資訊

    /**
     * 建構函式
     */
    public function __construct($config, $times)
    {
        $this->config = $config;
        $this->times = $times;
    }

    /**
     * 執行列印請求
     */
    abstract protected function printTicket($content);

    /**
     * 返回錯誤資訊
     */
    public function getError()
    {
        return $this->error;
    }

}