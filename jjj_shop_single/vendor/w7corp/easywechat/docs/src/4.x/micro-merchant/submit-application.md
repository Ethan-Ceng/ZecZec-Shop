# 商戶入駐
## 申請入駐

使用申請入駐介面提交你的小微商戶資料。

```php
$result = $app->submitApplication([
    'business_code' => '123456', // 業務申請編號
    'id_card_copy'  => 'media_id', // 身份證人像面照片
    // ...
    // 引數太多就不一一列出，自行根據 (小微商戶專屬文件 -> 申請入駐api) 填寫
]);
```

## 查詢申請狀態

使用申請入駐介面提交小微商戶資料後，一般5分鐘左右可以透過該查詢介面查詢具體的申請結果。

```php
$applymentId = '商戶申請單號(applyment_id 申請入駐介面返回)';
$businessCode = '業務申請編號(business_code)';
$app->getStatus(string $applymentId, string $businessCode = '');
```
> 商戶申請單號和業務申請編號填寫一個就行了，當 `applyment_id` 已填寫時，`business_code` 欄位無效。

當查詢申請狀態為待簽約，介面會一併返回簽約二維碼，服務商需引導商戶使用本人微信掃碼完成協議簽署。
