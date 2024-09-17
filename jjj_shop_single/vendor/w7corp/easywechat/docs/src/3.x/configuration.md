# 配置


在前面我們已經講過，初始化 SDK 的時候方法就是建立一個 `EasyWeChat\Foundation\Application` 例項：

```php
use EasyWeChat\Foundation\Application;

$options = [
   // ...
];

$app = new Application($options);

/**
* 如果想要在Application例項化完成之後, 修改某一個options的值,
* 比如服務商+子商戶支付回撥場景, 所有子商戶訂單支付資訊都是透過同一個服務商的$option 配置進來的,
* 當oauth在微信端驗證完成之後, 可以透過動態設定merchant_id來區分具體是哪個子商戶
*/
$app['config']->set('oauth.callback','wechat/oauthcallback/'. $sub_merchant_id->id);
```

那麼配置的具體選項有哪些，下面是一個完整的列表：

```php
<?php

return [
    /**
     * Debug 模式，bool 值：true/false
     *
     * 當值為 false 時，所有的日誌都不會記錄
     */
    'debug'  => true,

    /**
     * 賬號基本資訊，請從微信公眾平臺/開放平臺獲取
     */
    'app_id'  => 'your-app-id',         // AppID
    'secret'  => 'your-app-secret',     // AppSecret
    'token'   => 'your-token',          // Token
    'aes_key' => '',                    // EncodingAESKey，安全模式與相容模式下請一定要填寫！！！

    /**
     * 日誌配置
     *
     * level: 日誌級別, 可選為：
     *         debug/info/notice/warning/error/critical/alert/emergency
     * permission：日誌檔案許可權(可選)，預設為null（若為null值,monolog會取0644）
     * file：日誌檔案位置(絕對路徑!!!)，要求可寫許可權
     */
    'log' => [
        'level'      => 'debug',
        'permission' => 0777,
        'file'       => '/tmp/easywechat.log',
    ],

    /**
     * OAuth 配置
     *
     * scopes：公眾平臺（snsapi_userinfo / snsapi_base），開放平臺：snsapi_login
     * callback：OAuth授權完成後的回撥頁地址
     */
    'oauth' => [
        'scopes'   => ['snsapi_userinfo'],
        'callback' => '/examples/oauth_callback.php',
    ],

    /**
     * 微信支付
     */
    'payment' => [
        'merchant_id'        => 'your-mch-id',
        'key'                => 'key-for-signature',
        'cert_path'          => 'path/to/your/cert.pem', // XXX: 絕對路徑！！！！
        'key_path'           => 'path/to/your/key',      // XXX: 絕對路徑！！！！
        // 'device_info'     => '013467007045764',
        // 'sub_app_id'      => '',
        // 'sub_merchant_id' => '',
        // ...
    ],

    /**
     * Guzzle 全域性設定
     *
     * 更多請參考： http://docs.guzzlephp.org/en/latest/request-options.html
     */
    'guzzle' => [
        'timeout' => 3.0, // 超時時間（秒）
        //'verify' => false, // 關掉 SSL 認證（強烈不建議！！！）
    ],
];
```

> :heart: 安全模式下請一定要填寫 `aes_key`

## 日誌檔案

配置檔案裡的`/tmp/...`是絕對路徑

如果在 windows 下，去把它改成`C:\foo\bar`的形式，
如果是 Linux ，你已經懂了……

如果需要按日獨立儲存，可以配置成`'file'  => storage_path('/tmp/easywechat/easywechat_'.date('Ymd').'.log'),`

其它同理……

