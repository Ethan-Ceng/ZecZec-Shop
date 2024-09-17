<?php
// +----------------------------------------------------------------------
// | 路由設定
// +----------------------------------------------------------------------

return [
    // pathinfo分隔符
    'pathinfo_depr'         => '/',
    // URL偽靜態字尾
    'url_html_suffix'       => 'html',
    // URL普通方式引數 用於自動生成
    'url_common_param'      => true,
    // 是否開啟路由延遲解析
    'url_lazy_route'        => false,
    // 是否強制使用路由
    'url_route_must'        => false,
    // 合併路由規則
    'route_rule_merge'      => false,
    // 路由是否完全匹配
    'route_complete_match'  => false,
    // 是否開啟路由快取
    'route_check_cache'     => false,
    // 路由快取連線引數
    'route_cache_option'    => [],
    // 路由快取Key
    'route_check_cache_key' => '',
    // 訪問控制器層名稱
    'controller_layer'      => 'controller',
    // 空控制器名
    'empty_controller'      => 'Error',
    // 是否使用控制器字尾
    'controller_suffix'     => false,
    // 預設的路由變數規則
    'default_route_pattern' => '[\w\.]+',
    // 是否開啟請求快取 true自動快取 支援設定請求快取規則
    'request_cache'         => false,
    // 請求快取有效期
    'request_cache_expire'  => null,
    // 全域性請求快取排除規則
    'request_cache_except'  => [],
    // 預設控制器名
    'default_controller'    => 'Index',
    // 預設操作名
    'default_action'        => 'index',
    // 操作方法字尾
    'action_suffix'         => '',
    // 預設JSONP格式返回的處理方法
    'default_jsonp_handler' => 'jsonpReturn',
    // 預設JSONP處理方法
    'var_jsonp_handler'     => 'callback',
];
