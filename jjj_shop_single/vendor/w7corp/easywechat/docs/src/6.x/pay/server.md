# 服務端

支付推送和公眾號幾乎一樣，請參考：[公眾號：服務端](../official-account/server.md)。

## 官方文件

- [基礎下單支付結果通知文件](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml)
- [合單支付結果通知文件](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter5_1_13.shtml)
- [退款結果通知文件](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_11.shtml)

## 內建事件處理器

SDK 內建了兩個便捷方法以便於開發者快速處理支付推送事件：

> `$message` 屬性已經預設解密，可直接訪問解密後的屬性；
> 
> 成功狀態 SDK 預設會返回 success, 你可以不用返回任何東西；

### 支付成功事件

🚨 切記：推送資訊不一定靠譜，可能是偽造的，所以拿到推送通知，只取訂單號等必要資訊，其它資訊忽略，拿訂單號重新查詢微信支付訂單的最新狀態再做處理。

> :book: 官方文件：支付結果通知 <https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml>

```php
$server->handlePaid(function (Message $message, \Closure $next) {
    // $message->out_trade_no 獲取商戶訂單號
    // $message->payer['openid'] 獲取支付者 openid
    // 🚨🚨🚨 注意：推送資訊不一定靠譜哈，請務必驗證
    // 建議是拿訂單號呼叫微信支付查詢介面，以查詢到的訂單狀態為準
    return $next($message);
});
```

### 退款成功事件

> :book: 官方文件：退款結果通知 <https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_11.shtml>

```php
$server->handleRefunded(function (Message $message, \Closure $next) {
    // $message->out_trade_no 獲取商戶訂單號
    // $message->payer['openid'] 獲取支付者 openid
    return $next($message);
});
```

## 其它事件處理

以上便捷方法都只處理了**成功狀態**，其它狀態，可以透過自定義事件處理中介軟體的形式處理：

```php
$server->with(function($message, \Closure $next) {
    // $message->event_type 事件型別
    return $next($message);
});
```

## 自助處理推送訊息

你可以透過下面的方式獲取來自微信伺服器的推送訊息：

```php
$message = $server->getRequestMessage(); 
```

`$message` 為一個 `EasyWeChat\OpenWork\Message` 例項。

你可以在處理完邏輯後自行建立一個響應，當然，在不同的框架裡，響應寫法也不一樣，請自行實現。


## 回撥訊息

微信推送的回撥訊息是預設密文的，可[參考文件](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml)，但是 SDK 已經幫你解密好了，所以以上例子中的 `$message` 預設訪問的屬性都是明文的，例如：

```json
{
    "transaction_id":"1217752501201407033233368018",
    "amount":{
        "payer_total":100,
        "total":100,
        "currency":"CNY",
        "payer_currency":"CNY"
    },
    "mchid":"1230000109",
    "trade_state":"SUCCESS",
    "bank_type":"CMC",
    "promotion_detail":[...],
    "success_time":"2018-06-08T10:34:56+08:00",
    "payer":{
        "openid":"oUpF8uMuAJO_M2pxb1Q9zNjWeS6o"
    },
    "out_trade_no":"1217752501201407033233368018",
    "appid":"wxd678efh567hg6787",
    "trade_state_desc":"支付成功",
    "trade_type":"MICROPAY",
    "attach":"自定義資料",
    "scene_info":{
        "device_id":"013467007045764"
    }
}
```

所以你可以直接使用 `$message->transaction_id` 或者 `$message['transaction_id']` 來訪問以上屬性。

#### 怎麼獲取密文屬性呢？

`$message` 物件提供了 `$message->getOriginalAttributes()` 來獲取加密前的資料：

```json
{
    "id": "EV-2018022511223320873",
    "create_time": "2015-05-20T13:29:35+08:00",
    "resource_type": "encrypt-resource",
    "event_type": "TRANSACTION.SUCCESS",
    "summary": "支付成功",
    "resource": {
        "original_type": "transaction",
        "algorithm": "AEAD_AES_256_GCM",
        "ciphertext": "",
        "associated_data": "",
        "nonce": ""
    }
}
```

當然我們還特別封裝了用於獲取事件型別的方法：

```php
$message->getEventType(); // TRANSACTION.SUCCESS
```
