# OAuth

第三方服務商網頁授權有兩種：

- [第三方應用網頁授權](https://open.work.weixin.qq.com/api/doc/90001/90143/91120#%E6%9E%84%E9%80%A0%E7%AC%AC%E4%B8%89%E6%96%B9%E5%BA%94%E7%94%A8oauth2%E9%93%BE%E6%8E%A5)
- [企業網頁授權](https://open.work.weixin.qq.com/api/doc/90001/90143/91120#%E6%9E%84%E9%80%A0%E4%BC%81%E4%B8%9Aoauth2%E9%93%BE%E6%8E%A5)

建立例項：

```php
use EasyWeChat\work\Application;

$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx', // 應用的 secret
];

$app = new Application($config);
```

## 獲取 OAuth 模組例項

請根據你的場景選擇對應的方法獲取 OAuth 例項：

```php
// 第三方應用網頁授權
$oauth = $app->getOAuth(string $suiteId, AccessTokenInterface $suiteAccessToken);

// 企業網頁授權
$oauth = $app->getCorpOAuth(string $corpId, AccessTokenInterface $suiteAccessToken);
// 如需指定應用ID
$oauth = $oauth->withAgentId($agentId);
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
