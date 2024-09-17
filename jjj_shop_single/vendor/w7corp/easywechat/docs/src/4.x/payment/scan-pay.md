## 掃碼支付

### 模式一：先生成產品二維碼，掃碼下單後支付

> 請務必先熟悉流程：https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_4

#### 生成產品二維碼內容

```php
$content = $app->scheme($productId); // $productId 為你的產品/商品ID，用於回撥時帶回，自己識別即可

//結果示例：weixin://wxpay/bizpayurl?sign=XXXXX&appid=XXXXX&mch_id=XXXXX&product_id=XXXXXX&time_stamp=XXXXXX&nonce_str=XXXXX
```

將 `$content` 生成二維碼，SDK 並不內建二維碼生成庫，使用你熟悉的工具建立二維碼即可，比如 PHP 部分有以下工具可以選擇：

> - https://github.com/endroid/qr-code
> - https://github.com/SimpleSoftwareIO/simple-qrcode
> - https://github.com/aferrandini/PHPQRCode

#### 處理回撥

當用戶掃碼時，你的回撥介面會收到一個通知，呼叫[統一下單介面](https://www.easywechat.com/docs/master/zh-CN/payment/order)建立訂單後返回 `prepay_id`，你可以使用下面的程式碼處理掃碼通知：

```php
// 掃碼支付通知接收第三個引數 `$alert`，如果觸發該函式，會返回“業務錯誤”到微信伺服器，觸發 `$fail` 則返回“通訊錯誤”
$response = $app->handleScannedNotify(function ($message, $fail, $alert) use ($app) {
    // 如：$alert('商品已售空');
    // 如業務流程正常，則要呼叫“統一下單”介面，並返回 prepay_id 字串，程式碼如下
    $result = $app->order->unify([
        'trade_type' => 'NATIVE',
        'product_id' => $message['product_id'], // $message['product_id'] 則為生成二維碼時的產品 ID
        // ...
    ]);

    return $result['prepay_id'];
});

$response->send();
```

使用者在手機上付完錢以後，你會再收到**付款結果通知**，這時候請參考：[處理微信支付通知](https://www.easywechat.com/docs/master/zh-CN/payment/notify) 更新您的訂單狀態。

### 模式二：先下單，生成訂單後建立二維碼

> ：https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_5

#### 根據使用者選購的商品生成訂單

呼叫[統一下單介面](https://www.easywechat.com/docs/master/zh-CN/payment/order)建立訂單：

```php
$result = $app->order->unify([
      'trade_type' => 'NATIVE',
      'product_id' => $message['product_id'], // $message['product_id'] 則為生成二維碼時的產品 ID
      // ...
  ]);
```

#### 生成二維碼

> 版本 4.1.7+ 支援

從上一步得到的 `$result['code_url']` 得到二維碼內容：

將 `$result['code_url']` 生成二維碼圖片向用戶展示即可掃碼，生成工具上面自己找一下即可。 SDK 不內建

#### 支付通知

這種方式的通知就只有**付款結果通知**了，這時候請參考：[處理微信支付通知](https://www.easywechat.com/docs/master/zh-CN/payment/notify) 更新您的訂單狀態。
