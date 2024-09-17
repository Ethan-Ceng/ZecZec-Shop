<?php
use think\facade\Env;

// +----------------------------------------------------------------------
// | 日誌設定
// +----------------------------------------------------------------------
$rootPath = dirname(__DIR__);
return [
    // 預設日誌記錄通道
    'default'      => Env::get('log.channel', 'file'),
    // 日誌記錄級別
    'level'        => [],
    // 日誌型別記錄的通道 ['error'=>'email',...]
    'type_channel' => [],
    // 關閉全域性日誌寫入
    'close'        => false,
    // 全域性日誌處理 支援閉包
    'processor'    => null,

    // 日誌通道列表
    'channels'     => [
        'file' => [
            // 日誌記錄方式
            'type'           => 'File',
            // 日誌儲存目錄
            'path'           => "{$rootPath}/runtime/logs/",
            // 單檔案日誌寫入
            'single'         => false,
            // 獨立日誌級別
            'apart_level'    => ['error','sql'],
            // 最大日誌檔案數量
            'max_files'      => 0,
            // 使用JSON格式記錄
            'json'           => false,
            // 日誌處理
            'processor'      => null,
            // 關閉通道日誌寫入
            'close'          => false,
            // 日誌輸出格式化
            'format'         => '[%s][%s] %s',
            // 是否即時寫入
            'realtime_write' => false,
        ],
        // 其它日誌通道配置
        'task' => [
            // 日誌記錄方式
            'type'           => 'File',
            // 日誌儲存目錄
            'path'           => "{$rootPath}/runtime/logs/task/",
            // 單檔案日誌寫入
            'single'         => false,
            // 獨立日誌級別
            'apart_level'    => ['error','sql'],
            // 最大日誌檔案數量
            'max_files'      => 0,
            // 使用JSON格式記錄
            'json'           => false,
            // 日誌處理
            'processor'      => null,
            // 關閉通道日誌寫入
            'close'          => false,
            // 日誌輸出格式化
            'format'         => '[%s][%s] %s',
            // 是否即時寫入
            'realtime_write' => false,
        ],
    ],

];
