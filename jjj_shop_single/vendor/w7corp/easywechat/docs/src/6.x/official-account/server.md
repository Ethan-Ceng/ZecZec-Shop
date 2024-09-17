# 服務端

你可以透過 `$app->getServer()` 獲取服務端模組，**服務端模組預設處理了服務端驗證的邏輯**：

```php
use EasyWeChat\OfficialAccount\Application;

$config = [...];
$app = new Application($config);

$server = $app->getServer();
```

## 服務端驗證

SDK 已經內建了服務端驗證的實現，你不需要自己再去關心 `echostr` 怎麼返回，直接像下面這樣就可以完成服務端驗證：

```php
return $server->serve();
```

## 自助處理推送訊息

> 🚨 注意：不要在返回 `$server->serve()` 前輸出任何內容。

你可以透過下面的方式獲取來自微信伺服器的推送訊息：

```php
$message = $server->getRequestMessage(); // 原始訊息
```

你也可以獲取解密後的訊息 <version-tag>6.5.0+</version-tag>

```php
$message = $server->getDecryptedMessage();
```

`$message` 為一個 `EasyWeChat\OfficialAccount\Message` 例項。

你可以在處理完邏輯後自行建立一個響應，當然，在不同的框架裡，響應寫法也不一樣，請自行實現，我建議使用下面的中介軟體模式來完成會更簡單方便。

## 中介軟體模式

與 5.x 的設計類似，服務端使用中介軟體模式來依次呼叫開發者註冊的中介軟體：

```php
$server->with(function($message, \Closure $next) {
    // 你的自定義邏輯
    return $next($message);
});

$response = $server->serve();
```

你可以註冊多箇中間件來處理不同的情況：

```php
$server
    ->with(function($message, \Closure $next) {
        // 你的自定義邏輯1
        return $next($message);
    })
    ->with(function($message, \Closure $next) {
        // 你的自定義邏輯2
        return $next($message);
    })
    ->with(function($message, \Closure $next) {
        // 你的自定義邏輯3
        return $next($message);
    });

$response = $server->serve();
```

### 回覆訊息

當你在中介軟體裡不回覆訊息時，你將要傳遞訊息給下一個中介軟體：

```php
function($message, \Closure $next) {
    // 你的自定義邏輯3
    return $next($message);
}
```

如果此時你需要返回訊息給使用者，你可以直接像下面這樣回覆訊息內容：

```php
function($message, \Closure $next) {
    return '感謝你使用 EasyWeChat';
}
```

> 注意：回覆訊息後其他沒執行的中介軟體將不再執行，所以請你將全域性都需要執行的中介軟體優先提前註冊。

其他型別的訊息時，請直接參考 **[官方文件訊息的 XML 結構](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Passive_user_reply_message.html)** 以陣列形式返回即可。

需要省略 `ToUserName`、`FromUserName` 和 `CreateTime`，以回覆圖片訊息為例:

```php
function($message, \Closure $next) {
    return [
        'MsgType' => 'image',
        'Image' => [
            'MediaId' => 'media_id',
        ],
    ];
}
```

### 怎麼傳送多條訊息？

服務端只能回覆一條訊息，如果你想在接收到訊息時向用戶傳送多條訊息，你可以呼叫 **[客服訊息](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Service_Center_messages.html)** 介面來發送多條。

### 使用獨立的中介軟體類

當然，中介軟體也支援多種型別，比如你可以使用一個獨立的類作為中介軟體：

```php
class MyCustomHandler
{
    public function __invoke($message, \Closure $next)
    {
        if ($message->MsgType === 'text') {
            //...
        }

        return $next($message);
    }
}
```

註冊中介軟體：

```php
$server->with(MyCustomHandler::class);

// 或者

$server->with(new MyCustomHandler());
```

### 使用 callable 型別中介軟體

中介軟體支援 **[`callable`](http://php.net/manual/zh/language.types.callable.php)** 型別的引數，所以你不一定要傳入一個閉包（Closure），你可以選擇傳入一個函式名，一個 `[$class, $method]` 或者 `Foo::bar` 這樣的型別。

```php
$server->with([$object, 'method']);
$server->with('ClassName::method');
```

## 註冊指定訊息型別的訊息處理器

為了方便開發者處理訊息推送，server 類內建了兩個便捷方法：

### 處理普通訊息

當普通微信使用者向公眾賬號發訊息時被呼叫，且匹配對應的事件型別：

```php
$server->addMessageListener('text', function() { ... });
```

**引數**

- 引數 1 為訊息型別，也就是 message 中的 `MsgType` 欄位，例如：`image`;
- 引數 2 是中介軟體，也就是上面講到的多種型別的中介軟體。

### 處理事件訊息

事件訊息中介軟體僅在推送事件訊息時被呼叫，且匹配對應的事件型別：

```php
$server->addEventListener('subscribe', function() { ... });
```

**引數**

- 引數 1 為事件型別，也就是 message 中的 `Event` 欄位，例如：`subscribe`;
- 引數 2 是中介軟體，也就是上面講到的多種型別的中介軟體。

關於回覆訊息的結構，可以查閱 **[訊息](message.md)** 章節瞭解更多。

## 完整示例

以下示例完成了服務端驗證，自定義中介軟體回覆等邏輯：

```php
use EasyWeChat\OfficialAccount\Application;

$config = [...];
$app = new Application($config);

$server = $app->getServer();

$server->addEventListener('subscribe', function($message, \Closure $next) {
    return '感謝您關注 EasyWeChat!';
});

$response = $server->serve();

return $response;
```

> `$response` 是一個 [Psr\Http\Message\ResponseInterface](https://github.com/php-fig/http-message/blob/master/src/ResponseInterface.php) 實現，所以請自己決定如何適配您的框架。
