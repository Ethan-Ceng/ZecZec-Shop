# 退款

## 申請退款

當交易發生之後一段時間內，由於買家或者賣家的原因需要退款時，賣家可以透過退款介面將支付款退還給買家，微信支付將在收到退款請求並且驗證成功之後，按照退款規則將支付款按原路退到買家帳號上。

注意：

> 1、交易時間超過一年的訂單無法提交退款；
> 2、微信支付退款支援單筆交易分多次退款，多次退款需要提交原支付訂單的商戶訂單號和設定不同的退款單號。一筆退款失敗後重新提交，要採用原來的退款單號。總退款金額不能超過使用者實際支付金額。

參考：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_4

### 根據微信訂單號退款

```php
// 引數分別為：微信訂單號、商戶退款單號、訂單金額、退款金額、其他引數
$app->refund->byTransactionId(string $transactionId, string $refundNumber, int $totalFee, int $refundFee, array $config = []);

// Example:
$result = $app->refund->byTransactionId('transaction-id-xxx', 'refund-no-xxx', 10000, 10000, [
    // 可在此處傳入其他引數，詳細引數見微信支付文件
    'refund_desc' => '商品已售完',
]);

```
### 根據商戶訂單號退款

```php
// 引數分別為：商戶訂單號、商戶退款單號、訂單金額、退款金額、其他引數
$app->refund->byOutTradeNumber(string $number, string $refundNumber, int $totalFee, int $refundFee, array $config = []);

// Example:
$result = $app->refund->byOutTradeNumber('out-trade-no-xxx', 'refund-no-xxx', 20000, 1000, [
    // 可在此處傳入其他引數，詳細引數見微信支付文件
    'refund_desc' => '退運費',
]);
```

> $refundNumber 為商戶退款單號，自己生成用於自己識別即可。

## 查詢退款

提交退款申請後，透過呼叫該介面查詢退款狀態。退款有一定延時，用零錢支付的退款20分鐘內到賬，銀行卡支付的退款3個工作日後重新查詢退款狀態。

可透過 4 種不同型別的單號查詢：

>  - 微信訂單號 => `queryByTransactionId($transactionId)`
>  - 商戶訂單號 => `queryByOutTradeNumber($outTradeNumber)`
>  - 商戶退款單號 => `queryByOutRefundNumber($outRefundNumber)`
>  - 微信退款單號 => `queryByRefundId($refundId)`
