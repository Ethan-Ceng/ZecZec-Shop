# Access Token


我們一個 SDK 應用在初始化以後，你可以在任何時機從應用中拿到該配置下的 Access Token 例項：

```php
use EasyWeChat\Factory;

$config = [
    //...
];

$app = Factory::officialAccount($config);

// 獲取 access token 例項
$accessToken = $app->access_token;
$token = $accessToken->getToken(); // token 陣列  token['access_token'] 字串
$token = $accessToken->getToken(true); // 強制重新從微信伺服器獲取 token.
```

## 修改 `$app` 的 Access Token

```php
$app['access_token']->setToken($newAccessToken, 7200);
```

例如：

```php
$app['access_token']->setToken('ccfdec35bd7ba359f6101c2da321d675');
// 或者指定過期時間
$app['access_token']->setToken('ccfdec35bd7ba359f6101c2da321d675', 3600);  // 單位：秒
```
