# 小程式


```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'wx3cf0f39249eb0exx',
    'secret' => 'f1c242f4f28f735d4687abb469072axx',

    // 下面為可選項
    // 指定 API 呼叫返回結果的型別：array(default)/collection/object/raw/自定義類名
    'response_type' => 'array',

    'log' => [
        'level' => 'debug',
        'file' => __DIR__.'/wechat.log',
    ],
];

$app = Factory::miniProgram($config);
```

`$app` 在所有相關小程式的文件都是指 `Factory::miniProgram` 得到的例項，就不在每個頁面單獨寫了。
