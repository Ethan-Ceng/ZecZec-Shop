# 移動端

## 透過code獲取使用者資訊

透過iOS或Android應用授權登入，獲取一次性code，通過後端伺服器換取使用者的資訊。

```php
$code = 'CODE';

$app->mobile->getUser(string $code);
```