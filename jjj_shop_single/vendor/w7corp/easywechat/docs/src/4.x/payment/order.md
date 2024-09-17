# 訂單

## 統一下單

沒錯，什麼 H5 支付，公眾號支付，掃碼支付，支付中籤約，全部都是用這個介面下單。

> 引數 `appid`, `mch_id`, `nonce_str`, `sign`, `sign_type` 可不用傳入

> 服務商模式下, 需使用 `sub_openid`, 並傳入`sub_mch_id` 和`sub_appid`

```php
$result = $app->order->unify([
    'body' => '騰訊充值中心-QQ會員充值',
    'out_trade_no' => '20150806125346',
    'total_fee' => 88,
    'spbill_create_ip' => '123.12.12.123', // 可選，如不傳該引數，SDK 將會自動獲取相應 IP 地址
    'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付結果通知網址，如果不設定則會使用配置裡的預設地址
    'trade_type' => 'JSAPI', // 請對應換成你的支付方式對應的值型別
    'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',
]);

//如trade_type = APP
//需要進行二次簽名
(new \EasyWeChat\Payment\Jssdk\Client($app))->appConfig($result['prepay_id']);

// $result:
//{
//    "return_code": "SUCCESS",
//    "return_msg": "OK",
//    "appid": "wx2421b1c4390ec4sb",
//    "mch_id": "10000100",
//    "nonce_str": "IITRi8Iabbblz1J",
//    "openid": "oUpF8uMuAJO_M2pxb1Q9zNjWeSs6o",
//    "sign": "7921E432F65EB8ED0CE9755F0E86D72F2",
//    "result_code": "SUCCESS",
//    "prepay_id": "wx201411102639507cbf6ffd8b0779950874",
//    "trade_type": "JSAPI"
//}
```

**第二個引數**為是否[支付中籤約](https://pay.weixin.qq.com/wiki/doc/api/pap.php?chapter=18_13&index=5)，預設 `false`

> 支付中籤約相關引數 `contract_mchid`, `contract_appid`, `request_serial` 可不用傳入

```php
$isContract = true;

$result = $app->order->unify([
    'body' => '騰訊充值中心-QQ會員充值',
    'out_trade_no' => '20150806125346',
    'total_fee' => 88,
    'spbill_create_ip' => '123.12.12.123', // 可選，如不傳該引數，SDK 將會自動獲取相應 IP 地址
    'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付結果通知網址，如果不設定則會使用配置裡的預設地址
    'trade_type' => 'JSAPI', // 請對應換成你的支付方式對應的值型別
    'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',

    'plan_id' => 123,// 協議模板id
    'contract_code' => 100001256,// 簽約協議號
    'contract_display_account' => '騰訊充值中心',// 簽約使用者的名稱
    'contract_notify_url' => 'http://easywechat.org/contract_notify'
], $isContract);

//$result:
//{
//  "return_code": "SUCCESS",
//  "return_msg": "OK",
//  "appid": "wx123456",
//  "mch_id": "10000100",
//  "nonce_str": "CfOcMkDFblzulYvI",
//  "sign": "B53F4AFEE7FA6AD5739581486A5CB9C9",
//  "result_code": "SUCCESS",
//  "prepay_id": "wx08175759731015754a5c13791522969400",
//  "trade_type": "JSAPI",
//  "plan_id": "123",
//  "request_serial": "1565258279",
//  "contract_code": "100001256",
//  "contract_display_account": "騰訊充值中心",
//  "out_trade_no": "201908088195558331565258279",
//  "contract_result_code": "SUCCESS"
//}
```

## 查詢訂單

該介面提供所有微信支付訂單的查詢，商戶可以透過該介面主動查詢訂單狀態，完成下一步的業務邏輯。

需要呼叫查詢介面的情況：

> - 當商戶後臺、網路、伺服器等出現異常，商戶系統最終未接收到支付通知；
> - 呼叫支付介面後，返回系統錯誤或未知交易狀態情況；
> - 呼叫被掃支付 API，返回 USERPAYING 的狀態；
> - 呼叫關單或撤銷介面 API 之前，需確認支付狀態；

### 根據商戶訂單號查詢

```php
$app->order->queryByOutTradeNumber("商戶系統內部的訂單號（out_trade_no）");
```

### 根據微信訂單號查詢

```php
$app->order->queryByTransactionId("微信訂單號（transaction_id）");
```

## 關閉訂單

> 注意：訂單生成後不能馬上呼叫關單介面，最短呼叫時間間隔為 5 分鐘。

```php
$app->order->close(商戶系統內部的訂單號（out_trade_no）);
```
