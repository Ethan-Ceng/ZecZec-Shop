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
// | Workerman設定 僅對 php think worker 指令有效
// +----------------------------------------------------------------------
return [
    // 擴充套件自身需要的配置
    'host'                  => '0.0.0.0', // 監聽地址
    'port'                  => 2346, // 監聽埠
    'root'                  => '', // WEB 根目錄 預設會定位public目錄
    'app_path'              => '', // 應用目錄 守護程序模式必須設定（絕對路徑）
    'file_monitor'          => false, // 是否開啟PHP檔案更改監控（除錯模式下自動開啟）
    'file_monitor_interval' => 2, // 檔案監控檢測時間間隔（秒）
    'file_monitor_path'     => [], // 檔案監控目錄 預設監控application和config目錄

    // 支援workerman的所有配置引數
    'name'                  => 'thinkphp',
    'count'                 => 4,
    'daemonize'             => false,
    'pidFile'               => '',
];
