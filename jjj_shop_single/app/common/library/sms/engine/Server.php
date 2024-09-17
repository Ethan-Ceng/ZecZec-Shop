<?php

namespace app\common\library\sms\engine;


abstract class Server
{
    protected $error;

    /**
     * 返回錯誤資訊
     */
    public function getError()
    {
        return $this->error;
    }

}
