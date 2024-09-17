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
// | Workerman設定 僅對 php think worker:gateway 指令有效
// +----------------------------------------------------------------------
return [
    // 擴充套件自身需要的配置
    'protocol'              => 'websocket', // 協議 支援 tcp udp unix http websocket text
    'host'                  => '0.0.0.0', // 監聽地址
    'port'                  => 2348, // 監聽埠
    'socket'                => '', // 完整監聽地址
    'context'               => [], // socket 上下文選項
    'register_deploy'       => true, // 是否需要部署register
    'businessWorker_deploy' => true, // 是否需要部署businessWorker
    'gateway_deploy'        => true, // 是否需要部署gateway

    // Register配置
    'registerAddress'       => '127.0.0.1:1236',

    // Gateway配置
    'name'                  => 'thinkphp',
    'count'                 => 1,
    'lanIp'                 => '127.0.0.1',
    'startPort'             => 2000,
    'daemonize'             => false,
    'pingInterval'          => 30,
    'pingNotResponseLimit'  => 0,
    'pingData'              => '{"type":"ping"}',

    // BusinsessWorker配置
    'businessWorker'        => [
        'name'         => 'BusinessWorker',
        'count'        => 1,
        'eventHandler' => '\think\worker\Events',
    ],

];
