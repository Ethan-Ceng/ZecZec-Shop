# 服務商相關介面

## 單點登入


### 獲取從第三方單點登入連線

```php
$app->provider->getLoginUrl(string $redirectUri = '', string $userType = 'admin', string $state = ''); //$redirectUri 回撥地址  $userType支援登入的型別
```

### 獲取登入使用者資訊

```php
$app->provider->getLoginInfo(string $authCode); //$authCode oauth2.0授權企業微信管理員登入產生的code，最長為512位元組。只能使用一次，5分鐘未被使用自動過期
```

## 註冊定製化 

### 獲取註冊碼

```php
$app->provider->getRegisterCode(
                        string $corpName = '', //企業名稱
                        string $adminName = '',//管理員姓名
                        string $adminMobile = '',//管理員手機號
                        string $state = ''//自定義的狀態值
                    ); 
```

### 獲取註冊Uri

```php
$app->provider->getRegisterUri(string $registerCode = ''); //$registerCode 註冊碼
```

### 查詢註冊狀態

```php
$app->provider->getRegisterInfo(string $registerCode); //$registerCode 註冊碼
```

### 設定授權應用可見範圍

```php
$app->provider->setAgentScope(
                        string $accessToken, //查詢註冊狀態介面返回的access_token
                        string $agentId, //	授權方應用id
                        array $allowUser = [], //應用可見範圍（成員）若未填該欄位，則清空可見範圍中成員列表
                        array $allowParty = [], //	應用可見範圍（部門）若未填該欄位，則清空可見範圍中部門列表
                        array $allowTag = [] //應用可見範圍（標籤）若未填該欄位，則清空可見範圍中標籤列表
                    )
```

### 設定通訊錄同步完成

```php
$app->provider->contactSyncSuccess(string $accessToken); //$accessToken //查詢註冊狀態介面返回的access_token
```
