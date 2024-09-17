# 小微商戶配置

## 關注功能配置

```php
$response = $app->merchantConfig->setFollowConfig(string $subAppId, string $subscribeAppId, string $receiptAppId = '', string $subMchId = '');
```
> 注意：`subscribe_appid`，`receipt_appid` 兩個引數二選一，兩個都填的話SDK預設選第一個，具體請參考小微商戶專屬文件

## 開發配置新增支付目錄

```php
$response = $app->merchantConfig->addPath(string $jsapiPath, string $appId = '', string $subMchId = '');
```

## 新增對應APPID關聯

```php
$response = $app->merchantConfig->bindAppId(string $subAppId, string $appId = '', string $subMchId = '');
```

## 開發配置查詢

```php
$response = $app->merchantConfig->getConfig(string $subMchId = '', string $appId = '');
```

> 以上介面呼叫過 `setSubMchId` 方法並且兩個引數都傳入過 則無需傳入 `sub_mch_id` 和 `appid` 引數