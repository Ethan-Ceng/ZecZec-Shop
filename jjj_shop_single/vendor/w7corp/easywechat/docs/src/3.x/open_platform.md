# 微信開放平臺


### 例項化

```php
<?php
use EasyWeChat\Foundation\Application;

$options = [
    // ...
    'open_platform' => [
        'app_id'   => 'component-app-id',
        'secret'   => 'component-app-secret',
        'token'    => 'component-token',
        'aes_key'  => 'component-aes-key'
        ],
    // ...
    ];

$app = new Application($options);
$openPlatform = $app->open_platform;
```

### 監聽微信伺服器推送事件

公眾號第三方平臺推送的有四個事件：授權成功(`authorized`)，授權更新(`updateauthorized`)，授權取消（`unauthorized`），以及 `component_verify_ticket`。

本 SDK 預設處理方式為：

- `authorized` / `updateauthorized`: 獲取授權方(Authorizer)的所有資訊，並快取 `authorizer_access_token` 和 `authorizer_refresh_token`，授權方的資訊則需要開發者手動處理。
- `unauthorized`: 刪除 `authorizer_access_token` 和 `authorizer_refresh_token` 的快取。
- `component_verify_ticket`: 快取 `component_veirfy_ticket`。

當然也允許自定義處理這些事件，不過以上預設處理仍然會先執行，為的是幫助開發者免去快取的困擾。

```php
// 預設處理方式
$openPlatform->server->serve();

// 自定義處理
$openPlatform->server->setMessageHandler(function($event) {
    // 事件型別常量定義在 \EasyWeChat\OpenPlatform\Guard 類裡
    switch ($event->InfoType) {
        case 'authorized':
            // ...
        case 'unauthorized':
            // ...
        case 'updateauthorized':
            // ...
        case 'component_verify_ticket':
            // ...
    }
});
$openPlatform->server->serve();

// 或者
$openPlatform->server->listen(function ($event) {
    switch ($event->InfoType) {
        // ...
    }
});
```

#### 授權成功，授權更新

這兩個事件下，SDK 預設抓取了所有授權方所有的資訊，並快取 `authorizer_access_token` 和 `authorizer_refresh_token`，授權方的資訊為原微信 API 的返回結果，由開發者自行處理，比如儲存到資料庫。

```php
// 自定義處理
// 其中 $event 變數裡有微信推送事件本身的資訊，也有授權方所有的資訊。
$openPlatform->server->setMessageHandler(function($event) {
    // 事件型別常量定義在 \EasyWeChat\OpenPlatform\Guard 類裡
    switch ($event->InfoType) {
        case 'authorized':
            // 授權資訊，主要是 token 和授權域
            $info1 = $event->authorization_info;
            // 授權方資訊，就是授權方公眾號的資訊了
            $info2 = $event->authorizer_info;
    }
});
```

目前 SDK 對這兩個事件的處理方式沒有區別。

#### 授權取消

SDK 預設處理：刪除 `authorizer_access_token` 和 `authorizer_refresh_token` 的快取。開發者可以自行處理資料庫刪除授權方資訊等操作。

#### 推送 component_verify_ticket

在公眾號第三方平臺建立稽核通過後，微信伺服器會向其“授權事件接收URL”每隔10分鐘定時推送 `component_verify_ticket`。SDK 內部已實現快取 `component_veirfy_ticket`，無需開發者另行快取。

注：需要在URL路由中寫上觸發程式碼，並且註冊路由後需要等待微信伺服器推送 `component_verify_ticket`，才能進行後續操作，否則報"Component verify ticket does not exists."

### 呼叫 API

#### 設定授權方的 App Id

開發者必須設定授權方來呼叫 API。

```php
$openPlatform = new Application($options)->open_platform;

// 載入授權方資訊，比如 $authorizer = Authorizer::find($id);
$authorizerAppId = $authorizer->app_id;
$authorizerRefreshToken = $authorizer->refresh_token;

$app = $openPlatform->createAuthorizerApplication($authorizerAppId, $authorizerRefreshToken);
// 然後呼叫方法和普通呼叫一致。
// ...
```

### 授權 API

#### 獲取預授權網址

```php
// 直接跳轉
$response = $openPlatform->pre_auth->redirect('https://domain.com/callback');

// 獲取跳轉的連結
$response->getTargetUrl();
```

使用者授權後會帶上 `code` 跳轉到 `redirect` 指定的連結。

#### 使用授權碼換取公眾號的介面呼叫憑據和授權資訊

```php
// 使用授權碼換取公眾號的介面呼叫憑據和授權資訊
// Optional: $authorizationCode 不傳值時會自動獲取 URL 中 auth_code 值
$openPlatform->getAuthorizationInfo($authorizationCode = null);
```

#### 獲取授權方的公眾號帳號基本資訊

```php
$openPlatform->getAuthorizerInfo($authorizerAppId);
```

#### 獲取授權方的選項設定資訊

```php
$openPlatform->getAuthorizerOption($authorizerAppId, $optionName);
```

#### 設定授權方的選項資訊

```php
$openPlatform->setAuthorizerOption($authorizerAppId, $optionName, $optionValue);
```
