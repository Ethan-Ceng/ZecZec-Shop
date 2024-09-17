title: 小程式
---

## 例項化

```php
<?php
use EasyWeChat\Foundation\Application;

$options = [
    // ...
    'mini_program' => [
        'app_id'   => 'component-app-id',
        'secret'   => 'component-app-secret',
        'token'    => 'component-token',
        'aes_key'  => 'component-aes-key'
        ],
    // ...
    ];

$app = new Application($options);
$miniProgram = $app->mini_program;
```

## 登入

### 透過 Code 換取 SessionKey

```php
// 3.2 版本
$miniProgram->user->getSessionKey($code);
// 3.3 版本
$miniProgram->sns->getSessionKey($code);
```

## 加密資料解密

```php
$miniProgram->encryptor->decryptData($sessionKey, $iv, $encryptData);
```

## 資料分析

### API

- `summaryTrend($from, $to)` 概況趨勢，限定查詢1天資料，即 `$from` 要與 `$to` 相同；
- `dailyVisitTrend($from, $to)` 訪問日趨勢，限定查詢1天資料，即 `$from` 要與 `$to` 相同；
- `weeklyVisitTrend($from, $to)` 訪問周趨勢， `$from` 為週一日期， `$to` 為週日日期；
- `monthlyVisitTrend($from, $to)` 訪問月趨勢， `$from` 為月初日期， `$to` 為月末日期；
- `visitDistribution($from, $to)` 訪問分佈，限定查詢1天資料，即 `$from` 要與 `$to` 相同；
- `dailyRetainInfo($from, $to)` 訪問日留存，限定查詢1天資料，即 `$from` 要與 `$to` 相同；
- `weeklyRetainInfo($from, $to)` 訪問周留存， `$from` 為週一日期， `$to` 為週日日期；
- `montylyRetainInfo($from, $to)` 訪問月留存， `$from` 為月初日期， `$to` 為月末日期；
- `visitPage($from, $to)` 訪問頁面，限定查詢1天資料，即 `$from` 要與 `$to` 相同；

### 程式碼示例

```php
$miniProgram->stats->summaryTrend('20170313', '20170313');
```
