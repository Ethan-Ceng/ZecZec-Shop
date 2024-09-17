# 工具

提供企業微信網頁開發 JS-SDK 相關方法

## 配置

```php
<?php
use EasyWeChat\Work\Application;

$config = [...];

$app = new Application($config);

$utils = $app->getUtils();
```

## 生成 config 介面配置

:book: [官方文件 - config介面配置 說明文件](https://open.work.weixin.qq.com/api/doc/90001/90144/90547)


```php
$config = $utils->buildJsSdkConfig(
    string $url,
    array $jsApiList,
    array $openTagList = [],
    bool $debug = false,
    bool $beta = true
);

// print
[
    'jsApiList' => ['api1','api2'],
    'openTagList' => ['openTag1','openTag2'],
    'debug' => false,
    'beta' => true,
    'url' => 'https://www.easywechat.com/',
    'nonceStr' => 'mock-nonce',
    'timestamp' => 1601234567,
    'appId' => 'mock-appid',
    'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767'
];
```

## 生成 agentConfig 介面配置

:book: [官方文件 - agentConfig介面配置 說明文件](https://open.work.weixin.qq.com/api/doc/90001/90144/94325)


```php
$config = $utils->buildJsSdkAgentConfig(
    int $agentId,
    string $url,
    array $jsApiList,
    array $openTagList = [],
    bool $debug = false
);

// print
[
    'jsApiList' => ['api1','api2'],
    'openTagList' => ['openTag1','openTag2'],
    'debug' => false,
    'url' => 'https://www.easywechat.com/',
    'nonceStr' => 'mock-nonce',
    'timestamp' => 1601234567,
    'corpid' => 'mock-corpid',
    'agentid' => 100001,
    'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767'
];
```