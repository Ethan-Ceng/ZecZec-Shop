# 服務端

企業微信第三方服務端推送和公眾號一樣，請參考：[公眾號：服務端](../official-account/server.md)

## 第三方平臺推送事件處理

企業微信第三方資料推送的有以下事件：

- suite_ticket 推送 `suite_ticket`
- 授權成功 `create_auth`
- 授權變更 `change_auth`
- 授權取消 `cancel_auth`
- 通訊錄變更（Event） `change_contact`
  - ChangeType
    - 成員變更
      - 新增成員 `create_user`
      - 更新成員 `update_user`
      - 刪除成員 `delete_user`
    - 部門變更
      - 新增部門 `create_party`
      - 更新部門 `update_party`
      - 刪除部門 `delete_party`
    - 標籤變更
      - 成員標籤變更 `update_tag`
- 共享應用事件回撥 `share_agent_change`

## 內建訊息處理器

> _訊息處理器詳細說明見：公眾號開發 - 服務端一節_

### 授權成功事件

```php
$server->handleAuthCreated(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 授權變更事件

```php
$server->handleAuthChanged(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 授權取消事件

```php
$server->handleAuthCancelled(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 通訊錄變更事件

```php
$server->handleContactChanged(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 成員變更事件

```php
// 新增成員
$server->handleUserCreated(function($message, \Closure $next) {
    // ...
    return $next($message);
});

// 更新成員
$server->handleUserUpdated(function($message, \Closure $next) {
    // ...
    return $next($message);
});

// 刪除成員
$server->handleUserDeleted(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 部門變更事件

```php
// 新增部門
$server->handlePartyCreated(function($message, \Closure $next) {
    // ...
    return $next($message);
});

// 更新部門
$server->handlePartyUpdated(function($message, \Closure $next) {
    // ...
    return $next($message);
});

// 刪除部門
$server->handlePartyDeleted(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 成員標籤變更事件

```php
$server->handleUserTagUpdated(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 共享應用事件

```php
$server->handleShareAgentChanged(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### suite_ticket 推送事件

此推送已經預設處理（使用快取儲存和重新整理），可以直接忽略。

> 注意：如果你自行處理了 SuiteTicket 推送，你必須同時設定 ProviderAccessToken 類，因為 ProviderAccessToken 依賴它。

```php
$server->handleSuiteTicketRefreshed(callable | string $handler);
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

`$message` 為一個 `EasyWeChat\OpenWork\Message` 例項。

你可以在處理完邏輯後自行建立一個響應，當然，在不同的框架裡，響應寫法也不一樣，請自行實現。
