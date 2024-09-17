# 配置

下面是一個完整的配置樣例：

> 不建議你在配置的時候弄這麼多，用到啥就配置啥才是最好的，因為大部分用預設值即可。

```php
[
    /**
     * 賬號基本資訊，請從微信公眾平臺/開放平臺獲取
     */
    'app_id'  => 'your-app-id',         // AppID
    'secret'  => 'your-app-secret',     // AppSecret
    'token'   => 'your-token',          // Token
    'aes_key' => '',                    // EncodingAESKey，相容與安全模式下請一定要填寫！！！

    /**
     * 介面請求相關配置，超時時間等，具體可用引數請參考：
     * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
     */
    'http' => [
        'throw'  => true, // 狀態碼非 200、300 時是否丟擲異常，預設為開啟
        'timeout' => 5.0,
        // 'base_uri' => 'https://api.weixin.qq.com/', // 如果你在國外想要覆蓋預設的 url 的時候才使用，根據不同的模組配置不同的 uri

        'retry' => true, // 使用預設重試配置
        //  'retry' => [
        //      // 僅以下狀態碼重試
        //      'http_codes' => [429, 500]
        //       // 最大重試次數
        //      'max_retries' => 3,
        //      // 請求間隔 (毫秒)
        //      'delay' => 1000,
        //      // 如果設定，每次重試的等待時間都會增加這個係數
        //      // (例如. 首次:1000ms; 第二次: 3 * 1000ms; etc.)
        //      'multiplier' => 3
        //  ],
    ],
]
```

> :heart: 安全模式下請一定要填寫 `aes_key`
