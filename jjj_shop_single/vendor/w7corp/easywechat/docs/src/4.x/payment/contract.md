# 簽約

## 公眾號簽約

> 引數 `appid`, `version`, `timestamp`, `sign` 可不用傳入

```php
$result = $app->contract->web([
    'mch_id' => '1200009811',
    'plan_id' => '12535',
    'contract_code' => '100000',
    'contract_display_account' => '微信代扣',
    'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action',
]);
```

## APP 簽約

```php
$result = $app->contract->app(array $params);
```

## H5 簽約

```php
$result = $app->contract->h5(array $params);
```

## 小程式簽約

```php
$result = $app->jssdk->contractConfig(array $params);
```

## 申請扣款

```php
$result = $app->contract->apply(array $params);
```

## 申請解約

```php
$result = $app->contract->delete(array $params);
```
