# 應用管理

> 企業微信在 17 年 11 月對 API 進行了大量的改動，應用管理部分已經沒啥用了

應用管理是企業微信中比較特別的地方，因為它的使用是不基於應用的，或者說基於任何一個應用都能訪問這些 API，所以在用法上是直接呼叫 work 例項的 `agent` 屬性。

```php
$config = [
    ...
];

$app = Factory::work($config);
```

## 應用列表

```php
$agents = $app->agent->list(); // 測試拿不到內容
```

## 應用詳情

```php
$agents = $app->agent->get($agentId); // 只能傳配置檔案中的 id，API 改動所致
```

## 設定應用

```php
$agents = $app->agent->set($agentId, ['foo' => 'bar']);
```
