<?php
use think\facade\Env;

return [
    // 預設使用的資料庫連線配置
    'default' => Env::get('database.driver', 'mysql'),

    // 自定義時間查詢規則
    'time_query_rule' => [],

    // 自動寫入時間戳欄位
    // true為自動識別型別 false關閉
    // 字串則明確指定時間欄位型別 支援 int timestamp datetime date
    'auto_timestamp' => true,

    // 時間欄位取出後的預設時間格式
    'datetime_format' => 'Y-m-d H:i:s',

    // 資料庫連線配置資訊
    'connections' => [
        'mysql' => [
            // 資料庫型別
            'type' => Env::get('database.type', 'mysql'),
            // 伺服器地址
            'hostname' => Env::get('database.hostname', '127.0.0.1'),
            // 資料庫名
            'database' => Env::get('database.database', ''),
            // 使用者名稱
            'username' => Env::get('database.username', 'root'),
            // 密碼
            'password' => Env::get('database.password', ''),
            // 埠
            'hostport' => Env::get('database.hostport', '3306'),
            // 資料庫連線引數
            'params' => [],
            // 資料庫編碼預設採用utf8
            'charset' => Env::get('database.charset', 'utf8'),
            // 資料庫表字首
            'prefix' => Env::get('database.prefix', ''),

            // 資料庫部署方式:0 集中式(單一伺服器),1 分散式(主從伺服器)
            'deploy' => 0,
            // 資料庫讀寫是否分離 主從式有效
            'rw_separate' => false,
            // 讀寫分離後 主伺服器數量
            'master_num' => 1,
            // 指定從伺服器序號
            'slave_no' => '',
            // 是否嚴格檢查欄位是否存在
            'fields_strict' => true,
            // 是否需要斷線重連
            'break_reconnect' => false,
            // 監聽SQL
            'trigger_sql' => true,
            // 開啟欄位快取
            'fields_cache' => false,
            // 欄位快取路徑
            'schema_cache_path' => app()->getRuntimePath() . 'schema' . DIRECTORY_SEPARATOR,
        ],

        // 更多的資料庫配置資訊
    ],
];
