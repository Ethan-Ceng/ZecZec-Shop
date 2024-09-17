# 網頁授權

## 關於 OAuth2.0

OAuth 是一個關於授權（authorization）的開放網路標準，在全世界得到廣泛應用，目前的版本是 2.0 版。

<img src="https://user-images.githubusercontent.com/1472352/29310178-5a7a91cc-81df-11e7-9468-b66e150bfba1.png" alt="" style="max-width: 500px">

> 摘自：[RFC 6749](https://datatracker.ietf.org/doc/rfc6749/?include_text=1)

步驟解釋：

    （A）使用者開啟客戶端以後，客戶端要求使用者給予授權。
    （B）使用者同意給予客戶端授權。
    （C）客戶端使用上一步獲得的授權，向認證伺服器申請令牌。
    （D）認證伺服器對客戶端進行認證以後，確認無誤，同意發放令牌。
    （E）客戶端使用令牌，向資源伺服器申請獲取資源。
    （F）資源伺服器確認令牌無誤，同意向客戶端開放資源。

關於 OAuth 協議我們就簡單瞭解到這裡，如果還有不熟悉的同學，請 [Google 相關資料](https://www.google.com.hk/?gws_rd=ssl#safe=strict&q=OAuth2)

## 微信 OAuth

在微信裡的 OAuth 其實有兩種：[公眾平臺網頁授權獲取使用者資訊](http://mp.weixin.qq.com/wiki/9/01f711493b5a02f24b04365ac5d8fd95.html)、[開放平臺網頁登入](https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316505&token=&lang=zh_CN)。

它們的區別有兩處，授權地址不同，`scope` 不同。

> - **公眾平臺網頁授權獲取使用者資訊**

    **授權 URL**: `https://open.weixin.qq.com/connect/oauth2/authorize`
    **Scopes**: `snsapi_base` 與 `snsapi_userinfo`

> - **開放平臺網頁登入**

    **授權 URL**: `https://open.weixin.qq.com/connect/qrconnect`
    **Scopes**: `snsapi_login`

他們的邏輯都一樣：

1. 使用者嘗試訪問一個我們的業務頁面，例如: `/user/profile`
2. 如果使用者已經登入，則正常顯示該頁面
3. 系統檢查當前訪問的使用者並未登入（從 session 或者其它方式檢查），則跳轉到**跳轉到微信授權伺服器**（上面的兩種中一種**授權 URL** ），並告知微信授權伺服器我的**回撥 URL（redirect_uri=callback.php)**，此時使用者看到藍色的授權確認頁面（`scope` 為 `snsapi_base` 時不顯示）
4. 使用者點選確定完成授權，瀏覽器跳轉到**回撥 URL**: `callback.php` 並帶上 `code`： `?code=CODE&state=STATE`。
5. 在 `callback.php` 中得到 `code` 後，透過 `code` 再次向微信伺服器請求得到 **網頁授權 access_token** 與 `openid`
6. 你可以選擇拿 `openid` 去請求 API 得到使用者資訊（可選）
7. 將使用者資訊寫入 SESSION。
8. 跳轉到第 3 步寫入的 `target_url` 頁面（`/user/profile`）。

> 看懵了？沒事，使用 SDK，你不用管這麼多。:smile:
>
> 注意，上面的第 3 步：redirect_uri=callback.php 實際上我們會在 `callback.php` 後面還會帶上授權目標頁面 `user/profile`，所以完整的 `redirect_uri` 應該是下面的這樣的 PHP 去拼出來：`'redirect_uri='.urlencode('callback.php?target=user/profile')`
> 結果：redirect_uri=callback.php%3Ftarget%3Duser%2Fprofile

## 邏輯組成

從上面我們所描述的授權流程來看，我們至少有 3 個頁面：

1. **業務頁面**，也就是需要授權才能訪問的頁面。
2. **發起授權頁**，此頁面其實可以省略，可以做成一箇中間件，全域性檢查未登入就發起授權。
3. **授權回撥頁**，接收使用者授權後的狀態，並獲取使用者資訊，寫入使用者會話狀態（SESSION）。

## 開始之前

在開始之前請一定要記住，先登入公眾號後臺，找到**邊欄 “開發”** 模組下的 **“介面許可權”**，點選 **“網頁授權獲取使用者基本資訊”** 後面的修改，新增你的網頁授權域名。

> 如果你的授權地址為：`http://www.abc.com/xxxxx`，那麼請填寫 `www.abc.com`，也就是說請填寫與網址匹配的域名，前者如果填寫 `abc.com` 是通過不了的。

## SDK 中 OAuth 模組的 API

在 SDK 中，我們使用名稱為 `oauth` 的模組來完成授權服務，我們主要用到以下兩個 API：

### 發起授權

```php
// $redirectUrl 為跳轉目標，請自行 302 跳轉到目標地址
$redirectUrl = $app->oauth->scopes(['snsapi_userinfo'])
                          ->redirect();
```

當然你也可以在發起授權的時候指定回撥 URL，比如設定回撥 URL 為當前頁面：

```php
$redirectUrl = $app->oauth->scopes(['snsapi_userinfo'])
                          ->redirect($request->fullUrl());
```

它的返回值 `$redirectUrl` 是一個字串跳轉地址，請自行使用框架的跳轉方法實現跳轉，PHP 原生寫法：

```php
header("Location: {$redirectUrl}");
```

在 [Laravel](http://laravel.com) 框架中控制器方法是要求返回響應值的，那麼你就直接:

```php
return \redirect($redirectUrl);
```

### 獲取已授權使用者

```php
$code = "微信回撥URL攜帶的 code";
$user = $app->oauth->userFromCode($code);
```

返回的 `$user` 是 [Overtrue\Socialite\User](https://github.com/overtrue/socialite/blob/master/src/User.php) 物件，你可以從該物件拿到[更多的資訊](https://github.com/overtrue/socialite#user-interface)。

#### $user 可以用的方法:

- `$user->getId(); ` 對應微信的 `openid`
- `$user->getNickname(); ` 對應微信的 `nickname`
- `$user->getName(); ` 對應微信的 `nickname`
- `$user->getAvatar(); ` 頭像地址
- `$user->getRaw(); ` 原始 API 返回的結果
- `$user->getAccessToken(); ` `access_token`
- `$user->getRefreshToken(); ` `refresh_token`
- `$user->getExpiresIn(); ` `expires_in`，Access Token 過期時間
- `$user->getTokenResponse(); ` 返回 `access_token` 時的響應值

> r`裡沒有`openid`， `$user->id` 便是 `openid`.
> 如果你想拿微信返回給你的原樣的全部資訊，請使用：$user->getRaw();

當 `scope` 為 `snsapi_base` 時 `$oauth->userFromCode($code);` 物件裡只有 `id`，沒有其它資訊。

## 網頁授權例項

我們這裡來用原生 PHP 寫法舉個例子，`oauth_callback` 是我們的授權回撥 URL (未 urlencode 編碼的 URL), `user/profile` 是我們需要授權才能訪問的頁面，它的 PHP 程式碼如下：

```php
// http://easywechat.org/user/profile
<?php

use EasyWeChat\Factory;

$config = [
  // ...
  'oauth' => [
      'scopes'   => ['snsapi_userinfo'],
      'callback' => '/oauth_callback',
  ],
  // ..
];

$app = Factory::officialAccount($config);
$oauth = $app->oauth;

// 未登入
if (empty($_SESSION['wechat_user'])) {

  $_SESSION['target_url'] = 'user/profile';

  $redirectUrl = $oauth->redirect();

  header("Location: {$redirectUrl}");
  exit;
}

// 已經登入過
$user = $_SESSION['wechat_user'];

// ...

```

授權回撥頁：

```php
// http://easywechat.org/oauth_callback
<?php

use EasyWeChat\Factory;

$config = [
  // ...
];

$app = Factory::officialAccount($config);
$oauth = $app->oauth;

// 獲取 OAuth 授權結果使用者資訊
$code = "微信回撥URL攜帶的 code";
$user = $oauth->userFromCode($code);

$_SESSION['wechat_user'] = $user->toArray();

$targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];

header('Location:'. $targetUrl); // 跳轉到 user/profile
```

上面的例子呢都是基於 `$_SESSION` 來保持會話的，在微信客戶端中，你可以結合 Cookies 來儲存，但是有效期平臺不一樣時間也不一樣，好像 Android 的失效會快一些，不過基本也夠用了。

## 參考閱讀

- 本模組基於 [overtrue/socialite](https://github.com/overtrue/socialite/) 實現，更多的使用請閱讀該擴充套件包文件。
- state 引數的使用: [overtrue/socialite/#state](https://github.com/overtrue/socialite/#state)
