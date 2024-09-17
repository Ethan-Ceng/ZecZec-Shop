# 工具

提供微信網頁開發 JS-SDK 相關方法

## 配置

```php
<?php
use EasyWeChat\OfficialAccount\Application;

$config = [...];

$app = new Application($config);

$utils = $app->getUtils();
```

## 生成 JS-SDK 簽名

:book: [官方文件 - JS-SDK說明文件](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html)

```php
$config = $utils->buildJsSdkConfig(
    url: $url, 
    jsApiList: [],
    openTagList: [], 
    debug: false, 
);

// print
[
    "appId" => "wx...",
    "jsApiList" => [],
    "nonceStr" => "string",
    "openTagList" => [],
    "signature" =>  "sign",
    "timestamp" =>  "timestamp"
];

```
