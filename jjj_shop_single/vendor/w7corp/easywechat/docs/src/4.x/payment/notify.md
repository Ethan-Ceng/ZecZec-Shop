# 通知

## 支付結果通知

在使用者成功支付後，微信伺服器會向該 **訂單中設定的回撥 URL** 發起一個 POST 請求，請求的內容為一個 XML。裡面包含了所有的詳細資訊，具體請參考：[支付結果通知](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_7)

而對於使用者的退款操作，在退款成功之後也會有一個非同步回撥通知。

本 SDK 內預置了相關方法，以方便開發者處理這些通知，具體用法如下：

只需要在控制器中使用 `handlePaidNotify()` 方法，在其中對自己的業務進行處理並向微信伺服器傳送一個響應。

```php
$response = $app->handlePaidNotify(function ($message, $fail) {
    // 你的邏輯
    return true;
    // 或者錯誤訊息
    $fail('Order not exists.');
});

$response->send(); // Laravel 裡請使用：return $response;
```

這裡需要注意的有幾個點：

0. 退款結果通知和掃碼支付通知的使用方法均類似。
1. `handlePaidNotify` 只接收一個 [`Closure`](http://php.net/manual/zh/class.closure.php) 匿名函式。
2. 該匿名函式接收兩個引數，這兩個引數分別為：

   > - `$message` 為微信推送過來的通知資訊，為一個數組；
   > - `$fail` 為一個函式，觸發該函式可向微信伺服器返回對應的錯誤資訊，**微信會稍後重試再通知**。

3. 該函式返回值就是告訴微信 **“我是否處理完成”**。如果你觸發 `$fail` 函式，那麼微信會在稍後再次繼續通知你，直到你明確的告訴它：“我已經處理完成了”，**只有**在函數里 `return true;` 才代表處理完成。

4. `handlePaidNotify` 返回值 `$response` 是一個 Response 物件，如果你要直接輸出，使用 `$response->send()`, 在一些框架裡（如 Laravel）不是輸出而是返回：`return $response`。

通常我們的處理邏輯大概是下面這樣（**以下只是虛擬碼**）：

```php
$response = $app->handlePaidNotify(function($message, $fail){
    // 使用通知裡的 "微信支付訂單號" 或者 "商戶訂單號" 去自己的資料庫找到訂單
    $order = 查詢訂單($message['out_trade_no']);

    if (!$order || $order->paid_at) { // 如果訂單不存在 或者 訂單已經支付過了
        return true; // 告訴微信，我已經處理完了，訂單沒找到，別再通知我了
    }

    ///////////// <- 建議在這裡呼叫微信的【訂單查詢】介面查一下該筆訂單的情況，確認是已經支付 /////////////

    if ($message['return_code'] === 'SUCCESS') { // return_code 表示通訊狀態，不代表支付狀態
        // 使用者是否支付成功
        if (array_get($message, 'result_code') === 'SUCCESS') {
            $order->paid_at = time(); // 更新支付時間為當前時間
            $order->status = 'paid';

        // 使用者支付失敗
        } elseif (array_get($message, 'result_code') === 'FAIL') {
            $order->status = 'paid_fail';
        }
    } else {
        return $fail('通訊失敗，請稍後再通知我');
    }

    $order->save(); // 儲存訂單

    return true; // 返回處理完成
});

$response->send(); // return $response;
```

> 注意：請把 “支付成功與否” 與 “是否處理完成” 分開，它倆沒有必然關係。
> 比如：微信通知你使用者支付完成，但是支付失敗了(result_code 為 'FAIL')，你應該**更新你的訂單為支付失敗**，但是要**告訴微信處理完成**。

## 退款結果通知

使用示例：

```php
$response = $app->handleRefundedNotify(function ($message, $reqInfo, $fail) {
    // 其中 $message['req_info'] 獲取到的是加密資訊
    // $reqInfo 為 message['req_info'] 解密後的資訊
    // 你的業務邏輯...
    return true; // 返回 true 告訴微信“我已處理完成”
    // 或返回錯誤原因 $fail('引數格式校驗錯誤');
});

$response->send();
```

## 掃碼支付通知

掃碼支付【模式一】：https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_4

```php
// 掃碼支付通知接收第三個引數 `$alert`，如果觸發該函式，會返回“業務錯誤”到微信伺服器，觸發 `$fail` 則返回“通訊錯誤”
$response = $app->handleScannedNotify(function ($message, $fail, $alert) use ($app) {
    // 如：$alert('商品已售空');
    // 如業務流程正常，則要呼叫“統一下單”介面，並返回 prepay_id 字串，程式碼如下
    $result = $app->order->unify([
        'trade_type' => 'NATIVE',
        'product_id' => $message['product_id'],
        // ...
    ]);

    return $result['prepay_id'];
});

$response->send();
```
