# 電子發票

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx',
    //...
];

$app = Factory::work($config);
```

## 查詢電子發票

https://work.weixin.qq.com/api/doc#11631

API:

```php
mixed get(string $cardId, string $encryptCode)
```

example:

```php
$app->invoice->get('CARDID', 'ENCRYPTCODE');
```

## 批次查詢電子發票

https://work.weixin.qq.com/api/doc#11974

API:

```php
mixed select(array $invoices)
```

> $invoices: 發票引數列表

example:

```php
$invoices = [
    ["card_id" => "CARDID1", "encrypt_code" => "ENCRYPTCODE1"],
    ["card_id" => "CARDID2", "encrypt_code" => "ENCRYPTCODE2"]
];

$app->invoice->select($invoices);
```

## 更新發票狀態

https://work.weixin.qq.com/api/doc#11633

API:

```php
mixed update(string $cardId, string $encryptCode, string $status)
```

> $status: 發報銷狀態
>
> > - INVOICE_REIMBURSE_INIT：發票初始狀態，未鎖定；
> > - INVOICE_REIMBURSE_LOCK：發票已鎖定，無法重複提交報銷;
> > - INVOICE_REIMBURSE_CLOSURE:發票已核銷，從使用者卡包中移除

## 批次更新發票狀態

https://work.weixin.qq.com/api/doc#11633

API:

```php
mixed batchUpdate(array $invoices, string $openid, string $status)
```

example:

```php
$invoices = [
    ["card_id" => "CARDID1", "encrypt_code" => "ENCRYPTCODE1"],
    ["card_id" => "CARDID2", "encrypt_code" => "ENCRYPTCODE2"]
];
$openid = 'oV-gpwSU3xlMXbq0PqqRp1xHu9O4';

$status = 'INVOICE_REIMBURSE_CLOSURE';

$app->invoice->batchUpdate($invoices, $openid, $status)
```
