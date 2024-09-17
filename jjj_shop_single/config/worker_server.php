<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | Workerman設定 僅對 php think worker:server 指令有效
// +----------------------------------------------------------------------
return [
    // 擴充套件自身需要的配置
    'protocol'       => 'websocket', // 協議 支援 tcp udp unix http websocket text
    'host'           => '0.0.0.0', // 監聽地址
    'port'           => 2345, // 監聽埠
    'socket'         => '', // 完整監聽地址
    'context'        => [], // socket 上下文選項
    'worker_class'   => '', // 自定義Workerman服務類名 支援陣列定義多個服務

    // 支援workerman的所有配置引數
    'name'           => 'jjjshop',
    'count'          => 4,
    'daemonize'      => false,
    'pidFile'        => '',

    // 支援事件回撥
    // onWorkerStart
    'onWorkerStart'  => function ($worker) {

    },
    // onWorkerReload
    'onWorkerReload' => function ($worker) {

    },
    // onConnect
    'onConnect'      => function ($connection) {

    },
    // onMessage
    'onMessage'      => function ($connection, $data) {
        $connection->send('receive success');
    },
    // onClose
    'onClose'        => function ($connection) {

    },
    // onError
    'onError'        => function ($connection, $code, $msg) {
        echo "error [ $code ] $msg\n";
    },
];
