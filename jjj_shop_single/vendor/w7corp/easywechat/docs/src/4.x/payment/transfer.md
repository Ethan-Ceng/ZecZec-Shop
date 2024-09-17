# 企業付款

> EasyWeChat 4.0.7+

該模組需要用到雙向證書，請參考：https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=4_3

## 企業付款到使用者零錢

```php
$app->transfer->toBalance([
    'partner_trade_no' => '1233455', // 商戶訂單號，需保持唯一性(只能是字母或者數字，不能包含有符號)
    'openid' => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',
    'check_name' => 'FORCE_CHECK', // NO_CHECK：不校驗真實姓名, FORCE_CHECK：強校驗真實姓名
    're_user_name' => '王小帥', // 如果 check_name 設定為FORCE_CHECK，則必填使用者真實姓名
    'amount' => 10000, // 企業付款金額，單位為分
    'desc' => '理賠', // 企業付款操作說明資訊。必填
]);
```

## 查詢付款到零錢的訂單

```php
$partnerTradeNo = 1233455;
$app->transfer->queryBalanceOrder($partnerTradeNo);
```


## 企業付款到銀行卡

企業付款到銀行卡需要對銀行卡號與姓名進行 RSA 加密，所以這裡需要先下載 RSA 公鑰到本地（伺服器），我們提供了一個命令列工具：[EasyWeChat/console](https://github.com/EasyWeChat/console)，請使用 composer 安裝完成。

```bash
$ composer require easywechat/console -vvv
```

然後，在專案根目錄執行以下命令下載公鑰：

```bash
$ ./vendor/bin/easywechat payment:rsa_public_key \
  >  --mch_id=14339221228 \
  >  --api_key=36YTbDmLgyQ52noqdxgwGiYy \
  >  --cert_path=/Users/overtrue/www/demo/apiclient_cert.pem \
  >  --key_path=/Users/overtrue/www/demo/apiclient_key.pem
```

將會在當前目錄生成一個 `./public-14339221228.pem` 檔案，你可以將它移動到敏感目錄，然後在支付配置檔案中加如以下選項：

```php
use EasyWeChat\Factory;

$config = [
    // 必要配置
    'app_id'             => 'xxxx',
    'mch_id'             => 'your-mch-id',
    'key'                => 'key-for-signature',   // API 金鑰

    // 如需使用敏感介面（如退款、傳送紅包等）需要配置 API 證書路徑(登入商戶平臺下載 API 證書)
    'cert_path'          => '/path/to/your/cert.pem', // XXX: 絕對路徑！！！！
    'key_path'           => '/path/to/your/key',      // XXX: 絕對路徑！！！！

    // 將上面得到的公鑰存放路徑填寫在這裡
    'rsa_public_key_path' => '/path/to/your/rsa/publick/key/public-14339221228.pem', // <<<------------------------

    'notify_url'         => '預設的訂單回撥地址',     // 你也可以在下單時單獨設定來想覆蓋它
];

$app = Factory::payment($config);
```

```php
$result = $app->transfer->toBankCard([
    'partner_trade_no' => '1229222022',
    'enc_bank_no' => '6214830901234564', // 銀行卡號
    'enc_true_name' => '安正超',   // 銀行卡對應的使用者真實姓名
    'bank_code' => '1001', // 銀行編號
    'amount' => 100,  // 單位：分
    'desc' => '測試',
]);

```

## 查詢付款到銀行卡的訂單

```php
$partnerTradeNo = 1233455;
$app->transfer->queryBankCardOrder($partnerTradeNo);
```

