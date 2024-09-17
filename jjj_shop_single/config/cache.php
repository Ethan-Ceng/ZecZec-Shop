<?php
use think\facade\Env;

// +----------------------------------------------------------------------
// | 快取設定
// +----------------------------------------------------------------------
$rootPath = dirname(__DIR__);
return [
    // 預設快取驅動
    'default' => Env::get('cache.driver', 'file'),

    // 快取連線方式配置
    'stores'  => [
        'file' => [
            // 驅動方式
            'type'       => 'File',
            // 快取儲存目錄
            'path'       => "{$rootPath}/runtime/cache/",
            // 快取字首
            'prefix'     => '',
            // 快取有效期 0表示永久快取
            'expire'     => 0,
            // 快取標籤字首
            'tag_prefix' => 'tag:',
            // 序列化機制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // 更多的快取連線
    ],
];
