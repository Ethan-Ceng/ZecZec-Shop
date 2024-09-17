# 微信開放平臺第三方平臺

請仔細閱讀並理解：[微信官方文件 - 開放平臺 - 第三方平臺](https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/product/Third_party_platform_appid.html)

## 例項化

請按如下格式配置你的開放平臺賬號資訊，並例項化一個開放平臺物件：

```php
<?php
use EasyWeChat\OpenPlatform\Application;

$config = [
  'app_id' => 'wx3cf0f39249eb0exx', // 開放平臺賬號的 appid
  'secret' => 'f1c242f4f28f735d4687abb469072axx',   // 開放平臺賬號的 secret
  'token' => 'easywechat',  // 開放平臺賬號的 token
  'aes_key' => ''   // 明文模式請勿填寫 EncodingAESKey

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

> 💡 請不要把公眾號/小程式的配置資訊用於初始化開放平臺。

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

你可以輕鬆使用 `$config->all()` 獲取整個配置的陣列。

還可以使用 `$config->get($key, $default)` 讀取單個配置，或使用 `$config->set($key, $value)` 在呼叫前修改配置項。

### ComponentAccessToken

access_token 是開放平臺 API 呼叫的必備條件，如果你想獲取它的值，你可以透過以下方式拿到當前的 access_token：

```php
$componentAccessToken = $app->getComponentAccessToken();
$componentAccessToken->getToken(); // string
```

當然你也可以使用自己的 ComponentAccessToken 類：

```php
$componentAccessToken = new MyCustomComponentAccessToken();
$app->setComponentAccessToken($componentAccessToken)
```

### VerifyTicket

你可以透過以下方式拿到當前 verify_ticket 類：

```php
$verifyTicket = $app->getVerfiyTicket();

$verifyTicket->getTicket(); // strval
```

### 開放平臺賬戶

開放平臺賬號類，提供一系列 API 獲取開放平臺的基本資訊：

```php
$account = $app->getAccount();

$account->getAppId();
$account->getSecret();
$account->getToken();
$account->getAesKey();
```

## 第三方應用或網站網頁授權

> 注意：不是代公眾號/小程式授權。

第三方應用或者網站網頁授權的邏輯和公眾號的網頁授權基本一樣：

```php
$oauth = $app->getOAuth();
```

:book: 詳情請參考：[網頁授權](../oauth.md)

## 使用授權碼獲取授權資訊

在使用者在授權頁授權流程完成後，授權頁會自動跳轉進入回撥 URI，並在 URL 引數中返回授權碼和過期時間，如：(`https://easywechat.com/callback?auth_code=xxx&expires_in=600`)

```php
$authorizationCode = '授權成功時返回給第三方平臺的授權碼';

$authorization = $app->getAuthorization($authorizationCode);

$authorization->getAppId(); // authorizer_appid
$authorization->getAccessToken(); // EasyWeChat\OpenPlatform\AuthorizerAccessToken
$authorization->getRefreshToken(); // authorizer_access_token
$authorization->toArray();
$authorization->toJson();

// {
//   "authorization_info": {
//     "authorizer_appid": "wxf8b4f85f3a79...",
//     "authorizer_access_token": "QXjUqNqfYVH0yBE1iI_7vuN_9gQbpjfK7M...",
//     "expires_in": 7200,
//     "authorizer_refresh_token": "dTo-YCXPL4llX-u1W1pPpnp8Hgm4wpJt...",
//     "func_info": [
//       {
//         "funcscope_category": {
//           "id": 1
//         }
//       },
//       //...
//     ]
//   }
// }

```

## 建立預授權碼 <version-tag>6.3.0+</version-tag>

你可以透過下面的方式建立預授權碼：

```php
$reponse = $app->createPreAuthorizationCode();
// {
//   "pre_auth_code": "Cx_Dk6qiBE0Dmx4eKM-2SuzA...",
//   "expires_in": 600
// }
```

## 生成授權頁地址 <version-tag>6.3.0+</version-tag>

你可以透過下面方法生成一個授權頁地址，引導使用者進行授權：

```php
// 自動獲取預授權碼模式
$url = $app->createPreAuthorizationUrl('http://easywechat.com/callback');

// 或者指定預授權碼
$preAuthCode = 'createPreAuthorizationCode 得到的預授權碼 pre_auth_code';
$url = $app->createPreAuthorizationUrl('http://easywechat.com/callback', $preAuthCode);
```

## 獲取/重新整理介面呼叫令牌

