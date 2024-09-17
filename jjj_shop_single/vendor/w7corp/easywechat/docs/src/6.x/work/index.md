# 企業微信

請仔細閱讀並理解：[企業微信 API - 企業內部開發](https://open.work.weixin.qq.com/api/doc/90000/90135/90664)

## 例項化

```php
<?php
use EasyWeChat\Work\Application;

$config = [
  'corp_id' => 'wx3cf0f39249eb0exx',
  'secret' => 'f1c242f4f28f735d4687abb469072axx',
  'token' => 'easywechat',
  'aes_key' => '35d4687abb469072a29f1c242xxxxxx',

  /**
   * 介面請求相關配置，超時時間等，具體可用引數請參考：
   * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
   */
  'http' => [
      'throw'  => true, // 狀態碼非 200、300 時是否丟擲異常，預設為開啟
      'timeout' => 5.0,
      // 'base_uri' => 'https://qyapi.weixin.qq.com/', // 如果你在國外想要覆蓋預設的 url 的時候才使用，根據不同的模組配置不同的 uri

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

## API

Application 就是一個工廠類，所有的模組都是從 `$app` 中訪問，並且幾乎都提供了協議和 setter 可自定義修改。

### 服務端

服務端模組封裝了服務端相關的便捷操作，隱藏了部分複雜的細節，基於中介軟體模式可以更方便的處理訊息推送和服務端驗證。

```php
$app->getServer();
```

:book: 更多說明請參閱：[服務端使用文件](server.md)

### API Client

封裝了多種模式的 API 呼叫類，你可以選擇自己喜歡的方式呼叫開放平臺任意 API，預設自動處理了 access_token 相關的邏輯。

```php
$app->getClient();
```

:book: 更多說明請參閱：[API 呼叫](../client.md)

### 配置

```php
$config = $app->getConfig();
```

你可以輕鬆使用 `$config->get($key, $default)` 讀取配置，或使用 `$config->set($key, $value)` 在呼叫前修改配置項。

### getAccessToken

access_token 是 API 呼叫的必備條件，如果你想獲取它的值，你可以透過以下方式拿到當前的 access_token：

```php
$accessToken = $app->getAccessToken();
$accessToken->getToken(); // string
```

當然你也可以使用自己的 getAccessToken 類：

```php
$accessToken = new MyCustomAccessToken();
$app->getAccessToken($accessToken)
```

### 企業賬戶

企業賬號類，提供一系列 API 獲取企業的基本資訊：

```php
$account = $app->getAccount();

$account->getCorpId();
$account->getSecret();
$account->getToken();
$account->getAesKey();
```

## 企業網頁授權

> [點此檢視官方文件](https://open.work.weixin.qq.com/api/doc/90000/90135/91020)

```php
$oauth = $app->getOAuth();
```

:book: 詳情請參考：[網頁授權](./oauth.md)
