# 服務端

企業微信服務端推送和公眾號一樣，請參考：[公眾號：服務端](../official-account/server.md)

## 第三方平臺推送事件

企業微信資料推送的有以下事件：

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
- 批次任務執行完成 `batch_job_result`

## 內建訊息處理器

### 處理通訊錄變更事件（包括成員變更、部門變更、成員標籤變更）

```php
$server->handleContactChanged(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 處理任務執行完成事件

```php
$server->handleBatchJobsFinished(function($message, \Closure $next) {
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
