# 企業支付


你在閱讀本文之前確認你已經仔細閱讀了：[微信支付 | 企業付款文件 ](https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_1)。

## 配置

與其他支付介面一樣，企業支付介面也需要配置如下引數，需要特別注意的是，企業支付相關的全部介面 **都需要使用 SSL 證書**，因此 **cert_path 以及 cert_key 必須正確配置**。

```php
<?php

use EasyWeChat\Foundation\Application;

$options = [
    'app_id' => 'your-app-id',
    // payment
    'payment' => [
        'merchant_id'        => 'your-mch-id',
        'key'                => 'key-for-signature',
        'cert_path'          => 'path/to/your/cert.pem',
        'key_path'           => 'path/to/your/key',
        // ...
    ],
];

$app = new Application($options);

$merchantPay = $app->merchant_pay;
```

## 企業付款

企業付款使用的餘額跟微信支付的收款並非同一賬戶，請注意充值。

### 傳送介面

```php
<?php

$merchantPayData = [
        'partner_trade_no' => str_random(16), //隨機字串作為訂單號，跟紅包和支付一個概念。
        'openid' => $openid, //收款人的openid
        'check_name' => 'NO_CHECK',  //文件中有三種校驗實名的方法 NO_CHECK OPTION_CHECK FORCE_CHECK
        're_user_name'=>'張三',     //OPTION_CHECK FORCE_CHECK 校驗實名的時候必須提交
        'amount' => 100,  //單位為分
        'desc' => '企業付款',
        'spbill_create_ip' => '192.168.0.1',  //發起交易的IP地址
    ];
$result = $merchantPay->send($merchantPayData);

```

> 更多引數請參考官方文件中引數列表。

## 查詢付款資訊

用於商戶對已發放的企業支付進行查詢企業支付的具體資訊。

```php
$partnerTradeNo = "商戶系統內部的訂單號（partner_trade_no）";
$merchantPay->query($partnerTradeNo);
```
