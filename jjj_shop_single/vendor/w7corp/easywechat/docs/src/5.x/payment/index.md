# 支付

你在閱讀本文之前確認你已經仔細閱讀了：[微信支付 | 商戶平臺開發文件](https://pay.weixin.qq.com/wiki/doc/api/index.html)。

> 🚨 此版本僅支援微信支付 V2 版介面，V3 版介面請使用 6.x 版本或在支付模組獨立使用 [wechatpay/wechatpay](https://packagist.org/packages/wechatpay/wechatpay) 來支援 v2+v3 支付介面。

## 配置

配置在前面的例子中已經提到過了，支付的相關配置如下：

```php
use EasyWeChat\Factory;

$config = [
    // 必要配置
    'app_id'             => 'xxxx',
    'mch_id'             => 'your-mch-id',
    'key'                => 'key-for-signature',   // API v2 金鑰 (注意: 是v2金鑰 是v2金鑰 是v2金鑰)

    // 如需使用敏感介面（如退款、傳送紅包等）需要配置 API 證書路徑(登入商戶平臺下載 API 證書)
    'cert_path'          => 'path/to/your/cert.pem', // XXX: 絕對路徑！！！！
    'key_path'           => 'path/to/your/key',      // XXX: 絕對路徑！！！！

    'notify_url'         => '預設的訂單回撥地址',     // 你也可以在下單時單獨設定來想覆蓋它
];

$app = Factory::payment($config);
```

### 服務商

#### 設定子商戶資訊

```php
$app->setSubMerchant('sub-merchant-id', 'sub-app-id');  // 子商戶 AppID 為可選項
```

### 刷卡支付

[官方文件](https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_10)

```php
$result = $app->pay([
    'body' => 'image形象店-深圳騰大- QQ公仔',
    'out_trade_no' => '1217752501201407033233368018',
    'total_fee' => 888,
    'auth_code' => '120061098828009406',
]);
```

## 授權碼查詢 OPENID 介面

```php
$app->authCodeToOpenid($authCode);
```

## 沙箱模式

微信支付沙箱環境，是提供給微信支付商戶的開發者，用於模擬支付及回撥通知。以驗證商戶是否理解回撥通知、賬單格式，以及是否對異常做了正確的處理。EasyWeChat SDK 對於這一功能進行了封裝，開發者只需一步即可在沙箱模式和常規模式間切換，方便開發與最終的部署。

```php
// 在例項化的時候傳入配置即可
$app = Factory::payment([
    // ...
    'sandbox' => true, // 設定為 false 或註釋則關閉沙箱模式
]);

// 判斷當前是否為沙箱模式：
bool $app->inSandbox();
```

> 特別注意，沙箱模式對於測試用例有嚴格要求，若使用的用例與規定不符，將導致測試失敗。具體用例要求可關注公眾號“微信支付商戶接入驗收助手”（WXPayAssist）檢視。
