<?php
// +----------------------------------------------------------------------
// | 應用設定
// +----------------------------------------------------------------------

use think\facade\Env;

return [

    // 應用地址
    'app_host' => Env::get('app.host', ''),
    // 應用的名稱空間
    'app_namespace' => '',
    // 是否啟用路由
    'with_route' => true,
    // 是否啟用事件
    'with_event' => true,
    // 預設應用
    'default_app' => 'shop',
    // 預設時區
    'default_timezone' => 'Asia/Shanghai',

    // 應用對映（自動多應用模式有效）
    'app_map' => [],
    // 域名繫結（自動多應用模式有效）
    'domain_bind' => [],
    // 禁止URL訪問的應用列表（自動多應用模式有效）
    'deny_app_list' => [],

    // 異常頁面的模板檔案
    'exception_tmpl' => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 錯誤顯示資訊,非除錯模式有效
    'error_message' => '頁面錯誤！請稍後再試～',
    // 顯示錯誤資訊
    'show_error_msg' => true,
    // 預設多應用
    'auto_multi_app' => true,
    //後臺token加密
    'salt' => 'jjjshop_!@#$%*&',
];
