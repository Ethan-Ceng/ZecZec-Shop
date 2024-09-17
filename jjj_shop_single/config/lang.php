<?php
// +----------------------------------------------------------------------
// | 多語言設定
// +----------------------------------------------------------------------

use think\facade\Env;

return [
    // 預設語言
    'default_lang'    => Env::get('lang.default_lang', 'zh-cn'),
    // 允許的語言列表
    'allow_lang_list' => [],
    // 多語言自動偵測變數名
    'detect_var'      => 'lang',
    // 是否使用Cookie記錄
    'use_cookie'      => true,
    // 多語言cookie變數
    'cookie_var'      => 'think_lang',
    // 擴充套件語言包
    'extend_list'     => [],
    // Accept-Language轉義為對應語言包名稱
    'accept_language' => [
        'zh-hans-cn' => 'zh-cn',
    ],
    // 是否支援語言分組
    'allow_group'     => false,
];
