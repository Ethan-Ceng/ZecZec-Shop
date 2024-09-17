## 服務端

我們在企業微信應用開啟接收訊息的功能，將設定頁面的 token 與 aes key 配置到 agents 下對應的應用內：

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',

    'agent_id' => 100022,
    'secret'   => 'xxxxxxxxxx',

    // server config
    'token' => 'xxxxxxxxx',
    'aes_key' => 'xxxxxxxxxxxxxxxxxx',

    //...
];

$app = Factory::work($config);
```

接著配置服務端與公眾號的服務端用法一樣：

```php
$app->server->push(function($message){
   // $message['FromUserName'] // 訊息來源
   // $message['MsgType'] // 訊息型別：event ....
    
    return 'Hello easywechat.';
});

$response = $app->server->serve();

$response->send();
```

`$response` 為 `Symfony\Component\HttpFoundation\Response` 例項，根據你的框架情況來決定如何處理響應。
