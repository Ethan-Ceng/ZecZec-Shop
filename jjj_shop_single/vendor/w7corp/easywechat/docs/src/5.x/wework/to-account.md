# 企微ID賬號升級轉換

:book:   [官方文件 - 企業微信帳號ID安全性全面升級 說明文件](https://open.work.weixin.qq.com/api/doc/90001/90143/95327)

> 注意: 以下介面僅限第三方服務商呼叫

```php
$config = [...];

$app = Factory::openWork($config);
$work = $app->work('授權企業的corp_id','授權企業的永久授權碼');
```

### corpid轉換

```php
$work->corp_group->getOpenCorpid(string $corpId);
```

### userid轉換

```php
$work->corp_group->batchUseridToOpenUserid(array $useridList);
```

### external_userid轉換

```php
$work->external_contact->getNewExternalUserid(array $externalUserIds);
```

### 設定遷移完成

```php
$work->external_contact->finishExternalUseridMigration(string $corpId);
```

### unionid查詢external_userid

```php
$work->external_contact->unionidToexternalUserid3rd(string $unionid, string $openid, string $corpid = '');
```