# 提現相關

## 查詢提現狀態

```php
$response = $app->withdraw->queryWithdrawalStatus($date, $subMchId = '');
```
## 重新發起提現

```php
$response = $app->withdraw->requestWithdraw($date, $subMchId = '');
```

> 以上介面呼叫過 `setSubMchId` 方法則無需傳入 `sub_mch_id` 引數