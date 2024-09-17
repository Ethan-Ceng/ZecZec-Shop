# 服務端

第三方平臺的服務端推送和公眾號一樣，請參考：[公眾號：服務端](../official-account/server.md)

## 第三方平臺推送事件處理

公眾號第三方平臺推送的有四個事件：

> 如已經授權的公眾號、小程式再次進行授權，而未修改已授權的許可權的話，是沒有相關事件推送的。

- 授權成功 `authorized`
- 授權更新 `updateauthorized`
- 授權取消 `unauthorized`
- VerifyTicket `component_verify_ticket`

SDK 預設會處理事件 `component_verify_ticket` ，並會快取 `verify_ticket` 所以如果你暫時不需要處理其他事件，直接這樣使用即可：

```php
$server = $app->getServer();

return $server->serve();
```

## 內建訊息處理器

> _訊息處理器詳細說明見公眾號開發 - 服務端一節_

### 處理授權成功事件

```php
$server->handleAuthorized(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```
### 處理授權更新事件

```php
$server->handleAuthorizeUpdated(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```
### 處理授權取消事件

```php
$server->handleUnauthorized(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 處理 VerifyTicket 推送事件（已預設處理）

此推送已經預設處理（使用快取儲存和重新整理），可以直接忽略。

> 注意：如果你自行處理了 VerifyTicket 推送，你必須同時設定 ComponentAccessToken 類，因為 ComponentAccessToken 依賴它。

```php
$server->handleVerifyTicketRefreshed(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

## 其它事件處理

以上便捷方法都只處理了特定事件，其它狀態，可以透過自定義事件處理中介軟體的形式處理：

```php
$server->with(function($message, \Closure $next) {
    // $message->event_type 事件型別
    return $next($message);
});
```

## 自助處理推送訊息

你可以透過下面的方式獲取來自微信伺服器的推送訊息：


```php
$message = $server->getRequestMessage(); // 原始訊息
```

你也可以獲取解密後的訊息 <version-tag>6.5.0+</version-tag>

```php
$message = $server->getDecryptedMessage();
```

`$message` 為一個 `EasyWeChat\OpenPlatform\Message` 例項。

你可以在處理完邏輯後自行建立一個響應，當然，在不同的框架裡，響應寫法也不一樣，請自行實現。
