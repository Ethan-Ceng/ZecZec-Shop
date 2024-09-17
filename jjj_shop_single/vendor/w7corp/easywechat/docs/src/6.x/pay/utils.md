# 工具

提供各種支付需要的配置生成方法。

## 配置

```php
<?php
use EasyWeChat\Pay\Application;

$config = [...];

$app = new Application($config);

$utils = $app->getUtils();
```

> 注意

## 生成支付 JS 配置

有四種發起支付的方式：WeixinJSBridge, JSSDK, 小程式支付, APP

### WeixinJSBridge 調起支付 API

:book: [官方文件 - WeixinJSBridge 調起支付](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_4.shtml)

 ```php
 $appId = '商戶申請的公眾號對應的 appid，由微信支付生成，可在公眾號後臺檢視';
 $signType = 'RSA'; // 預設RSA，v2要傳MD5
 $config = $utils->buildBridgeConfig($prepayId, $appId, $signType); // 返回陣列
 ```

呼叫示例

 ```js
 WeixinJSBridge.invoke(
  'getBrandWCPayRequest', {
    timeStamp: "<?= $config['timeStamp'] ?>", //注意 timeStamp 的格式
    nonceStr: "<?= $config['nonceStr'] ?>",
    package: "?= $config['package'] ?>",
    signType: "<?= $config['signType'] ?>",
    paySign: "<?= $config['paySign'] ?>", // 支付簽名
  },
  function (res) {
    if (res.err_msg == "get_brand_wcpay_request:ok") {
      // 使用以上方式判斷前端返回,微信團隊鄭重提示：
      // res.err_msg將在使用者支付成功後返回
      // ok，但並不保證它絕對可靠。
    }
  }
);
 ```

### JSSDK 調起支付 API

:book: [官方文件 - wx.chooseWXPay 調起支付](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html#58)

 ```php
 $appId = '商戶申請的公眾號對應的 appid，由微信支付生成，可在公眾號後臺檢視';
 $signType = 'RSA'; // 預設RSA，v2要傳MD5
 $config = $utils->buildSdkConfig($prepayId, $appId, $signType); // 返回陣列
 ```

呼叫例項:

```js
wx.chooseWXPay({
  timestamp: "<?= $config['timestamp'] ?>",
  nonceStr: "<?= $config['nonceStr'] ?>",
  package: "<?= $config['package'] ?>",
  signType: "<?= $config['signType'] ?>",
  paySign: "<?= $config['paySign'] ?>",
  success: function (res) {
    // 支付成功後的回撥函式
  }
});
```

### 小程式調起支付 API

:book: [官方文件 - 小程式調起支付 API](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_5_4.shtml)

 ```php
 $appId = '商戶申請的小程式對應的appid，由微信支付生成，可在小程式後臺檢視';
 $signType = 'RSA'; // 預設RSA，v2要傳MD5
 $config = $utils->buildMiniAppConfig($prepayId, $appId, $signType); // 返回陣列
 ```

呼叫示例：

 ```js
 wx.requestPayment({
  timeStamp: "<?= $config['timeStamp'] ?>",
  nonceStr: "<?= $config['nonceStr'] ?>",
  package: "<?= $config['package'] ?>",
  signType: "<?= $config['signType'] ?>",
  paySign: "<?= $config['paySign'] ?>",
  success: function (res) {
    // 支付成功後的回撥函式
  }
});
 ```

### APP 調起支付 API

:book: [官方文件 - APP 調起支付 API](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_2_4.shtml)

 ```php
 $appId = '商戶申請的公眾號對應的appid，由微信支付生成，可在公眾號後臺檢視';
 $config = $utils->buildAppConfig($prepayId, $appId); // 返回陣列
 ```

呼叫示例：[官方文件 - APP 調起支付 API](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_2_4.shtml)

# 二維碼生成工具推薦

> :heart: 建議由前端生成二維碼

確實需要用PHP生成二維碼，那麼以下這些供參考：

- [endroid/QrCode](https://github.com/endroid/QrCode)
- [Bacon/BaconQrCode](https://github.com/Bacon/BaconQrCode)
- [SimpleSoftwareIO/simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode) Bacon/BaconQrCode 的 Laravel 版本
- [aferrandini/PHPQRCode](https://github.com/aferrandini/PHPQRCode)
