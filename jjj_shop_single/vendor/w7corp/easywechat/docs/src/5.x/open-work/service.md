# 第三方應用介面

## 獲取應用suite_access_token

```php
$app->suite_access_token->getToken()
```

## 獲取預授權碼

```php
$app->corp->getPreAuthCode();
```

## 設定授權配置

```php
$app->corp->setSession(string $preAuthCode, array $sessionInfo);
```

## 獲取企業永久授權碼

```php
$app->corp->getPermanentByCode(string $preAuthCode); //傳入臨時授權碼
```

## 獲取企業授權資訊

```php
$app->corp->getAuthorization(string $authCorpId, string $permanentCode); //$authCorpId 授權的企業corp_id $permanentCode 授權的永久授權碼
```

## 獲取應用的管理員列表

```php
$app->corp->getManagers(string $authCorpId, string $agentId); //$authCorpId 授權的企業corp_id  $agentId 授權方安裝的應用agentid
```

##  網頁授權登入第三方

### 構造第三方oauth2連結

```php
//$redirectUri 回撥uri 這裡可以覆蓋 預設讀取配置檔案
//$scope 應用授權作用域。
//$state 自定義安全值
$app->corp->getOAuthRedirectUrl(string $redirectUri = '', string $scope = 'snsapi_userinfo', string $state = null); 
```

### 第三方根據code獲取企業成員資訊

```php
$app->corp->getUserByCode(string $code); 
```

### 第三方使用user_ticket獲取成員詳情

```php
$app->corp->getUserByTicket(string $userTicket); 
```
