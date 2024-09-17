# 小微商戶

你在閱讀本文之前確認你已經仔細閱讀了：[微信小微商戶專屬介面文件](https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=19_2)。

## 配置

小微商戶整體介面呼叫方式相對於其他微信介面略有不同，配置時請勿填錯，相關配置如下：

```php
use EasyWeChat\Factory;

$config = [
    // 必要配置
    'mch_id'           => 'your-mch-id', // 服務商的商戶號
    'key'              => 'key-for-signature', // API 金鑰
    'apiv3_key'        => 'APIv3-key-for-signature', // APIv3 金鑰
    // API 證書路徑(登入商戶平臺下載 API 證書)
    'cert_path'        => 'path/to/your/cert.pem', // XXX: 絕對路徑！！！！
    'key_path'         => 'path/to/your/key', // XXX: 絕對路徑！！！！
    // 以下兩項配置在獲取證書介面時可為空，在呼叫入駐介面前請先呼叫獲取證書介面獲取以下兩項配置,如果獲取過證書可以直接在這裡配置，也可參照本文件獲取平臺證書章節中示例
    // 'serial_no'     => '獲取證書介面獲取到的平臺證書序列號',
    // 'certificate'   => '獲取證書介面獲取到的證書內容'
    
    // 以下為可選項
    // 指定 API 呼叫返回結果的型別：array(default)/collection/object/raw/自定義類名
    'response_type' => 'array'
    'appid'            => 'wx931386123456789e' // 服務商的公眾賬號 ID
];

$app = Factory::microMerchant($config);

```


`$app` 在所有相關小微商戶的文件都是指 `Factory::microMerchant` 得到的例項，就不在每個頁面單獨寫了。

## 使用時值得注意的地方：
1、小微商戶所有介面中以下列出引數 `version`, `mch_id`, `nonce_str`, `sign`, `sign_type`, `cert_sn` 可不用傳入。

2、所有敏感資訊無需手動加密，sdk會在呼叫介面前自動完成加密

3、在呼叫入駐等需要敏感資訊加密的介面前請先呼叫獲取證書介面然後把配置填入配置項

4、入駐成功獲取到子商戶號後需幫助子商戶呼叫配置修改等介面可以先呼叫以下方法，方便呼叫修改等介面時無需再次傳入子商戶號
```php
// $subMchId 為子商戶號
// $appid    服務商的公眾賬號 ID
$app->setSubMchId(string $subMchId, string $appId = '');
```
