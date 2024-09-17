# 代授權方實現業務

> 授權方已經把公眾號、小程式授權給你的開放平臺第三方平臺了，接下來的代授權方實現業務只需一行程式碼即可獲得授權方例項。

## 例項化

```php
use EasyWeChat\Factory;

$config = [
    // ...
];

$openPlatform = Factory::openPlatform($config);
```

### 獲取授權方例項

```php
// 代公眾號實現業務
$officialAccount = $openPlatform->officialAccount(string $appId, string $refreshToken);
// 代小程式實現業務
$miniProgram = $openPlatform->miniProgram(string $appId, string $refreshToken);
```

> $appId 為授權方公眾號 APPID，非開放平臺第三方平臺 APPID
>
> $refreshToken 為授權方的 refresh_token，可透過 [獲取授權方授權資訊](https://www.easywechat.com/docs/master/open-platform/index#heading-h2-2) 介面獲得。

### 幫助授權方管理開放平臺賬號

```php
// 代公眾號實現業務
$account = $officialAccount->account;
// 代小程式實現業務
$account = $miniProgram->account;

// 建立開放平臺賬號
// 並繫結公眾號或小程式
$result = $account->create();

// 將公眾號或小程式繫結到指定開放平臺帳號下
$result = $account->bindTo($openAppId);

// 將公眾號/小程式從開放平臺帳號下解綁
$result = $account->unbindFrom($openAppid);

// 獲取公眾號/小程式所繫結的開放平臺帳號
$result = $account->getBinding();
```

> 授權第三方平臺註冊的開放平臺帳號只可用於獲取使用者 unionid 實現使用者身份打通。
>
>  第三方平臺不可操作（包括繫結/解綁）透過 open.weixin.qq.com 線上流程註冊的開放平臺帳號。
>
>  公眾號只可將此許可權集授權給一個第三方平臺，授權互斥。

接下來的 API 呼叫等操作和公眾號、小程式的開發一致，請移步到[公眾號](https://www.easywechat.com/docs/master/official-account/index)或[小程式](https://www.easywechat.com/docs/master/mini-program/index)開發章節繼續進行開發吧。

### 程式碼示例

```php
// 假設你的公眾號訊息與事件接收 URL 為：https://easywechat.com/$APPID$/callback ...

Route::post('{appId}/callback', function ($appId) {
    // ...
    $officialAccount = $openPlatform->officialAccount($appId);
    $server = $officialAccount->server; // ❗️❗️  這裡的 server 為授權方的 server，而不是開放平臺的 server，請注意！！！

    $server->push(function () {
        return 'Welcome!';
    });

    return $server->serve();
});

// 呼叫授權方業務例子
Route::get('how-to-use', function () {
    $officialAccount = $openPlatform->officialAccount('已授權的公眾號 APPID', 'Refresh-token');
    // 獲取使用者列表：
    $officialAccount->user->list();

    $miniProgram = $openPlatform->miniProgram('已授權的小程式 APPID', 'Refresh-token');
    // 根據 code 獲取 session
    $miniProgram->auth->session('js-code');
    // 其他同理
});
```
