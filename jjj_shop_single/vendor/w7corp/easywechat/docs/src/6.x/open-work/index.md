# 企業微信服務商

請仔細閱讀並理解：[企業微信 API - 第三方應用開發](https://open.work.weixin.qq.com/api/doc/90001/90142/90594)

## 例項化

```php
<?php
use EasyWeChat\OpenWork\Application;

$config = [
  'corp_id' => 'wx3cf0f39249eb0exx',
  'provider_secret' => 'f1c242f4f28f735d4687abb469072axx',
  'token' => 'easywechat',
  'aes_key' => '', // 明文模式請勿填寫 EncodingAESKey

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

### ProviderAccessToken

provider_access_token 是開放平臺 API 呼叫的必備條件，如果你想獲取它的值，你可以透過以下方式拿到當前的 provider_access_token：

```php
$providerAccessToken = $app->getProviderAccessToken();
$providerAccessToken->getToken(); // string
```

當然你也可以使用自己的 ProviderAccessToken 類：

```php
$providerAccessToken = new MyCustomProviderAccessToken();
$app->setProviderAccessToken($providerAccessToken)
```

### SuiteTicket

你可以透過以下方式拿到當前 suite_ticket 類：

```php
$suiteTicket = $app->getSuiteTicket();

$suiteTicket->getTicket(); // string
```

### 開放平臺賬戶

開放平臺賬號類，提供一系列 API 獲取開放平臺的基本資訊：

```php
$account = $app->getAccount();

$account->getCorpId();
$account->getProviderSecret();
$account->getToken();
$account->getAesKey();
```

## 第三方應用需要在開啟的網頁裡面攜帶使用者的身份資訊

> [點此檢視官方文件](https://open.work.weixin.qq.com/api/doc/90001/90143/91120#%E6%9E%84%E9%80%A0%E7%AC%AC%E4%B8%89%E6%96%B9%E5%BA%94%E7%94%A8oauth2%E9%93%BE%E6%8E%A5)

第三方應用或者網站網頁授權的邏輯和公眾號的網頁授權基本一樣：

```php
$oauth = $app->getOAuth(string $suiteId, AccessTokenInterface $suiteAccessToken);
```

:book: 詳情請參考：[網頁授權](./oauth.md)

## 企業網頁授權

> [點此檢視官方文件](https://open.work.weixin.qq.com/api/doc/90001/90143/91120#%E6%9E%84%E9%80%A0%E4%BC%81%E4%B8%9Aoauth2%E9%93%BE%E6%8E%A5)

```php
$oauth = $app->getCorpOAuth(string $corpId, AccessTokenInterface $suiteAccessToken);
```

:book: 詳情請參考：[網頁授權](./oauth.md)

## 使用授權碼獲取授權資訊

在使用者在授權頁授權流程完成後，授權頁會自動跳轉進入回撥 URI，並在 URL 引數中返回授權碼和過期時間，如：(`https://easywechat.com/callback?auth_code=xxx&expires_in=600`)

```php
$permanentCode = '企業永久授權碼';
$suiteAccessToken = new SuiteAccessToken($suiteId, $suiteSecret);

$authorization = $app->getAuthorization($corpId, $authorizatpermanentCodeionCode, $suiteAccessToken);

$authorization->getCorpId(); // auth_corp_info.corpid
$authorization->toArray();
$authorization->toJson();

// {
//     "errcode":0 ,
//     "errmsg":"ok" ,
//     "dealer_corp_info":
//     {
//         "corpid": "xxxx",
//         "corp_name": "name"
//     },
//     "auth_corp_info":
//     {
//         "corpid": "xxxx",
//         "corp_name": "name",
//         "corp_type": "verified",
//         "corp_square_logo_url": "yyyyy",
//         "corp_user_max": 50,
//         "corp_agent_max": 30,
//         "corp_full_name":"full_name",
//         "verified_end_time":1431775834,
//         "subject_type": 1,
//         "corp_wxqrcode": "zzzzz",
//         "corp_scale": "1-50人",
//         "corp_industry": "IT服務",
//         "corp_sub_industry": "計算機軟體/硬體/資訊服務",
//         "location":"廣東省廣州市"
//     },
//     "auth_info":
//     {
//         "agent" :
//         [
//             {
//                 "agentid":1,
//                 "name":"NAME",
//                 "round_logo_url":"xxxxxx",
//                 "square_logo_url":"yyyyyy",
//                 "appid":1,
//                 "auth_mode":1,
//                 "privilege":
//                 {
//                     "level":1,
//                     "allow_party":[1,2,3],
//                     "allow_user":["zhansan","lisi"],
//                     "allow_tag":[1,2,3],
//                     "extra_party":[4,5,6],
//                     "extra_user":["wangwu"],
//                     "extra_tag":[4,5,6]
//                 },
//                 "shared_from":
//                 {
//                     "corpid":"wwyyyyy"
//                 }
//             },
//             {
//                 "agentid":2,
//                 "name":"NAME2",
//                 "round_logo_url":"xxxxxx",
//                 "square_logo_url":"yyyyyy",
//                 "appid":5,
//                 "shared_from":
//                 {
//                     "corpid":"wwyyyyy"
//                 }
//             }
//         ]
//     }
// }

```

## 獲取企業憑證

在公眾號/小程式介面呼叫令牌（`authorizer_access_token`）失效時，可以使用重新整理令牌（authorizer_refresh_token）獲取新的介面呼叫令牌。

> 注意： `authorizer_access_token` 有效期為 2 小時，開發者需要快取 `authorizer_access_token`，避免獲取/重新整理介面呼叫令牌的 API 呼叫觸發每日限額。快取方法可以參考：<https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Get_access_token.html>

```php
$permanentCode = '企業永久授權碼';
$suiteAccessToken = new SuiteAccessToken($suiteId, $suiteSecret);

$authorizerAccessToken = $app->getAuthorizerAccessToken($corpId, $permanentCode, $suiteAccessToken)

// {
//     "errcode":0 ,
//     "errmsg":"ok" ,
//     "access_token": "xxxxxx",
//     "expires_in": 7200
// }


$authorizerAccessToken->getToken(); // string
```
