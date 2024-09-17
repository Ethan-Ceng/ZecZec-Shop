# 示例

> 👏🏻 歡迎點選本頁下方 "幫助我們改善此頁面！" 連結參與貢獻更多的使用示例！



<details>
    <summary>JSAPI 下單</summary>

> 官方文件：<https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_1.shtml>

```php
$response = $app->getClient()->postJson("v3/pay/transactions/jsapi", [
   "mchid" => "1518700000", // <---- 請修改為您的商戶號
   "out_trade_no" => "native12177525012012070352333'.rand(1,1000).'",
   "appid" => "wx6222e9f48a0xxxxx", // <---- 請修改為服務號的 appid
   "description" => "Image形象店-深圳騰大-QQ公仔",
   "notify_url" => "https://weixin.qq.com/",
   "amount" => [
        "total" => 1,
        "currency" => "CNY"
    ],
    "payer" => [
        "openid" => "o4GgauInH_RCEdvrrNGrnxxxxxx" // <---- 請修改為服務號下單使用者的 openid
    ]
]);

\dd($response->toArray(false));
```

</details>


<details>
    <summary>Native 下單</summary>

```php
$response = $app->getClient()->postJson('v3/pay/transactions/native', [
    'mchid' => (string)$app->getMerchant()->getMerchantId(),
    'out_trade_no' => 'native20210720xxx',
    'appid' => 'wxe2fb06xxxxxxxxxx6',
    'description' => 'Image形象店-深圳騰大-QQ公仔',
    'notify_url' => 'https://weixin.qq.com/',
    'amount' => [
        'total' => 1,
        'currency' => 'CNY',
    ]
]);

print_r($response->toArray(false));
```
</details>


<details>
    <summary>查詢訂單（商戶訂單號）</summary>

```php

$outTradeNo = 'native20210720xxx';
$response = $app->getClient()->get("v3/pay/transactions/out-trade-no/{$outTradeNo}", [
    'query'=>[
        'mchid' =>  $app->getMerchant()->getMerchantId()
    ]
]);

print_r($response->toArray());
```
</details>


<details>
    <summary>查詢訂單（微信訂單號）</summary>

```php
$transactionId = '217752501201407033233368018';
$response = $app->getClient()->get("pay/transactions/id/{$transactionId}", [
    'query'=>[
        'mchid' =>  $app->getMerchant()->getMerchantId()
    ]
]);

print_r($response->toArray());
```
</details>

<details>
    <summary>Laravel 中處理微信支付回撥</summary>

> 記得需要將此類路由關閉 csrf 驗證。

```php
// 假設你設定的通知地址notify_url為: https://easywechat.com/payment_notify

// 注意：通知地址notify_url必須為https協議

Route::post('payment_notify', function () {
    // $app 為你例項化的支付物件，此處省略例項化步驟
    $server = $app->getServer();

    // 處理支付結果事件
    $server->handlePaid(function ($message) {
        // $message 為微信推送的通知結果，詳看微信官方文件

        // 微信支付訂單號 $message['transaction_id']
        // 商戶訂單號 $message['out_trade_no']
        // 商戶號 $message['mchid']
        // 具體看微信官方文件...
        // 進行業務處理，如存資料庫等...
    });

    // 處理退款結果事件
    $server->handleRefunded(function ($message) {
        // 同上，$message 詳看微信官方文件
        // 進行業務處理，如存資料庫等...
    });

    return $server->serve();
});
```
</details>
  
<details>
   <summary>付款（V2）</summary>

```php
$response = $api->post('/mmpaymkttransfers/promotion/transfers', [
    'body' => [
        'mch_appid' => $app->getConfig()['app_id'],     //注意在配置檔案中加上app_id
        'mchid' => $app->getConfig()['mch_id'],         //商戶號
        'partner_trade_no' => '202203081646729819743',  // 商戶訂單號，需保持唯一性(只能是字母或者數字，不能包含有符號)
        'openid' => 'ogn1H45HCRxVRiEMLbLLuABbxxxx',     //使用者openid
        'check_name' => 'FORCE_CHECK',                  // NO_CHECK：不校驗真實姓名, FORCE_CHECK：強校驗真實姓名
        're_user_name'=> '使用者真實姓名',                  // 如果 check_name 設定為 FORCE_CHECK 則必填使用者真實姓名
        'amount' => '100',                              //金額
        'desc' => '理賠',                                // 企業付款操作說明資訊。必填
    ],
    'local_cert' => $app->getConfig()['certificate'], //v2證書絕對路徑
    'local_pk' => $app->getConfig()['private_key'],   //v2證書金鑰絕對路徑
]);

print_r($response->toArray());
```
</details>
  
<!--
<details>
    <summary>標題</summary>
內容
</details>
-->
