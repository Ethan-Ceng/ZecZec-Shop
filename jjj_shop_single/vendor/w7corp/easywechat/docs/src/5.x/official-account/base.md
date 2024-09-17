# 基礎介面

## 清理介面呼叫次數

> 此介面官方有每月呼叫限制，不可隨意呼叫

```php
$app->base->clearQuota();
```

## 獲取微信伺服器 IP (或IP段)

```php
$app->base->getValidIps();
```