# 微信開放平臺第三方平臺

此頁涉及介面資訊與說明請參見：[授權流程技術說明 - 官方文件](https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=&lang=)

# 微信開放平臺第三方平臺

## 例項化

```php
<?php
use EasyWeChat\Factory;

$config = [
  'app_id'   => '開放平臺第三方平臺 APPID',
  'secret'   => '開放平臺第三方平臺 Secret',
  'token'    => '開放平臺第三方平臺 Token',
  'aes_key'  => '開放平臺第三方平臺 AES Key'
];

$openPlatform = Factory::openPlatform($config);
```

## 獲取使用者授權頁 URL

```php
$openPlatform->getPreAuthorizationUrl('https://easywechat.com/callback'); // 傳入回撥URI即可
```

## 使用授權碼換取介面呼叫憑據和授權資訊

在使用者在授權頁授權流程完成後，授權頁會自動跳轉進入回撥URI，並在URL引數中返回授權碼和過期時間，如：(https://easywechat.com/callback?auth_code=xxx&expires_in=600)

```php
$openPlatform->handleAuthorize(string $authCode = null);
```

> $authCode 不傳的時候會獲取 url 中的 auth_code 引數值

## 獲取授權方的帳號基本資訊

```php
$openPlatform->getAuthorizer(string $appId);
```

## 獲取授權方的選項設定資訊

```php
$openPlatform->getAuthorizerOption(string $appId, string $name);
```

## 設定授權方的選項資訊

```php
$openPlatform->setAuthorizerOption(string $appId, string $name, string $value);
```

> 該API用於獲取授權方的公眾號或小程式的選項設定資訊，如：地理位置上報，語音識別開關，多客服開關。注意，獲取各項選項設定資訊，需要有授權方的授權，詳見許可權集說明。


## 獲取已授權的授權方列表

```php
$openPlatform->getAuthorizers(int $offset = 0, int $count = 500)
```
