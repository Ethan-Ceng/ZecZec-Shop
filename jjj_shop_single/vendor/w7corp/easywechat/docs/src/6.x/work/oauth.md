# OAuth

> 此文件為企業微信內部應用開發的網頁授權,非第三方應用網頁授權

[企業微信官方文件](https://work.weixin.qq.com/api/doc#90000/90135/91020)

建立例項：

```php
use EasyWeChat\work\Application;

$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx', // 應用的 secret
];

$app = new Application($config);
```

## 跳轉授權

```php
// $callbackUrl 為授權回撥地址
$callbackUrl = 'https://xxx.xxx'; // 需設定可信域名

// 返回授權跳轉連結
$redirectUrl = $app->getOAuth()->redirect($callbackUrl);
```

## 獲取授權使用者資訊

在回撥頁面中，你可以使用以下方式獲取授權者資訊：

```php
$code = "回撥URL中的code";
$user = $app->getOAuth()->detailed()->userFromCode($code);

// 獲取使用者資訊
$user->getId(); // 對應企業微信英文名（userid）
$user->getRaw(); // 獲取企業微信介面返回的原始資訊
```

:book: OAuth 詳情請參考：[網頁授權](../oauth.md)

獲取使用者其他資訊需呼叫通訊錄介面，參考：[企業微信通訊錄 API](https://github.com/EasyWeChat/docs/blob/master/wework/contacts.md)

## 參考閱讀

- 本模組基於 [overtrue/socialite](https://github.com/overtrue/socialite/) 實現，更多的使用請閱讀該擴充套件包文件。
- state 引數的使用: [overtrue/socialite/#state](https://github.com/overtrue/socialite/#state)
