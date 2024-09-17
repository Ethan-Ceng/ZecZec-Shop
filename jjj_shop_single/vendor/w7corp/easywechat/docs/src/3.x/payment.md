# 支付


你在閱讀本文之前確認你已經仔細閱讀了：[微信支付 | 商戶平臺開發文件](https://pay.weixin.qq.com/wiki/doc/api/index.html)。

網友貢獻的教程：[小能手馬闖set 釋出在 Laravel-China 的文章《基於 Laravel5.1 LTS 版的微信開發》](https://laravel-china.org/topics/3146)

## 配置

配置在前面的例子中已經提到過了，支付的相關配置如下：


```php
<?php

use EasyWeChat\Foundation\Application;

$options = [
    // 前面的appid什麼的也得保留哦
    'app_id' => 'xxxx',
    // ...

    // payment
    'payment' => [
        'merchant_id'        => 'your-mch-id',
        'key'                => 'key-for-signature',
        'cert_path'          => 'path/to/your/cert.pem', // XXX: 絕對路徑！！！！
        'key_path'           => 'path/to/your/key',      // XXX: 絕對路徑！！！！
        'notify_url'         => '預設的訂單回撥地址',       // 你也可以在下單時單獨設定來想覆蓋它
        // 'device_info'     => '013467007045764',
        // 'sub_app_id'      => '',
        // 'sub_merchant_id' => '',
        // ...
    ],
];

$app = new Application($options);

$payment = $app->payment;
```

## 建立訂單

### 正常模式

```php
<?php

use EasyWeChat\Payment\Order;

$attributes = [
    'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
    'body'             => 'iPad mini 16G 白色',
    'detail'           => 'iPad mini 16G 白色',
    'out_trade_no'     => '1217752501201407033233368018',
    'total_fee'        => 5388, // 單位：分
    'notify_url'       => 'http://xxx.com/order-notify', // 支付結果通知網址，如果不設定則會使用配置裡的預設地址
    'openid'           => '當前使用者的 openid', // trade_type=JSAPI，此引數必傳，使用者在商戶appid下的唯一標識，
    // ...
];

$order = new Order($attributes);

```

### 子服務商模式

```php
<?php

use EasyWeChat\Payment\Order;

$attributes = [
    'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
    'body'             => 'iPad mini 16G 白色',
    'detail'           => 'iPad mini 16G 白色',
    'out_trade_no'     => '1217752501201407033233368018',
    'total_fee'        => 5388, // 單位：分
    'notify_url'       => 'http://xxx.com/order-notify', // 支付結果通知網址，如果不設定則會使用配置裡的預設地址
    'sub_openid'        => '當前使用者的 openid', // 如果傳入sub_openid, 請在例項化Application時, 同時傳入$sub_app_id, $sub_merchant_id
    // ...
];

$order = new Order($attributes);

```


通知url必須為直接可訪問的url，不能攜帶引數。示例：notify_url：“https://pay.weixin.qq.com/wxpay/pay.action”

## 下單介面

### 刷卡支付

[官方文件](https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_10)

```php
$result = $payment->pay($order);
```

> 也許你需要生成二維碼圖片，參考頁面底部相關的包推薦

## 統一下單

[公眾號支付](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1)、[掃碼支付](https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_1)、[APP 支付](https://pay.weixin.qq.com/wiki/doc/api/app.php?chapter=9_1) 都統一使用此介面完成訂單的建立。

```php
$result = $payment->prepare($order);
if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
    $prepayId = $result->prepay_id;
}
```

## 支付結果通知

在使用者成功支付後，微信伺服器會向該 **訂單中設定的回撥URL** 發起一個 POST 請求，請求的內容為一個 XML。裡面包含了所有的詳細資訊，具體請參考：
[支付結果通用通知](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_7)

在本 SDK 中處理回撥真的再簡單不過了，請求驗證你就不用管了，SDK 已經為你做好了，你只需要關注業務即可：

```php
$response = $app->payment->handleNotify(function($notify, $successful){
    // 你的邏輯
    return true; // 或者錯誤訊息
});

$response->send(); // Laravel 裡請使用：return $response;
```

這裡需要注意的有幾個點：

1. `handleNotify` 只接收一個 [`callable`](http://php.net/manual/zh/language.types.callable.php) 引數，通常用一個匿名函式即可。
2. 該匿名函式接收兩個引數，這兩個引數分別為：

    - `$notify` 為封裝了通知資訊的 `EasyWeChat\Support\Collection` 物件，前面已經講過這裡就不贅述了，你可以以物件或者陣列形式來讀取通知內容，比如：`$notify->total_fee` 或者 `$notify['total_fee']`。
    - `$successful` 這個引數其實就是判斷 **使用者是否付款成功了**（result_code == 'SUCCESS'）

3. 該函式返回值就是告訴微信 **“我是否處理完成”**，如果你返回一個 `false` 或者一個具體的錯誤訊息，那麼微信會在稍後再次繼續通知你，直到你明確的告訴它：“我已經處理完成了”，在函數里 `return true;` 代表處理完成。

4. `handleNotify` 返回值 `$response` 是一個 Response 物件，如果你要直接輸出，使用 `$response->send()`, 在一些框架裡不是輸出而是返回：`return $response`。

通常我們的處理邏輯大概是下面這樣（**以下只是虛擬碼**）：

```php
$response = $app->payment->handleNotify(function($notify, $successful){
    // 使用通知裡的 "微信支付訂單號" 或者 "商戶訂單號" 去自己的資料庫找到訂單
    $order = 查詢訂單($notify->out_trade_no);

    if (!$order) { // 如果訂單不存在
        return 'Order not exist.'; // 告訴微信，我已經處理完了，訂單沒找到，別再通知我了
    }

    // 如果訂單存在
    // 檢查訂單是否已經更新過支付狀態
    if ($order->paid_at) { // 假設訂單欄位“支付時間”不為空代表已經支付
        return true; // 已經支付成功了就不再更新了
    }

    // 使用者是否支付成功
    if ($successful) {
        // 不是已經支付狀態則修改為已經支付狀態
        $order->paid_at = time(); // 更新支付時間為當前時間
        $order->status = 'paid';
    } else { // 使用者支付失敗
        $order->status = 'paid_fail';
    }

    $order->save(); // 儲存訂單

    return true; // 返回處理完成
});

return $response;
```

> 注意：請把 “支付成功與否” 與 “是否處理完成” 分開，它倆沒有必然關係。
> 比如：微信通知你使用者支付完成，但是支付失敗了(result_code 為 'FAIL')，你應該**更新你的訂單為支付失敗**，但是要**告訴微信處理完成**。


## 撤銷訂單API

目前只有 **刷卡支付** 有此功能。

> 呼叫支付介面後請勿立即呼叫撤銷訂單API，建議支付後至少15s後再呼叫撤銷訂單介面。

```php
$orderNo = "商戶系統內部的訂單號（out_trade_no）";
$payment->reverse($orderNo);
```

或者：

```php

$orderNo = "微信的訂單號（transaction_id）";
$payment->reverseByTransactionId($orderNo);
```


## 查詢訂單

該介面提供所有微信支付訂單的查詢，商戶可以透過該介面主動查詢訂單狀態，完成下一步的業務邏輯。

需要呼叫查詢介面的情況：

  - 當商戶後臺、網路、伺服器等出現異常，商戶系統最終未接收到支付通知；
  - 呼叫支付介面後，返回系統錯誤或未知交易狀態情況；
  - 呼叫被掃支付API，返回USERPAYING的狀態；
  - 呼叫關單或撤銷介面API之前，需確認支付狀態；

```php
$orderNo = "商戶系統內部的訂單號（out_trade_no）";
$payment->query($orderNo);
```

或者：

```php

$orderNo = "微信的訂單號（transaction_id）";
$payment->queryByTransactionId($orderNo);
```

## 關閉訂單

> 注意：訂單生成後不能馬上呼叫關單介面，最短呼叫時間間隔為5分鐘。

```php
$orderNo = "商戶系統內部的訂單號（out_trade_no）";
$payment->close($orderNo);
```

## 申請退款

當交易發生之後一段時間內，由於買家或者賣家的原因需要退款時，賣家可以透過退款介面將支付款退還給買家，微信支付將在收到退款請求並且驗證成功之後，按照退款規則將支付款按原路退到買家帳號上。

注意：

> 1、交易時間超過一年的訂單無法提交退款；
> 2、微信支付退款支援單筆交易分多次退款，多次退款需要提交原支付訂單的商戶訂單號和設定不同的退款單號。一筆退款失敗後重新提交，要採用原來的退款單號。總退款金額不能超過使用者實際支付金額。

```php
$payment->refund(訂單號，退款單號，總金額，退款金額，操作員，退款單號型別(out_trade_no/transaction_id)，退款賬戶(REFUND_SOURCE_UNSETTLED_FUNDS/REFUND_SOURCE_RECHARGE_FUNDS))
```

參考：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_4

例子：

```php
# 1. 使用商戶訂單號退款
$result = $payment->refund($orderNo, $refundNo, 100); // 總金額 100 退款 100，操作員：商戶號
// or
$result = $payment->refund($orderNo, $refundNo, 100, 80); // 總金額 100， 退款 80，操作員：商戶號
// or
$result = $payment->refund($orderNo, $refundNo, 100, 80, 1900000109); // 總金額 100， 退款 80，操作員：1900000109
// or
$result = $payment->refund($orderNo, $refundNo, 100, 80, 1900000109, 'out_trade_no'); // 總金額 100， 退款 80，操作員：1900000109, 退款單號：使用商戶訂單號退款
// or
$result = $payment->refund($orderNo, $refundNo, 100, 80, 1900000109, 'out_trade_no', 'REFUND_SOURCE_RECHARGE_FUNDS'); // 總金額 100， 退款 80，操作員：1900000109, 退款單號：使用商戶訂單號退款, 退款賬戶：可用餘額退款

# 2. 使用 TransactionId 退款
$result = $payment->refundByTransactionId($transactionId, $refundNo, 100); // 總金額 100 退款 100，操作員：商戶號
// or
$result = $payment->refundByTransactionId($transactionId, $refundNo, 100, 80); // 總金額 100， 退款 80，操作員：商戶號
// or
$result = $payment->refundByTransactionId($transactionId, $refundNo, 100, 80, 1900000109); // 總金額 100， 退款 80，操作員：1900000109
// or
$result = $payment->refundByTransactionId($transactionId, $refundNo, 100, 80, 1900000109, 'REFUND_SOURCE_RECHARGE_FUNDS'); // 總金額 100， 退款 80，操作員：1900000109，退款賬戶：可用餘額退款
```

> $refundNo 為商戶退款單號，自己生成用於自己識別即可。

## 查詢退款

提交退款申請後，透過呼叫該介面查詢退款狀態。退款有一定延時，用零錢支付的退款20分鐘內到賬，銀行卡支付的退款3個工作日後重新查詢退款狀態。

```php
$result = $payment->queryRefund($outTradeNo);
// or
$result = $payment->queryRefundByTransactionId($transactionId);
// or
$result = $payment->queryRefundByRefundNo($outRefundNo);
// or
$result = $payment->queryRefundByRefundId($refundId);
```

## 下載對賬單

```php
$bill = $payment->downloadBill('20140603')->getContents(); // type: ALL
// or
$bill = $payment->downloadBill('20140603', 'SUCCESS')->getContents(); // type: SUCCESS
// bill 為 csv 格式的內容

// 儲存為檔案
file_put_contents('YOUR/PATH/TO/bill-20140603.csv', $bill);
```

第二個引數為型別：

 - **ALL**：返回當日所有訂單資訊（預設值）
 - **SUCCESS**：返回當日成功支付的訂單
 - **REFUND**：返回當日退款訂單
 - **REVOKED**：已撤銷的訂單

## 測速上報

```php
$payment->report($api, $timeConsuming, $resultCode, $returnCode);
// or
$payment->report($api, $timeConsuming, $resultCode, $returnCode, [
        'err_code'     => 'xxxx',
        'err_code_des' => '...',
        'out_trade_no' => '...',
        'user_ip'      => '...',
    ]);
```

## 轉換短連結

```php
$shortUrl = $payment->urlShorten('http://easywechat.org');
```

## 授權碼查詢OPENID介面

```php
$response = $payment->authCodeToOpenId($authCode);
$response->openid;
```

## 生成支付 JS 配置

有兩種發起支付的方式：[WeixinJSBridge](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6), [JSSDK](https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141115&token=&lang=zh_CN)

1. WeixinJSBridge:

    ```php
    $json = $payment->configForPayment($prepayId); // 返回 json 字串，如果想返回陣列，傳第二個引數 false
    ```

    javascript:

    ```js
    ...
    WeixinJSBridge.invoke(
           'getBrandWCPayRequest', <?= $json ?>,
           function(res){
               if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                    // 使用以上方式判斷前端返回,微信團隊鄭重提示：
                    // res.err_msg將在使用者支付成功後返回
                    // ok，但並不保證它絕對可靠。
               }
           }
       );
    ...
    ```

2. JSSDK:

    ```php
    $config = $payment->configForJSSDKPayment($prepayId); // 返回陣列
    ```

    javascript:

    ```js
    wx.chooseWXPay({
        timestamp: <?= $config['timestamp'] ?>,
        nonceStr: '<?= $config['nonceStr'] ?>',
        package: '<?= $config['package'] ?>',
        signType: '<?= $config['signType'] ?>',
        paySign: '<?= $config['paySign'] ?>', // 支付簽名
        success: function (res) {
            // 支付成功後的回撥函式
        }
    });
    ```

## 生成共享收貨地址 JS 配置

1. 發起 OAuth 授權：

```php
use EasyWeChat\Support\Url as UrlHelper;

// 檢查當前不是微信 oauth 的回撥，則跳過去授權
// 注意，授權回撥地址為當前頁
if (empty($_GET['code'])) {
    $currentUrl = UrlHelper::current(); // 獲取當前頁 URL
    $response = $app->oauth->scopes(['snsapi_base'])->redirect($currentUrl);

    return $response; // or echo $response;

}
// 授權回來
$oauthUser = $app->oauth->user();
$token = $oauthUser->getAccessToken();
$configForPickAddress = $payment->configForShareAddress($token);

// 拿著這個生成好的配置 $configForPickAddress 去訂單頁（或者直接顯示訂單頁）寫 js 呼叫了
// ...
```

## 生成 APP 支付配置

```php
$config = $payment->configForAppPayment($prepayId);
```

`$config` 為陣列格式，你可以用 API 返回給客戶端

# 二維碼生成工具推薦

你也許需要生成二維碼，那麼以下這些供參考：

- https://github.com/endroid/QrCode
- https://github.com/Bacon/BaconQrCode
- https://github.com/SimpleSoftwareIO/simple-qrcode (Bacon/BaconQrCode 的 Laravel 版本)
- https://github.com/aferrandini/PHPQRCode

