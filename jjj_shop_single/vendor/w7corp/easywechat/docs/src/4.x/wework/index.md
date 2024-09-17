## 企業微信

企業微信的使用與公眾號以及其它幾個應用的使用方式都是一致的，使用 `\EasyWeChat\Factory::work($config)` 來初始化：

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',

    'agent_id' => 100020, // 如果有 agend_id 則填寫
    'secret'   => 'xxxxxxxxxx',

    // 指定 API 呼叫返回結果的型別：array(default)/collection/object/raw/自定義類名
    'response_type' => 'array',

    'log' => [
        'level' => 'debug',
        'file' => __DIR__.'/wechat.log',
    ],
];

$app = Factory::work($config);
```

然後你就可以用 `$app` 來呼叫企業微信的服務了。