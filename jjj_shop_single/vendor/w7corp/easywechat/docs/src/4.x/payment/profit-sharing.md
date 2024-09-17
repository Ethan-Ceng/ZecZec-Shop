# 分賬
> 官方文件 https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=27_1&index=1

```php
use EasyWeChat\Factory;
$config = [
	'app_id'     => '***',
	"secret"     => "***",
	'mch_id'     => '***',
	'key'        => '***',
	'cert_path'  => 'cert.pem',
	'key_path'   => 'key.pem',
	'notify_url' => 'http://***.com/notify.php',
];
$payment = Factory::payment($config);
```

### 新增接收方

> 商戶發起新增分賬接收方請求，後續可透過發起分賬請求將結算後的錢分到該分賬接收方。

```php
$receiver = [
	"type"          => "PERSONAL_OPENID",
	"account"       => "…………",//PERSONAL_OPENID：個人openid
	"name"          => "張三",//接收方真實姓名
	"relation_type" => "PARTNER"
];
$payment->profit_sharing->addReceiver($receiver);
$receiver = [
	"type"          => "MERCHANT_ID",
	"account"       => "132456798",//MERCHANT_ID：商戶ID
	"name"          => "商戶全稱",//商戶全稱
	"relation_type" => "PARTNER"
];
$payment->profit_sharing->addReceiver($receiver);
```

### 刪除接收方

```php
$payment->profit_sharing->deleteReceiver($receiver);
```

### 單次分賬

```php
$transaction_id = "***";
$out_trade_no = "***";
$receivers = [
	[
		"type"        => "PERSONAL_OPENID",
		"account"     => "***",
		"amount"      => 2,
		"description" => "分到個人"
	],
	[
		"type"        => "MERCHANT_ID",
		"account"     => "***",
		"amount"      => 1,
		"description" => "分到商戶"
	]
];
$sharing = $payment->profit_sharing->share($transaction_id,$out_trade_no,$receivers);
```

### 多次分賬

```php
$payment->profit_sharing->multiShare($transaction_id,$out_trade_no,$receivers);
```

### 多次分賬完結

```php
$params = [
	"transaction_id" => "",
	"out_order_no"   => "",
	"description"    => ""
];
$payment->profit_sharing->markOrderAsFinished($params);
```

### 分賬查詢

```php
$res = $payment->profit_sharing->query($transaction_id,$out_trade_no);
```

> 查詢結果

```
Array
(
    [return_code] => SUCCESS
    [result_code] => SUCCESS
    [mch_id] => ***
    [nonce_str] => 38e92cbe2790642f
    [sign] => 8904B6440C58785540950F2911500F55C9A94CAC75790B0721B9AA470E6BF9A8
    [transaction_id] => 4200000589202007249764665257
    [out_order_no] => 202007241544057945
    [order_id] => 30000103702020072402011591464
    [status] => FINISHED
    [receivers] => [{"type":"MERCHANT_ID","account":"***","amount":7,"description":"解凍給分賬方","result":"SUCCESS","finish_time":"20200724172033"},{"type":"PERSONAL_OPENID","account":"***","amount":2,"description":"分到個人1","result":"SUCCESS","finish_time":"20200724172033"},{"type":"PERSONAL_OPENID","account":"***-g4","amount":1,"description":"分到郭","result":"SUCCESS","finish_time":"20200724172034"}]
)
```

### 分賬退回

```php
$out_trade_no = "***";//退款訂單號
$out_return_no = "***";//系統內部退款單號
$return_amount = 1;
$return_account = "***-g4";
$description = "訂單取消";
$payment->profit_sharing->returnShare($out_trade_no,$out_return_no,$return_amount,$return_account,$description);
```
