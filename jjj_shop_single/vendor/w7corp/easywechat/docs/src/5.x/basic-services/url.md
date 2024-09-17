# 短網址服務

主要使用場景： 開發者用於生成二維碼的原連結（商品、支付二維碼等）太長導致掃碼速度和成功率下降，將原長連結透過此介面轉成短連結再生成二維碼將大大提升掃碼速度和成功率。

## 長連結轉短連結

```php
$shortUrl = $app->url->shorten('https://easywechat.com');
//
(
    [errcode] => 0
    [errmsg] => ok
    [short_url] => https://w.url.cn/s/Aq7jWrd
)
```