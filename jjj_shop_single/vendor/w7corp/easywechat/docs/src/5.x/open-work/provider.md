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

### 通訊錄單個搜尋

```php
$app->provider->searchContact(
                         string $corpId, //查詢的企業corpid
                         string $queryWord, //搜尋關鍵詞。當查詢使用者時應為使用者名稱稱、名稱拼音或者英文名；當查詢部門時應為部門名稱或者部門名稱拼音
                         string $agentId, //授權方應用id
                         int $offset = 0, //查詢的偏移量，每次呼叫的offset在上一次offset基礎上加上limit
                         int $limit = 50, //查詢返回的最大數量，預設為50，最多為200，查詢返回的數量可能小於limit指定的值
                         int $queryType = 0, //查詢型別 1：查詢使用者，返回使用者userid列表 2：查詢部門，返回部門id列表。 不填該欄位或者填0代表同時查詢部門跟使用者
                         $fullMatchField = null //如果需要精確匹配使用者名稱稱或者部門名稱或者英文名，不填則預設為模糊匹配；1：匹配使用者名稱稱或者部門名稱 2：匹配使用者英文名
                     )
```
