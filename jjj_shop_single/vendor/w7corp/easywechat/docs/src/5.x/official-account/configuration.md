# 配置

常用的配置引數會比較少，因為除非你有特別的定製，否則基本上預設值就可以了：

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

下面是一個完整的配置樣例：

> 不建議你在配置的時候弄這麼多，用到啥就配置啥才是最好的，因為大部分用預設值即可。

```php
<?php

return [
    /**
     * 賬號基本資訊，請從微信公眾平臺/開放平臺獲取
     */
    'app_id'  => 'your-app-id',         // AppID
    'secret'  => 'your-app-secret',     // AppSecret
    'token'   => 'your-token',          // Token
    'aes_key' => '',                    // EncodingAESKey，相容與安全模式下請一定要填寫！！！

     /**
      * 指定 API 呼叫返回結果的型別：array(default)/collection/object/raw/自定義類名
      * 使用自定義類名時，建構函式將會接收一個 `EasyWeChat\Kernel\Http\Response` 例項
      */
    'response_type' => 'array',

    /**
     * 日誌配置
     *
     * level: 日誌級別, 可選為：
     *         debug/info/notice/warning/error/critical/alert/emergency
     * path：日誌檔案位置(絕對路徑!!!)，要求可寫許可權
     */
    'log' => [
        'default' => 'dev', // 預設使用的 channel，生產環境可以改為下面的 prod
        'channels' => [
            // 測試環境
            'dev' => [
                'driver' => 'single',
                'path' => '/tmp/easywechat.log',
                'level' => 'debug',
            ],
            // 生產環境
            'prod' => [
                'driver' => 'daily',
                'path' => '/tmp/easywechat.log',
                'level' => 'info',
            ],
        ],
    ],

    /**
     * 介面請求相關配置，超時時間等，具體可用引數請參考：
     * http://docs.guzzlephp.org/en/stable/request-config.html
     *
     * - retries: 重試次數，預設 1，指定當 http 請求失敗時重試的次數。
     * - retry_delay: 重試延遲間隔（單位：ms），預設 500
     * - log_template: 指定 HTTP 日誌模板，請參考：https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php
     */
    'http' => [
        'max_retries' => 1,
        'retry_delay' => 500,
        'timeout' => 5.0,
        // 'base_uri' => 'https://api.weixin.qq.com/', // 如果你在國外想要覆蓋預設的 url 的時候才使用，根據不同的模組配置不同的 uri
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
];
```

> :heart: 安全模式下請一定要填寫 `aes_key`

## 日誌配置

你可以配置多個日誌的 channel，每個 channel 裡的 `driver` 對應不同的日誌驅動，內建可用的 `driver` 如下表：

名稱 | 描述
------------- | -------------
`stack` | 複合型，可以包含下面多種驅動的混合模式
`single` | 基於 `StreamHandler` 的單一檔案日誌，引數有 `path`，`level`
`daily` | 基於 `RotatingFileHandler` 按日期生成日誌檔案，引數有 `path`，`level`，`days`(預設 7 天)
`slack` | 基於 `SlackWebhookHandler` 的 Slack 元件，引數請參考原始碼：[LogManager.php](https://github.com/overtrue/wechat/blob/master/src/Kernel/Log/LogManager.php#L247)
`syslog` | 基於 `SyslogHandler` Monolog 驅動，引數有 `facility` 預設為 `LOG_USER`，`level`
`errorlog` | 記錄日誌到系統錯誤日誌，基於 `ErrorLogHandler`，引數有 `type`，預設為 `ErrorLogHandler::OPERATING_SYSTEM`

### 自定義日誌驅動

由於日誌使用的是 [Monolog](https://github.com/Seldaek/monolog)，所以，除了預設的檔案式日誌外，你可以自定義日誌處理器：

```php
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;


// 註冊自定義日誌
$app->logger->extend('mylog', function($app, $config){
    return new Logger($this->parseChannel($config), [
        $this->prepareHandler(new RotatingFileHandler(
            $config['path'], $config['days'], $this->level($config)
        )),
    ]);
});
```

>  在你自定義的閉包函式中，可以使用 `EasyWeChat\Kernel\Log\LogManager` 中的方法，具體請檢視 SDK 原始碼。

配置檔案中在 `driver` 部分即可使用你自定義的驅動了：

```php
'log' => [
    'default' => 'dev', // 預設使用的 channel，生產環境可以改為下面的 prod
    'channels' => [
        // 測試環境
        'dev' => [
            'driver' => 'mylog',
            'path' => '/tmp/easywechat.log',
            'level' => 'debug',
            'days' => 5,
        ],

        //...
    ],
],
```

