<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 應用入口檔案 ]
namespace think;

require __DIR__ . '/../vendor/autoload.php';

// 執行HTTP應用並響應
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);
