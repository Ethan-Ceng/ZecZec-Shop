# 企業互聯

### 獲取應用共享資訊

```php
$agentId = 100001;

$app->corp_group->getAppShareInfo(int $agentId);
```

### 獲取下級企業的access_token

```php
$corpId = 'wwd216fa8c4c5c0e7x';
$agentId = 100001;

$app->corp_group->getToken(string $corpId, int $agentId)
```

### 獲取下級企業的小程式session


```php
$userId = 'wmAoNVCwAAUrSqEqz7oQpEIEMVWDrPeg';
$sessionKey = 'n8cnNEoyW1pxSRz6/Lwjwg==';

$app->corp_group->getMiniProgramTransferSession(string $userId, string $sessionKey);
```
