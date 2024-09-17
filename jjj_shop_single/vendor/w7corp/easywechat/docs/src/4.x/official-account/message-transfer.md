# 多客服訊息轉發

多客服的訊息轉發絕對是超級的簡單，轉發的訊息型別為 `transfer`：

```php

use EasyWeChat\Kernel\Messages\Transfer;

// 轉發收到的訊息給客服
$app->server->push(function($message) {
  return new Transfer();
});

$response = $app->server->serve();
```

當然，你也可以指定轉發給某一個客服：

```php
use EasyWeChat\Kernel\Messages\Transfer;

$app->server->push(function($message) {
    return new Transfer($account);
});
```