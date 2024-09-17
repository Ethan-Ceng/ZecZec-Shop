# 微信小程式訊息解密

## 比如獲取電話等功能，資訊是加密的，需要解密。

API:

```php
$decryptedData = $app->encryptor->decryptData($session, $iv, $encryptedData);
```
