# 服務端

## 第三方平臺推送事件

公眾號第三方平臺推送的有四個事件：

> 如已經授權的公眾號、小程式再次進行授權，而未修改已授權的許可權的話，是沒有相關事件推送的。

​	授權成功 `authorized`

​	授權更新 `updateauthorized`

​	授權取消 `unauthorized`

​	VerifyTicket  `component_verify_ticket`

SDK 預設會處理事件 `component_verify_ticket` ，並會快取 `verify_ticket` 所以如果你暫時不需要處理其他事件，直接這樣使用即可：

```php
$server = $openPlatform->server;

return $server->serve();
```

## 自定義訊息處理器

> *訊息處理器詳細說明見公眾號開發 - 服務端一節*

```php
use EasyWeChat\OpenPlatform\Server\Guard;

$server = $openPlatform->server;

// 處理授權成功事件
$server->push(function ($message) {
    // ...
}, Guard::EVENT_AUTHORIZED);

// 處理授權更新事件
$server->push(function ($message) {
    // ...
}, Guard::EVENT_UPDATE_AUTHORIZED);

// 處理授權取消事件
$server->push(function ($message) {
    // ...
}, Guard::EVENT_UNAUTHORIZED);
```

### 示例（Laravel 框架）

```php
// 假設你的開放平臺第三方平臺設定的授權事件接收 URL 為: https://easywechat.com/open-platform （其他事件推送同樣會推送到這個 URL）
Route::post('open-platform', function () { // 關閉 CSRF
    // $openPlatform 為你例項化的開放平臺物件，此處省略例項化步驟
    return $openPlatform->server->serve(); // Done!
});

// 處理事件
use EasyWeChat\OpenPlatform\Server\Guard;
Route::post('open-platform', function () {
    $server = $openPlatform->server;
    // 處理授權成功事件，其他事件同理
    $server->push(function ($message) {
        // $message 為微信推送的通知內容，不同事件不同內容，詳看微信官方文件
        // 獲取授權公眾號 AppId： $message['AuthorizerAppid']
        // 獲取 AuthCode：$message['AuthorizationCode']
        // 然後進行業務處理，如存資料庫等...
    }, Guard::EVENT_AUTHORIZED);

    return $server->serve();
});
```
