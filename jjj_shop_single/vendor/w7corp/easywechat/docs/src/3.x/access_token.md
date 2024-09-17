# Access Token


SDK 中有一個 [Access Token](https://github.com/overtrue/wechat/blob/master/src/Core/AccessToken.php) 物件，它是一個全域性使用的東西，請把它與 OAuth 中的 code 換取的 Access Token 區別開。

我們一個 SDK 應用在初始化以後，你可以在任何時機從應用中拿到該配置下的 Access Token 例項：

```php
use EasyWeChat\Foundation\Application;

$options = [
    //...
];

$app = new Application($options);

// 獲取 access token 例項
$accessToken = $app->access_token; // EasyWeChat\Core\AccessToken 例項
$token = $accessToken->getToken(); // token 字串
$token = $accessToken->getToken(true); // 強制重新從微信伺服器獲取 token.
```

## 修改 `$app` 的 Access Token

```php
$app['access_token']->setToken($newAccessToken, $expires);
```

例如：

```php
$app['access_token']->setToken('ccfdec35bd7ba359f6101c2da321d675');
// 或者指定過期時間
$app['access_token']->setToken('ccfdec35bd7ba359f6101c2da321d675', 3600);  // 單位：秒
```

## 設定 AccessToken 的快取

你也可以自定義 token 的快取方式，把一個實現了 `Doctrine\Common\Cache\Cache` 快取介面的例項作為 AccessToken 建構函式的第三個引數傳入即可：

本專案使用 [doctrine/cache](https://github.com/doctrine/cache) 來完成快取工作，它支援幾乎目前所有的快取引擎。

以 Redis 為例：

```php

use Doctrine\Common\Cache\RedisCache; // RedisCache 例項了 `Doctrine\Common\Cache\Cache` 介面

$cache = new RedisCache();

// 建立 redis 例項
$redis = new Redis();
$redis->connect('redis_host', 6379);

$cache->setRedis($redis);

$app->access_token->setCache($cache);
```

