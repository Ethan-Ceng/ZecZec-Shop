# 自動回覆


## 獲取例項

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$reply = $app->reply;
```

## 獲取當前設定的回覆規則

```php
$reply->current();
```