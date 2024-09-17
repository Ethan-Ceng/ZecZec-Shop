# 快速開始

在我們已經安裝完成後，即可很快的開始使用它了，當然你還是有必要明白 PHP 基本知識，如名稱空間等，我這裡就不贅述了。

我們以完成伺服器端驗證與接收響應使用者傳送的訊息為例來演示,首先你有必要了解一下微信互動的執行流程：

```
                                 +-----------------+                       +---------------+
+----------+                     |                 |    POST/GET/PUT       |               |
|          | ------------------> |                 | ------------------->  |               |
|   user   |                     |  wechat server  |                       |  your server  |
|          | < - - - - - - - - - |                 |                       |               |
+----------+                     |                 | <- - - - - - - - - -  |               |
                                 +-----------------+                       +---------------+

```

那麼我們要做的就是圖中 **微信伺服器把使用者訊息轉到我們的自有伺服器（虛線返回部分）** 後的處理過程。

## 服務端驗證

在微信接入開始有一個 “伺服器驗證” 的過程，這一步呢，其實就是微信伺服器向我們伺服器發起一個請求（上圖實線部分），傳了一個名稱為 `echostr` 的字串過來，我們只需要原樣返回就好了。

你也知道，微信後臺只能填寫一個伺服器地址，所以 **伺服器驗證** 與 **訊息的接收與回覆**，都在這一個連結內完成互動。

考慮到這些，我已經把驗證這一步給封裝到 SDK 裡了，你可以完全忽略這一步。

下面我們來配置一個基本的服務端，這裡假設我們自己的伺服器域名叫 `easywechat.com`，我們在伺服器上準備這麼一個檔案`server.php`:

// server.php

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'wx3cf0f39249eb0xxx',
    'secret' => 'f1c242f4f28f735d4687abb469072xxx',
    'token' => 'TestToken',
    'response_type' => 'array',
    //...
];

$app = Factory::officialAccount($config);

$response = $app->server->serve();

// 將響應輸出
$response->send();exit; // Laravel 裡請使用：return $response;

```

> :heart: 安全模式下請一定要配置 `aes_key`

一個服務端帶驗證功能的程式碼已經完成，當然沒有對訊息做處理，彆著急，後面我們再講。

我們先來分析上面的程式碼：

```php
// 引入我們的主專案工廠類。
use EasyWeChat\Factory;

// 一些配置
$config = [...];

// 使用配置來初始化一個公眾號應用例項。
$app = Factory::officialAccount($config);

$response = $app->server->serve();

// 將響應輸出
$response->send(); exit; // Laravel 裡請使用：return $response;
```

最後這一行我有必要詳細講一下：

> 1.  我們的 `$app->server->serve()` 就是執行服務端業務了，那麼它的返回值是一個 `Symfony\Component\HttpFoundation\Response` 例項。
> 2.  我這裡是直接呼叫了它的 `send()` 方法，它就是直接輸出（echo）了，我們在一些框架就不能直接輸出了，那你就直接拿到 Response 例項後做相應的操作即可，比如 Laravel 裡你就可以直接 `return $app->server->serve();`

OK, 有了上面的程式碼，那麼請你按 **[微信官方的接入指引](http://mp.weixin.qq.com/wiki/)** 在公眾號後臺完成配置並啟用，並相應修改上面的 `$config` 的相關配置。

> URL 就是我們的 `http://easywechat.com/server.php`，這裡我是舉例哦，你可不要填寫我的域名。

這樣，點選提交驗證就 OK 了。

> :heart: 請一定要將微信後臺的開發者模式 “**啟用**” ！！！！！！看到紅色 “**停用**” 才真正的是啟用了。
> 最後，請不要用瀏覽器訪問這個地址，它是給微信伺服器訪問的，不是給人訪問的。

## 接收 & 回覆使用者訊息

那服務端驗證通過了，我們就來試一下接收訊息吧。

> 在剛剛上面程式碼最後一行 `$app->server->serve()->send();` 前面，我們呼叫 `$app->server` 的 `push()` 方法來註冊一個訊息處理器，這裡用到了 **[PHP 閉包](http://php.net/manual/zh/functions.anonymous.php)** 的知識，如果你不熟悉趕緊補課去。

```php
// ...

$app->server->push(function ($message) {
    return "您好！歡迎使用 EasyWeChat!";
});

$response = $app->server->serve();

// 將響應輸出
$response->send(); // Laravel 裡請使用：return $response;

```

> 注意：send() 方法裡已經包含 echo 了，請不要再加 echo 在前面。

好吧，開啟你的微信客戶端，向你的公眾號傳送任意一條訊息，你應該會收到回覆：`您好！歡迎使用 EasyWeChat!`。

> 到了“你的公眾號暫時無法提供服務” ？， 好，那檢查一下你的日誌吧，日誌在哪兒？我們的配置裡寫了日誌路徑了(`__DIR__.'/wechat.log'`)。 沒有這個檔案？看看許可權哦。

> avel 框架應用時，因 POST 請求預設會有 CSRF 驗證，所以需要在 `App\Http\Middleware\VerifyCsrfToken` 的 `except` 陣列中新增微信請求，否則會提示“你的公眾號暫時無法提供服務”。

一個基本的服務端驗證就完成了。

## 總結

1. 所有的應用服務都透過主入口 `EasyWeChat\Factory` 類來建立：

```php

// 公眾號
$app = Factory::officialAccount($config);

// 小程式
$app = Factory::miniProgram($config);

// 開放平臺
$app = Factory::openPlatform($config);

// 企業微信
$app = Factory::work($config);

// 企業微信開放平臺
$app = Factory::openWork($config);

// 微信支付
$app = Factory::payment($config);

```

## 最後

希望你在使用本 SDK 的時候如果你發現 SDK 的不足，歡迎提交 PR 或者給我[提建議 & 報告問題](https://github.com/overtrue/wechat/issues)。
