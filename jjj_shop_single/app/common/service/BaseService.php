<?php

namespace app\common\service;

/**
 * 服務基類
 * Interface BaseService
 * @package app\common\model
 */
Class BaseService
{
    public $error = '';

    /**
     * 返回模型的錯誤資訊
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 是否存在錯誤
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->error);
    }
}