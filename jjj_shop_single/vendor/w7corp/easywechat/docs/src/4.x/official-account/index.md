## 公眾號

公眾號的各模組相對比較統一，用法如下：


```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'wx3cf0f39249eb0exx',
    'secret' => 'f1c242f4f28f735d4687abb469072axx',

    // 指定 API 呼叫返回結果的型別：array(default)/collection/object/raw/自定義類名
    'response_type' => 'array',
    
    //...
];

$app = Factory::officialAccount($config);
```

`$app` 在所有相關公眾號的文件都是指 `Factory::officialAccount` 得到的例項，就不在每個頁面單獨寫了。
