# 短網址服務


主要使用場景： 開發者用於生成二維碼的原連結（商品、支付二維碼等）太長導致掃碼速度和成功率下降，將原長連結透過此介面轉成短連結再生成二維碼將大大提升掃碼速度和成功率。

## 獲取例項

```php
<?php
use EasyWeChat\Foundation\Application;
// ...
$app = new Application($options);

$url = $app->url;
```

## API

+ `shorten($url)` 長連結轉短連結

example:

```php
$shortUrl = $url->shorten('http://overtrue.me/open-source');
//
```

微信官方文件：http://mp.weixin.qq.com/wiki/
