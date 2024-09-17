# JSSDK

JSSDK 模組用於生成調起微信支付以及共享收貨地址的呼叫所需的配置引數。

## 配置

```php
use EasyWeChat\Factory;

$config = [
    // 前面的appid什麼的也得保留哦
    'app_id'             => 'xxxx',
    'mch_id'             => 'your-mch-id',
    'key'                => 'key-for-signature',
    'cert_path'          => 'path/to/your/cert.pem', // XXX: 絕對路徑！！！！
    'key_path'           => 'path/to/your/key',      // XXX: 絕對路徑！！！！
    'notify_url'         => '預設的訂單回撥地址',     // 你也可以在下單時單獨設定來想覆蓋它
    // 'device_info'     => '013467007045764',
    // 'sub_app_id'      => '',
    // 'sub_merchant_id' => '',
    // ...
];

$payment = Factory::payment($config);

$jssdk = $payment->jssdk;
```

## 生成支付 JS 配置

有三種發起支付的方式：[WeixinJSBridge](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6), [JSSDK](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=15_1), [小程式](https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=7_7)

1. WeixinJSBridge:

    ```php
    $json = $jssdk->bridgeConfig($prepayId); // 返回 json 字串，如果想返回陣列，傳第二個引數 false
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
    $config = $jssdk->sdkConfig($prepayId); // 返回陣列
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

3. 小程式:

    ```php
    $config = $jssdk->bridgeConfig($prepayId, false); // 返回陣列
    ```

    javascript:

    ```js
    wx.requestPayment({
        timeStamp: <?= $config['timeStamp'] ?>, //注意 timeStamp 的格式
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

1. 發起 OAuth 授權，獲取使用者 `$accessToken`,參考網頁授權章節。

2. 使用 `$accessToken` 獲取配置

```php
$configForPickAddress = $jssdk->shareAddressConfig($token);

// 拿著這個生成好的配置 $configForPickAddress 去訂單頁（或者直接顯示訂單頁）寫 js 呼叫了
// ...
```

## 生成 APP 支付配置

```php
$config = $jssdk->appConfig($prepayId);
```

`$config` 為陣列格式，你可以用 API 返回給客戶端

# 二維碼生成工具推薦

你也許需要生成二維碼，那麼以下這些供參考：

>  - https://github.com/endroid/QrCode
>  - https://github.com/Bacon/BaconQrCode
>  - https://github.com/SimpleSoftwareIO/simple-qrcode (Bacon/BaconQrCode 的 Laravel 版本)
>  - https://github.com/aferrandini/PHPQRCode
