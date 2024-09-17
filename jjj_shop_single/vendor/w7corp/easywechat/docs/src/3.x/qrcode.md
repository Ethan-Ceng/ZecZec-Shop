# 二維碼


目前有2種類型的二維碼：

1. 臨時二維碼，是有過期時間的，最長可以設定為在二維碼生成後的**30天**後過期，但能夠生成較多數量。臨時二維碼主要用於帳號繫結等不要求二維碼永久儲存的業務場景
2. 永久二維碼，是無過期時間的，但數量較少（目前為最多10萬個）。永久二維碼主要用於適用於帳號繫結、使用者來源統計等場景。

## 獲取例項

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$qrcode = $app->qrcode;
```


## API

+ `Bag temporary($sceneId, $expireSeconds = null)` 建立臨時二維碼；
+ `Bag forever($sceneValue)` 建立永久二維碼
+ `Bag card(array $card)` 建立卡券二維碼
+ `string url($ticket)` 獲取二維碼網址，用法： `<img src="<?php $qrcode->url($qrTicket); ?>">`；

### 建立臨時二維碼

```php
$result = $qrcode->temporary(56, 6 * 24 * 3600);

$ticket = $result->ticket;// 或者 $result['ticket']
$expireSeconds = $result->expire_seconds; // 有效秒數
$url = $result->url; // 二維碼圖片解析後的地址，開發者可根據該地址自行生成需要的二維碼圖片
```

### 建立永久二維碼

```php
$result = $qrcode->forever(56);// 或者 $qrcode->forever("foo");

$ticket = $result->ticket; // 或者 $result['ticket']
$url = $result->url;
```

### 獲取二維碼網址

```php
$url = $qrcode->url($ticket);
```

### 建立卡券二維碼

```php
$qrcode->card($card);
```

### 獲取二維碼內容

```php
$url = $qrcode->url($ticket);

$content = file_get_contents($url); // 得到二進位制圖片內容

file_put_contents(__DIR__ . '/code.jpg', $content); // 寫入檔案
```
