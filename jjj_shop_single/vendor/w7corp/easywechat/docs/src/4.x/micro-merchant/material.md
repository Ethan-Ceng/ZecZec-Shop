# 商戶資訊修改
## 修改結算銀行卡

```php
$response = $app->material->setSettlementCard([
    // 'sub_mch_id' => '1230000109',
    'account_number' => '銀行卡號',
    'bank_name' => '開戶銀行全稱（含支行）',
    'account_bank' => '開戶銀行',
    'bank_address_code' => '開戶銀行省市編碼',
]);
```
## 修改聯絡資訊

```php
$response = $app->material->updateContact([
    // 'sub_mch_id' => '1230000109',
    'mobile_phone' => '手機號',
    'email' => '郵箱',
    'merchant_name' => '商戶簡稱',
]);
```

> 以上介面呼叫過 `setSubMchId` 方法則無需傳入 `sub_mch_id` 引數