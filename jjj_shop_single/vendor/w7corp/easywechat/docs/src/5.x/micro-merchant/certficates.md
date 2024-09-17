# 獲取平臺證書
呼叫獲取平臺證書介面之前，請前往微信支付商戶平臺升級API證書，升級後才可成功呼叫本介面。

```php
// 獲取到證書後可以做快取處理，無需每次重新獲取
$response = $app->certficates->get(bool $returnRaw = false);

// 獲取到平臺證書後，可以直接使用 setCertificate 方法把證書配置追加到配置項裡面去
$app->setCertificate(string $certificate, string $serialNo);
```
> $returnRaw 不填預設為false時，請確保你的PHP已安裝了sodium擴充套件    
> 返回值：固定array格式的解密後的證書資訊

> $returnRaw 傳入true時     
> 返回值：Response物件`$response->getBody()->getContents();`獲取到微信返回xml原始資料
