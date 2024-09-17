# 紅包


在閱讀本文之前確認你已經仔細閱讀了：[微信支付 | 現金紅包文件 ](https://pay.weixin.qq.com/wiki/doc/api/tools/cash_coupon.php?chapter=13_1)。

## 配置

與支付介面一樣，紅包介面也需要配置如下引數，需要特別注意的是，紅包相關的全部介面**都需要使用 SSL 證書**，因此**cert_path 以及 cert_key 必須正確配置**。

```php
use EasyWeChat\Factory;

$config = [
    'app_id'    => 'you-app-id',
    'mch_id'    => 'your-mch-id',
    'key'       => 'key-for-signature',
    'cert_path' => 'path/to/your/cert.pem',
    'key_path'  => 'path/to/your/key',
    // ...
];

$payment = Factory::payment($config);

$redpack = $payment->redpack;
```

## 傳送紅包

微信的現金紅包分為**普通紅包**和**裂變紅包**兩類。SDK 中對其分別進行了封裝，同時也提供了一個統一的呼叫方法。

**預設情況下，透過介面傳送的紅包金額應該在200元以內，但可以透過在呼叫傳送介面時傳遞場景 ID (scene_id)來發送特定場景的紅包，不同場景紅包可以由商戶自己登入商戶平臺設定最大金額。scene_id 的可選值及對應含義可參閱微信支付官方文件。**


### 傳送普通紅包介面

```php
$redpackData = [
    'mch_billno'   => 'xy123456',
    'send_name'    => '測試紅包',
    're_openid'    => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',
    'total_num'    => 1,  //固定為1，可不傳
    'total_amount' => 100,  //單位為分，不小於100
    'wishing'      => '祝福語',
    'client_ip'    => '192.168.0.1',  //可不傳，不傳則由 SDK 取當前客戶端 IP
    'act_name'     => '測試活動',
    'remark'       => '測試備註',
    // ...
];

$result = $redpack->sendNormal($redpackData);
```

### 傳送裂變紅包介面

```php
$redpackData = [
    'mch_billno'   => 'xy123456',
    'send_name'    => '測試紅包',
    're_openid'    => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',
    'total_num'    => 3,  //不小於3
    'total_amount' => 300,  //單位為分，不小於300
    'wishing'      => '祝福語',
    'act_name'     => '測試活動',
    'remark'       => '測試備註',
    'amt_type'     => 'ALL_RAND',  //可不傳
    // ...
];

$result = $redpack->sendGroup($redpackData);
```

## 紅包預下單介面

紅包預下單介面是為搖一搖紅包介面配合使用的，在開發搖一搖周邊的搖紅包相關功能時，需要呼叫本介面獲取紅包單號。詳情參見[官方文件](http://mp.weixin.qq.com/wiki/7/0ddd50ed2421b99fedd071281c074aab.html#.E7.BA.A2.E5.8C.85.E9.A2.84.E4.B8.8B.E5.8D.95.E6.8E.A5.E5.8F.A3)


```php
$redpackData = [
    'hb_type'      => 'NORMAL',  //NORMAL 或 GROUP
    'mch_billno'   => 'xy123456',
    'send_name'    => '測試紅包',
    're_openid'    => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',
    'total_num'    => 1,  //普通紅包固定為1，裂變紅包不小於3
    'total_amount' => 100,  //單位為分，普通紅包不小於100，裂變紅包不小於300
    'wishing'      => '祝福語',
    'client_ip'    => '192.168.0.1',  //可不傳，不傳則由 SDK 取當前客戶端 IP
    'act_name'     => '測試活動',
    'remark'       => '測試備註',
    'amt_type'     => 'ALL_RAND',
    // ...
];

$result = $redpack->prepare($redpackData);
```

## 查詢紅包資訊

用於商戶對已發放的紅包進行查詢紅包的具體資訊以及領取情況 ，普通紅包和裂變包均使用這一介面進行查詢。

```php
$mchBillNo = "商戶系統內部的訂單號（mch_billno）";
$redpack->info($mchBillNo);
```
