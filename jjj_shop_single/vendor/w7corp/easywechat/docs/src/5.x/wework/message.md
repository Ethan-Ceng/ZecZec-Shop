# 訊息

## 主動傳送訊息

```php
use EasyWeChat\Kernel\Messages\TextCard;


// 獲取 Messenger 例項
$messenger = $app->messenger;

// 準備訊息
$message = new TextCard([
    'title' => '你的請假單審批透過', 
    'description' => '單號：1928373, ....', 
    'url' => 'http://easywechat.com/oa/....'
]);

// 傳送
$messenger->message($message)->toUser('overtrue')->send();

```

你也可以很方便的傳送普通文字訊息：

```php
$messenger->message('你的請假單（單號：1928373）已經審批透過！')->toUser('overtrue')->send();
// 或者寫成
$messenger->toUser('overtrue')->send('你的請假單（單號：1928373）已經審批透過！');
```

## 接收訊息

被動接收訊息，與回覆訊息，請參考：[服務端](server)


## 更新任務卡片訊息狀態 

```php
$messenger->updateTaskcard(array $userids, int $agentId, string $taskId, string $replaceName = '已收到')
```

