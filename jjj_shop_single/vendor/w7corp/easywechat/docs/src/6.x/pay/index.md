# 微信支付

請仔細閱讀並理解：[微信官方文件 - 微信支付](https://pay.weixin.qq.com/wiki/doc/apiv3/wxpay/pages/index.shtml)

## 例項化

```php
<?php
use EasyWeChat\Pay\Application;

$config = [
    'mch_id' => 1360649000,

    // 商戶證書
    'private_key' => __DIR__ . '/certs/apiclient_key.pem',
    'certificate' => __DIR__ . '/certs/apiclient_cert.pem',

     // v3 API 秘鑰
    'secret_key' => '43A03299A3C3FED3D8CE7B820Fxxxxx',

    // v2 API 秘鑰
    'v2_secret_key' => '26db3e15cfedb44abfbb5fe94fxxxxx',

    // 平臺證書：微信支付 APIv3 平臺證書，需要使用工具下載
    // 下載工具：https://github.com/wechatpay-apiv3/CertificateDownloader
    'platform_certs' => [
        // '/path/to/wechatpay/cert.pem',
    ],

    /**
     * 介面請求相關配置，超時時間等，具體可用引數請參考：
     * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
     */
    'http' => [
        'throw'  => true, // 狀態碼非 200、300 時是否丟擲異常，預設為開啟
        'timeout' => 5.0,
        // 'base_uri' => 'https://api.mch.weixin.qq.com/', // 如果你在國外想要覆蓋預設的 url 的時候才使用，根據不同的模組配置不同的 uri
    ],
];

$app = new Application($config);
```

## API

Application 就是一個工廠類，所有的模組都是從 `$app` 中訪問，並且幾乎都提供了協議和 setter 可自定義修改。

### API Client

封裝了多種模式的 API 呼叫類，你可以選擇自己喜歡的方式呼叫開放平臺任意 API，預設自動處理了 access_token 相關的邏輯。

```php
$app->getClient();
```

:book: 更多說明請參閱：[API 呼叫](../client.md)

### 工具

為了方便開發者生成各種調起支付所需配置，你可以使用工具類：

```php
$app->getUtils();
```

:book: 更多說明請參閱：[工具](utils.md)

### 配置

```php
$config = $app->getConfig();
```

你可以輕鬆使用 `$config->get($key, $default)` 讀取配置，或使用 `$config->set($key, $value)` 在呼叫前修改配置項。

### 支付賬戶

支付賬戶類，提供一系列 API 獲取支付的基本資訊：

```php
$account = $app->getMerchant();

$account->getMerchantId();
$account->getPrivateKey();
$account->getCertificate();
$account->getSecretKey();
$account->getV2SecretKey();
$account->getPlatformCerts();
```