在公眾號/小程式介面呼叫令牌 `authorizer_access_token` 失效時，可以使用重新整理令牌 `authorizer_refresh_token` 獲取新的介面呼叫令牌。

> `authorizer_access_token` 有效期為 2 小時，開發者需要快取 `authorizer_access_token`，避免獲取/重新整理介面呼叫令牌的 API 呼叫觸發每日限額。

```php
$authorizerAppId = '授權方 appid';
$authorizerRefreshToken = '上一步得到的 authorizer_refresh_token';

$app->refreshAuthorizerToken($authorizerAppId, $authorizerRefreshToken)

// {
//   "authorizer_access_token": "some-access-token",
//   "expires_in": 7200,
//   "authorizer_refresh_token": "refresh_token_value"
// }
```

---

## 代替公眾號/小程式請求 API

代替公眾號/小程式請求，需要首先拿到 `EasyWeChat\OpenPlatform\AuthorizerAccessToken`，用以代替公眾號的 Access Token，官方流程說明：[開發前必讀 /Token 生成介紹](https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Before_Develop/creat_token.html) 。

### 獲取 AuthorizerAccessToken

你可以使用開放 **平臺永久授權碼** 換取授權者資訊，然後換取 Authorizer Access Token：

```php
$authorizationCode = '授權成功時返回給第三方平臺的授權碼';
$authorization = $app->getAuthorization($authorizationCode);
$authorizerAccessToken = $authorization->getAccessToken();
```

> 🚨 Authorizer Access Token 只有 2 小時有效期，不建議將它儲存到資料庫，當然如果你不得不這麼做，請記得參考上面 「**獲取/重新整理介面呼叫令牌**」章節重新整理。

### 代公眾號呼叫

**方式一：使用 authorizer_refresh_token** <version-tag>6.3.0+</version-tag>

此方式適用於大部分場景，將授權資訊儲存到資料庫中，代替呼叫時取出對應公眾號的 authorizer_refresh_token 即可。

```php
$authorizerRefreshToken = '公眾號授權時得到的 authorizer_refresh_token';
$officialAccount = $app->getOfficialAccountWithRefreshToken($appId, $authorizerRefreshToken);
```

**方式二：使用 authorizer_access_token** <version-tag>6.3.0+</version-tag>

此方案適用於使用獨立的中央授權服務單獨維護授權資訊的方式。

```php
$authorizerAccessToken = '公眾號授權時得到的 authorizer_access_token';
$officialAccount = $app->getOfficialAccountWithAccessToken($appId, $authorizerAccessToken);
```

**方式三：使用 AuthorizerAccessToken 類**

不推薦，請使用方式一或者二，此方法由於設計之初沒有充分考慮到使用場景，導致使用很麻煩。

```php
// $token 為你存到資料庫的授權碼 authorizer_access_token
$authorizerAccessToken = new AuthorizerAccessToken($authorizerAppId, $token);
$officialAccount = $app->getOfficialAccount($authorizerAccessToken);


使用以上方式初始化公眾號物件後，可以直接呼叫公眾號的 API 方法，如：

// 呼叫公眾號介面
$response = $officialAccount->getClient()->get('cgi-bin/users/list');
```

> `$officialAccount` 為 `EasyWeChat\OfficialAccount\Application` 例項

:book: 更多公眾號用法請參考：[公眾號](../official-account/index.md)

### 代小程式呼叫

小程式和公眾號使用方式一樣，同樣有三種方式：

```php
// 方式一：使用 authorizer_refresh_token
$authorizerRefreshToken = '小程式授權時得到的 authorizer_refresh_token';
$officialAccount = $app->getMiniAppWithRefreshToken($appId, $authorizerRefreshToken);

// 方式二：使用 authorizer_access_token
$authorizerAccessToken = '小程式授權時得到的 authorizer_access_token';
$officialAccount = $app->getMiniAppWithAccessToken($appId, $authorizerAccessToken);

// 方式三：不推薦
// $token 為你存到資料庫的授權碼 authorizer_access_token
$authorizerAccessToken = new AuthorizerAccessToken($authorizerAppId, $token);
$miniApp = $app->getMiniApp($authorizerAccessToken);

// 呼叫小程式介面
$response = $miniApp->getClient()->get('cgi-bin/users/list');
```

- [微信官方文件 - 開放平臺代小程式實現小程式登入介面](https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/others/WeChat_login.html#請求地址)

:book: 更多小程式用法請參考：[小程式](../mini-app/index.md)
