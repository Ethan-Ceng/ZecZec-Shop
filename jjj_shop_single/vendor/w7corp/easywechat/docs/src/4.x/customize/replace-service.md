# 自定義服務模組

由於使用了容器模式來組織各模組的例項，意味著你可以比較容易的替換掉已經有的服務，以公眾號服務為例：

```php

<...>

$app = Factory::officialAccount($config);

$app->rebind('request', new MyCustomRequest(...)); 
```

這裡的 `request` 為 SDK 內部服務名稱。
