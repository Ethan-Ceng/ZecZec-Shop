# 示例

> 👏🏻 歡迎點選本頁下方 "幫助我們改善此頁面！" 連結參與貢獻更多的使用示例！

<details>
  <summary>Laravel 開放平臺處理推送訊息</summary>

> 注意：對應路由需要關閉 csrf 驗證。

假設你的開放平臺第三方平臺設定的授權事件接收 URL 為: https://easywechat.com/open-platform （其他事件推送同樣會推送到這個 URL）

```php
// routes/web.php
Route::post('open-platform', function () {
    // $app 為你例項化的開放平臺物件，此處省略例項化步驟
    return $app->server->serve(); // Done!
});

// 處理授權事件
Route::post('open-platform', function () {
    $server = $app->getServer();

    // 處理授權成功事件，其他事件同理
    $server->handleAuthorized(function ($message) {
        // $message 為微信推送的通知內容，不同事件不同內容，詳看微信官方文件
        // 獲取授權公眾號 AppId： $message['AuthorizerAppid']
        // 獲取 AuthCode：$message['AuthorizationCode']
        // 然後進行業務處理，如存資料庫等...
    });

    return $server->serve();
});
```
</details>


<details>
    <summary>Laravel Octane(swoole) 開放平臺處理推送訊息</summary>

```php
// routes/web.php

use EasyWeChat\OpenPlatform\Application;

// 授權事件回撥地址：http://yourdomain.com/open-platform/server
Route::post('open-platform/server', function () {
        $config = config('wechatv6.open_platform');
        $app = new Application($config);

        // 相容octane
        $app->setRequestFromSymfonyRequest(request());

        $server = $app->getServer();
        return $server->serve();
});
```
</details>

<details>
    <summary>webman 開放平臺處理推送訊息</summary>

```php
namespace app\controller;

use EasyWeChat\OpenPlatform\Application;
use support\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

// 授權事件回撥地址：http://yourdomain.com/openPlatform/server

class OpenPlatform
{
    public function server(Request $request)
    {
        $config = config('wechatv6.open_platform');
        $app = new Application($config);
        $symfony_request = new SymfonyRequest($request->get(), $request->post(), [], $request->cookie(), [], [], $request->rawBody());
        $symfony_request->headers = new HeaderBag($request->header());
        $app->setRequestFromSymfonyRequest($symfony_request);
        $server = $app->getServer();
        $response = $server->serve();
        return $response->getBody()->getContents();
    }
}
```
</details>


<details>
  <summary>Laravel 開放平臺PC版預授權<version-tag>6.3.0+</version-tag></summary>

官方文件： https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Before_Develop/Authorization_Process_Technical_Description.html

用例：

```php
// routes/web.php

// 授權落地頁
Route::any('open-platform/auth', function(){
        $auth_code = request()->get('auth_code');
        // 完成授權寫入資料庫的邏輯省略。。。
})->name('open_platform.auth');

// 授權跳轉頁
Route::any('open-platform/preauth', function(){
      // $app 為你例項化的開放平臺物件，此處省略例項化步驟
      $options=[
            //1 表示手機端僅展示公眾號；2 表示僅展示小程式，3 表示公眾號和小程式都展示。如果為未指定，則預設小程式和公眾號都展示。
            // 'auth_type' => '',

            // 指定的許可權集id列表，如果不指定，則預設拉取當前第三方賬號已經全網釋出的許可權集列表。
            // 'category_id_list' => '',
      ];

      $url = $app->createPreAuthorizationUrl(route('open_platform.auth'), $options);

      return response("<script>window.location.href='$url';</script>")->header('Content-Type', 'text/html');
});
```

</details>

<details>
  <summary>Laravel 開放平臺代公眾號/小程式代呼叫示例<version-tag>6.3.0+</version-tag></summary>

路由配置：

```php
// routes/web.php
// 例如：https://yourdomain.com/open-platform/miniapp/get-phone-number/wx123212312313abc

Route::any('open-platform/miniapp/get-phone-number/{appid}', 'OpenPlatformController@getPhoneNumber');
Route::any('open-platform/officialAccount/get-user-list/{appid}', 'OpenPlatformController@getUsers');
```

對應控制器：`app/Http/Controllers/OpenPlatformController`：

```php
use App\Http\Controllers\Controller;

class OpenPlatformController extends Controller
{
    public function mini(string $appid): \EasyWeChat\MiniApp\Application
    {
        $refreshToken = '授權後在快取或資料庫獲取';

        // $app 為你例項化的開放平臺物件，此處省略例項化步驟
        return $app->getMiniAppWithRefreshToken($appid, $refreshToken);
    }

    public function officialAccount(string $appid): \EasyWeChat\OfficialAccount\Application
    {
        $refreshToken = '授權後在快取或資料庫獲取';

        // $app 為你例項化的開放平臺物件，此處省略例項化步驟
        return $app->getOfficialAccountWithRefreshToken($appid, $refreshToken);
    }

    public function getUsers(string $appid)
    {
        return $this->officialAccount($appid)
                    ->getClient()
                    ->get('cgi-bin/users/list')
                    ->toArray();
    }

    public function getPhoneNumber(string $appid)
    {
        $data = [
          'code' => (string) request()->get('code'),
        ];

        return $this->mini($appid)
                    ->getClient()
                    ->postJson('wxa/business/getuserphonenumber', $data)
                    ->toArray();
    }
}
```

</details>

<details>
  <summary>Laravel 開放平臺代公眾號處理回撥事件</summary>

```php
// 代公眾號處理回撥事件
Route::any('callback/{appid}', function ($appId) {
    // $app 為你例項化的開放平臺物件，此處省略例項化步驟
    // $refreshToken 為授權後你快取或資料庫中的 authorizer_refresh_token，此處省略獲取步驟

    $refreshToken = '你已快取或資料庫中的 authorizer_refresh_token';

    $server = $app->getOfficialAccountWithRefreshToken($appId, $refreshToken)->getServer();

    $server->addMessageListener('text', function ($message) {
        return sprintf("你對 overtrue 說：“%s”", $message->Content);
    });

    return $server->serve();
});
```

</details>

<!--
<details>
    <summary>標題</summary>
內容
</details>
-->
