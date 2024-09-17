# 撤銷訂單

目前只有 **刷卡支付** 有此功能。

> 呼叫支付介面後請勿立即呼叫撤銷訂單API，建議支付後至少15s後再呼叫撤銷訂單介面。

## 透過內部訂單號撤銷訂單

```php
$app->reverse->byOutTradeNumber("商戶系統內部的訂單號（out_trade_no）");
```

## 透過微信訂單號撤銷訂單

```php
$app->reverse->byTransactionId("微信的訂單號（transaction_id）");
```
