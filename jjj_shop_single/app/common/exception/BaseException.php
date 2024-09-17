<?php

namespace app\common\exception;

use think\Exception;

/**
 * 自定義異常類的基類
 */
class BaseException extends Exception
{
    public $code = 0;
    public $message = 'invalid parameters';

    /**
     * 建構函式，接收一個關聯陣列
     */
    public function __construct($params = [])
    {
        parent::__construct();
        if (!is_array($params)) {
            return;
        }
        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
        if (array_key_exists('msg', $params)) {
            $this->message = $params['msg'];
        }
    }
}

