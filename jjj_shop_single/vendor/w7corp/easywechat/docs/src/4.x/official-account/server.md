# 服務端

我們在入門小教程一節以服務端為例講解了一個基本的訊息的處理，這裡就不再講伺服器驗證的流程了，請直接參考前面的入門例項即可。

服務端的作用呢，在整個微信開發中主要是負責 **[接收使用者傳送過來的訊息](http://mp.weixin.qq.com/wiki/10/79502792eef98d6e0c6e1739da387346.html)**，還有 **[使用者觸發的一系列事件](http://mp.weixin.qq.com/wiki/2/5baf56ce4947d35003b86a9805634b1e.html)**。

首先我們得理清訊息與事件的回覆邏輯，當你收到使用者訊息後（訊息由微信伺服器推送到你的伺服器），在你對訊息進行一些處理後，不管是選擇回覆一個訊息還是什麼不都回給使用者，你也應該給微信伺服器一個 “答覆”，如果是選擇回覆一條訊息，就直接返回一個訊息xml就好，如果選擇不作任何回覆，你也得回覆一個空字串或者字串 `SUCCESS`（不然使用者就會看到 `該公眾號暫時無法提供服務`）。

## 基本使用

在 SDK 中使用 `$app->server->push(callable $callback)` 來設定訊息處理器：

```php
$app->server->push(function ($message) {
    // $message['FromUserName'] // 使用者的 openid
    // $message['MsgType'] // 訊息型別：event, text....
    return "您好！歡迎使用 EasyWeChat";
});

// 在 laravel 中：
$response = $app->server->serve();

// $response 為 `Symfony\Component\HttpFoundation\Response` 例項
// 對於需要直接輸出響應的框架，或者原生 PHP 環境下
$response->send();

// 而 laravel 中直接返回即可：

return $response;
```

這裡我們使用 `push` 傳入了一個 **閉包（[Closure](http://php.net/manual/en/class.closure.php)）**，該閉包接收一個引數 `$message` 為訊息物件（型別取決於你的配置中 `response_type`），你可以在全域性訊息處理器中對訊息型別進行篩選：

```php
$app->server->push(function ($message) {
    switch ($message['MsgType']) {
        case 'event':
            return '收到事件訊息';
            break;
        case 'text':
            return '收到文字訊息';
            break;
        case 'image':
            return '收到圖片訊息';
            break;
        case 'voice':
            return '收到語音訊息';
            break;
        case 'video':
            return '收到影片訊息';
            break;
        case 'location':
            return '收到座標訊息';
            break;
        case 'link':
            return '收到連結訊息';
            break;
        case 'file':
            return '收到檔案訊息';
        // ... 其它訊息
        default:
            return '收到其它訊息';
            break;
    }

    // ...
});
```

當然，因為這裡 `push` 接收一個 [`callable`](http://php.net/manual/zh/language.types.callable.php) 的引數，所以你不一定要傳入一個 Closure 閉包，你可以選擇傳入一個函式名，一個 `[$class, $method]` 或者 `Foo::bar` 這樣的型別。

某些情況，我們需要直接使用 `$message` 引數，那麼怎麼在 `push` 的閉包外呼叫呢？

```php
    $message = $server->getMessage();
```
>  注意：`$message` 的型別取決於你的配置中 `response_type`

## 註冊多個訊息處理器

有時候你可能需要對訊息記日誌，或者一系列的自定義操作，你可以註冊多個 handler：

```php
$app->server->push(MessageLogHandler::class);
$app->server->push(MessageReplyHandler::class);
$app->server->push(OtherHandler::class);
$app->server->push(...);
```

1. 最後一個非空返回值將作為最終應答給使用者的訊息內容，如果中間某一個 handler 返回值 false, 則將終止整個呼叫鏈，不會呼叫後續的 handlers。
2. 傳入的自定義 Handler 類需要實現 `\EasyWeChat\Kernel\Contracts\EventHandlerInterface`。

## 註冊指定訊息型別的訊息處理器

我們想對特定型別的訊息應用不同的處理器，可以在第二個引數傳入型別篩選：

> 注意，第二個引數必須是 `\EasyWeChat\Kernel\Messages\Message` 類的常量。

```php
use EasyWeChat\Kernel\Messages\Message;

$app->server->push(ImageMessageHandler::class, Message::IMAGE); // 圖片訊息
$app->server->push(TextMessageHandler::class, Message::TEXT); // 文字訊息

// 同時處理多種型別的處理器
$app->server->push(MediaMessageHandler::class, Message::VOICE|Message::VIDEO|Message::SHORT_VIDEO); // 當訊息為 三種中任意一種都可觸發
```

## 請求訊息的屬性

當你接收到使用者發來的訊息時，可能會提取訊息中的相關屬性，參考：

請求訊息基本屬性(以下所有訊息都有的基本屬性)：

>>  - `ToUserName`    接收方帳號（該公眾號 ID）
>>  - `FromUserName`  傳送方帳號（OpenID, 代表使用者的唯一標識）
>>  - `CreateTime`    訊息建立時間（時間戳）
>>  - `MsgId`        訊息 ID（64位整型）

### 文字：

>  - `MsgType`  text
>  - `Content`  文字訊息內容

### 圖片：

>  - `MsgType`  image
>  - `MediaId`  圖片訊息媒體id，可以呼叫多媒體檔案下載介面拉取資料。
>  - `PicUrl`   圖片連結

### 語音：

>  - `MsgType`        voice
>  - `MediaId`        語音訊息媒體id，可以呼叫多媒體檔案下載介面拉取資料。
>  - `Format`         語音格式，如 amr，speex 等
>  - `Recognition`  * 開通語音識別後才有

  > 識別後，使用者每次傳送語音給公眾號時，微信會在推送的語音訊息XML資料包中，增加一個 `Recongnition` 欄位

### 影片：

>  - `MsgType`       video
>  - `MediaId`       影片訊息媒體id，可以呼叫多媒體檔案下載介面拉取資料。
>  - `ThumbMediaId`  影片訊息縮圖的媒體id，可以呼叫多媒體檔案下載介面拉取資料。

### 小影片：

>  - `MsgType`     shortvideo
>  - `MediaId`     影片訊息媒體id，可以呼叫多媒體檔案下載介面拉取資料。
>  - `ThumbMediaId`    影片訊息縮圖的媒體id，可以呼叫多媒體檔案下載介面拉取資料。

### 事件：

>  - `MsgType`     event
>  - `Event`       事件型別 （如：subscribe(訂閱)、unsubscribe(取消訂閱) ...， CLICK 等）

#### 掃描帶引數二維碼事件
>  - `EventKey`    事件KEY值，比如：qrscene_123123，qrscene_為字首，後面為二維碼的引數值
>  - `Ticket`      二維碼的 ticket，可用來換取二維碼圖片

#### 上報地理位置事件
>  - `Latitude`    23.137466   地理位置緯度
>  - `Longitude`   113.352425  地理位置經度
>  - `Precision`   119.385040  地理位置精度

#### 自定義選單事件
>  - `EventKey`    事件KEY值，與自定義選單介面中KEY值對應，如：CUSTOM_KEY_001, www.qq.com

### 地理位置：

>  - `MsgType`     location
>  - `Location_X`  地理位置緯度
>  - `Location_Y`  地理位置經度
>  - `Scale`       地圖縮放大小
>  - `Label`       地理位置資訊

### 連結：

>  - `MsgType`      link
>  - `Title`        訊息標題
>  - `Description`  訊息描述
>  - `Url`          訊息連結

### 檔案：

  `MsgType`      file
  `Title`        檔名
  `Description`  檔案描述，可能為null
  `FileKey`      檔案KEY
  `FileMd5`      檔案MD5值
  `FileTotalLen` 檔案大小，單位位元組

## 回覆訊息

回覆的訊息可以為 `null`，此時 SDK 會返回給微信一個 "SUCCESS"，你也可以回覆一個普通字串，比如：`歡迎關注 overtrue.`，此時 SDK 會對它進行一個封裝，產生一個 [`EasyWeChat\Kernel\Messages\Text`](https://github.com/EasyWeChat/message/blob/master/src/Kernel/Messages/Text.php) 型別的訊息並在最後的 `$app->server->serve();` 時生成對應的訊息 XML 格式。

如果你想返回一個自己手動拼的原生 XML 格式訊息，請返回一個 [`EasyWeChat\Kernel\Messages\Raw`](https://github.com/EasyWeChat/message/blob/master/src/Kernel/Messages/Raw.php) 例項即可。

## 訊息轉發給客服系統

參見：[多客服訊息轉發](message-transfer)

關於訊息的使用，請參考 [`訊息`](messages) 章節。
