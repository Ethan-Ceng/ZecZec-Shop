# 二維碼

目前有 2 種類型的二維碼：

1. 臨時二維碼，是有過期時間的，最長可以設定為在二維碼生成後的 **30天**後過期，但能夠生成較多數量。臨時二維碼主要用於帳號繫結等不要求二維碼永久儲存的業務場景
2. 永久二維碼，是無過期時間的，但數量較少（目前為最多10萬個）。永久二維碼主要用於適用於帳號繫結、使用者來源統計等場景。

## 建立臨時二維碼

```php
$result = $app->qrcode->temporary('foo', 6 * 24 * 3600);

// Array
// (
//     [ticket] => gQFD8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTmFjVTRWU3ViUE8xR1N4ajFwMWsAAgS2uItZAwQA6QcA
//     [expire_seconds] => 518400
//     [url] => http://weixin.qq.com/q/02NacU4VSubPO1GSxj1p1k
// )
```

## 建立永久二維碼

```php
$result = $app->qrcode->forever(56);// 或者 $app->qrcode->forever("foo");
// Array
// (
//     [ticket] => gQFD8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTmFjVTRWU3ViUE8xR1N4ajFwMWsAAgS2uItZAwQA6QcA
//     [url] => http://weixin.qq.com/q/02NacU4VSubPO1GSxj1p1k
// )
```

## 獲取二維碼網址

```php
$url = $app->qrcode->url($ticket);
// https://api.weixin.qq.com/cgi-bin/showqrcode?ticket=TICKET
```

## 獲取二維碼內容

```php
$url = $app->qrcode->url($ticket);

$content = file_get_contents($url); // 得到二進位制圖片內容

file_put_contents(__DIR__ . '/code.jpg', $content); // 寫入檔案
```
