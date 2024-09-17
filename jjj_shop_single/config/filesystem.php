<?php

use think\facade\Env;

return [
    // 預設磁碟
    'default' => Env::get('filesystem.driver', 'local'),
    // 磁碟列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            // 磁碟型別
            'type'       => 'local',
            // 磁碟路徑
            'root'       => app()->getRootPath() . 'public/uploads',
            // 磁碟路徑對應的外部URL路徑
            'url'        => '/uploads',
            // 可見性
            'visibility' => 'public',
        ],
        // 更多的磁碟配置資訊
    ],
];
