# 安全與風控

> EasyWeChat 4.0.7+

## 獲取 RSA 公鑰

```php
$result = $app->security->getPublicKey();

// 存成檔案

file_put_contents('./public.pem', $result);
```

將會得到 PKCS#1 格式金鑰：

```
-----BEGIN RSA PUBLIC KEY-----
MIIBCgKCAQEArT82k67xybiJS9AD8nNAeuDYdrtCRaxkS6cgs8L9h83eqlDTlrdw
zBVSv5V4imTq/URbXn4K0V/KJ1TwDrqOI8hamGB0fvU13WW1NcJuv41RnJVua0QA
lS3tS1JzOZpMS9BEGeFvyFF/epbi/m9+2kUWG94FccArNnBtBqqvFncXgQsm98JB
3a62NbS1ePP/hMI7Kkz+JNMyYsWkrOUFDCXAbSZkWBJekY4nGZtK1erqGRve8Jbx
TWirAm/s08rUrjOuZFA21/EI2nea3DidJMTVnXVPY2qcAjF+595shwUKyTjKB8v1
REPB3hPF1Z75O6LwuLfyPiCrCTmVoyfqjwIDAQAB
-----END RSA PUBLIC KEY-----
```

使用 OpenSSL 轉換 PKCS#1 為 PKCS#8 格式金鑰：

```shell
openssl rsa -RSAPublicKey_in -in public.pem -out public.pem
```

PKCS#8 格式金鑰：

```
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArT82k67xybiJS9AD8nNA
euDYdrtCRaxkS6cgs8L9h83eqlDTlrdwzBVSv5V4imTq/URbXn4K0V/KJ1TwDrqO
I8hamGB0fvU13WW1NcJuv41RnJVua0QAlS3tS1JzOZpMS9BEGeFvyFF/epbi/m9+
lkUWG94FccArNnBtBqqvFncXgQsm98JB3a42NbS1ePP/hMI7Kkz+JNMyYsWkrOUF
DCXAbSZkWBJekY4nGZtK1erqGRve8JbxTWirAm/s08rUrjOuZFA21/EI2nea3Did
JMTVnXVPY2qcAjF+595shwUKyTjKB8v1REPB3hPF1Z75O6LwuLfyPiCrCTmVoyfq
jwIDAQAB
-----END PUBLIC KEY-----
```
