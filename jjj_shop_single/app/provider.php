<?php
use app\common\exception\ExceptionHandler;
use app\Request;

// 容器Provider定義檔案
return [
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandler::class,
];
