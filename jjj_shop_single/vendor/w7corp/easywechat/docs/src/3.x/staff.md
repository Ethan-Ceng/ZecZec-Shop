# 客服


> 2016.06.28 已經更新為新版多客服 API
> 請更新到 3.1 版本： composer require "overtrue/wechat:~3.1"

微信的客服才能傳送訊息或者群發訊息，而且還有時效限制，真噁心的說。。。

## 客服管理

```php
<?php
use EasyWeChat\Foundation\Application;
// ...
$app = new Application($options);

$staff = $app->staff; // 客服管理
```

## API

### 獲取所有客服賬號列表

```php
$staff->lists();
```

### 獲取所有線上的客服賬號列表

```php
$staff->onlines();
```

### 新增客服帳號

```php
$staff->create('foo@test', '客服1');
```

### 修改客服帳號

```php
$staff->update('foo@test', '客服1');
```

### 刪除客服帳號

```php
$staff->delete('foo@test');
```

### 設定客服帳號的頭像

```php
$staff->avatar('foo@test', $avatarPath); // $avatarPath 為本地圖片路徑，非 URL
```

### 獲取客服聊天記錄 `NEW`

```php
$staff->records($startTime, $endTime, $pageIndex, $pageSize);

// example: $records = $staff->records('2015-06-07', '2015-06-21', 1, 20);
```

### 主動傳送訊息給使用者

```php
$staff->message($message)->to($openId)->send();
```

> `$message` 為訊息物件，請參考：[訊息](messages.html)

### 指定客服傳送訊息

```php
$staff->message($message)->by('account@test')->to($openId)->send();
```
> `$message` 為訊息物件，請參考：[訊息](messages.html)

## 客服會話控制

> 客服會話為新版 API 功能

```php
<?php
use EasyWeChat\Foundation\Application;
// ...
$app = new Application($options);

$session = $app->staff_session; // 客服會話管理
```

## 建立會話

```php
$session->create('test1@test', 'OPENID');
```

### 關閉會話

```php
$session->close('test1@test', 'OPENID');
```

### 獲取客戶會話狀態

```php
$session->get('OPENID');
```

### 獲取客服會話列表

```php
$session->lists('test1@test');
```

### 獲取未接入會話列表

```php
$session->waiters();
```


關於更多客服介面資訊請參考微信官方文件：http://mp.weixin.qq.com/wiki
