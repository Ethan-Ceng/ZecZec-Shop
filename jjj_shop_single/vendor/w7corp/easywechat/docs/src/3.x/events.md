# 事件


> 注意：3.0 起，所有服務端的入口（**訊息與事件**）都已經合併為一個方法來處理：`setMessageHandler()`

### 在服務端接收使用者端產生的事件

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$server = $app->server;

$server->setMessageHandler(function($message){
    // 注意，這裡的 $message 不僅僅是使用者發來的訊息，也可能是事件
    // 當 $message->MsgType 為 event 時為事件
    if ($message->MsgType == 'event') {
        # code...
        switch ($message->Event) {
            case 'subscribe':
                # code...
                break;

            default:
                # code...
                break;
        }
    }
});

$response = $server->serve();

$response->send(); // Laravel 裡請使用：return $response;
```

> 注意：`$response` 是一個物件，不要直接 echo.

更多請參考：[服務端](server.html)

關於事件型別請參考微信官方文件：http://mp.weixin.qq.com/wiki/
