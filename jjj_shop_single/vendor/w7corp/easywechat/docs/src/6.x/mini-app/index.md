# 小程式

> 🚨 使用前建議熟讀 [微信官方文件: 小程式](https://developers.weixin.qq.com/doc/offiaccount/Getting_Started/Overview.html)

常用的配置引數會比較少，因為除非你有特別的定製，否則基本上預設值就可以了：

```php
use EasyWeChat\MiniApp\Application;

$config = [
    'app_id' => 'wx3cf0f39249eb0exx',
    'secret' => 'f1c242f4f28f735d4687abb469072axx',
    'token' => 'easywechat',
    'aes_key' => '......'

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
];

$app = new Application($config);
```

:book: 更多配置項請參考：[配置](config.md)

## API

Application 就是一個工廠類，所有的模組都是從 `$app` 中訪問，並且幾乎都提供了協議和 setter 可自定義修改。

### 服務端

服務端模組封裝了服務端相關的便捷操作，隱藏了部分複雜的細節，基於中介軟體模式可以更方便的處理訊息推送和服務端驗證。

```php
$app->getServer();
```

:book: 更多說明請參閱：[服務端使用文件](server.md)

### API Client

封裝了多種模式的 API 呼叫類，你可以選擇自己喜歡的方式呼叫小程式任意 API，預設自動處理了 access_token 相關的邏輯。

```php
$app->getClient();
```

:book: 更多說明請參閱：[API 呼叫](../client.md)

### 配置

```php
$config = $app->getConfig();
```

你可以輕鬆使用 `$config->get($key, $default)` 讀取配置，或使用 `$config->set($key, $value)` 在呼叫前修改配置項。

### AccessToken

access_token 是小程式 API 呼叫的必備條件，如果你想獲取它的值，你可以透過以下方式拿到當前的 access_token：

```php
$accessToken = $app->getAccessToken();
$accessToken->getToken(); // string
```

當然你也可以使用自己的 AccessToken 類：

```php
$accessToken = new MyCustomAccessToken();
$app->setAccessToken($accessToken)
```

### 小程式賬戶

小程式賬號類，提供一系列 API 獲取小程式的基本資訊：

```php
$account = $app->getAccount();

$account->getAppId();
$account->getSecret();
$account->getToken();
$account->getAesKey();
```

### 工具類

對於一些高頻介面，我們提供了一個簡單的工具類來簡化大家的開發工作，可以透過下面的方法獲取：

```php
$utils = $app->getUtils();
```

:book: 更多說明請參閱：[工具類](utils.md)
