# 客服

使用客服系統可以向用戶傳送訊息以及群發訊息，客服的管理等功能。

## 客服管理

### 獲取所有客服

```php
$app->customer_service->list();
```

### 獲取所有線上的客服

```php
$app->customer_service->online();
```

### 新增客服

```php
$app->customer_service->create('foo@test', '客服1');
```

### 修改客服

```php
$app->customer_service->update('foo@test', '客服1');
```

### 刪除賬號

```php
$app->customer_service->delete('foo@test');
```

### 設定客服頭像

```php
$app->customer_service->setAvatar('foo@test', $avatarPath); // $avatarPath 為本地圖片路徑，非 URL
```

### 獲取客服與客戶聊天記錄

```php
$app->customer_service->messages($startTime, $endTime, $msgId = 1, $number = 10000);
```

示例:

```php
$records = $app->customer_service->messages('2015-06-07', '2015-06-21', 1, 20000);
```

### 主動傳送訊息給使用者

```php
$app->customer_service->message($message)->to($openId)->send();
```

> `$message` 為訊息物件或文字，請參考：[訊息](messages)

示例：

```php
$app->customer_service->message('hello')
                  >  ->to('oV-gpwdOIwSI958m9osAhGBFxxxx')
                  >  ->send();
```

### 指定客服傳送訊息

```php
$app->customer_service->message($message)
                      >  ->from('account@test')
                      >  ->to($openId)
                      >  ->send();
```
> `$message` 為訊息物件或文字，請參考：[訊息](messages.html)

示例：

```php
$app->customer_service->message('hello')
                  >  ->from('kf2001@gh_176331xxxx')
                  >  ->to('oV-gpwdOIwSI958m9osAhGBFxxxx')
                  >  ->send();
```

### 邀請微信使用者加入客服

以賬號 `foo@test` 邀請 微訊號 為 `xxxx` 的微信使用者加入客服。

```php
$app->customer_service->invite('foo@test', 'xxxx');
```

## 客服會話控制

## 建立會話

```php
$app->customer_service_session->create('test1@test', 'OPENID');
```

### 關閉會話

```php
$app->customer_service_session->close('test1@test', 'OPENID');
```

### 獲取客戶會話狀態

```php
$app->customer_service_session->get('OPENID');
```

### 獲取客服會話列表

```php
$app->customer_service_session->list('test1@test');
```

### 獲取未接入會話列表

```php
$app->customer_service_session->waiting();
```
