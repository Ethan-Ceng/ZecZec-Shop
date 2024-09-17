<?php

return [
    'paths'                    => [],
    'allowed_origins'          => ['*'], // 允許跨域請求的源（域名）
    'allowed_origins_patterns' => [],
    'allowed_methods'          => ['*'], // 允許的 HTTP 方法
    'allowed_headers'          => ['*'], // 允許的 HTTP 頭
    'exposed_headers'          => [], // 允許瀏覽器訪問的頭
    'max_age'                  => 0, // 預檢請求的有效期
    'supports_credentials'     => false,
];
