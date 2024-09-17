# 服務端


我們在入門小教程一節以服務端為例講解了一個基本的訊息的處理，這裡就不再講伺服器驗證的流程了，請直接參考前面的入門例項即可。

服務端的作用呢，在整個微信開發中主要是負責 **[接收使用者傳送過來的訊息](http://mp.weixin.qq.com/wiki/10/79502792eef98d6e0c6e1739da387346.html)**，還有 **[使用者觸發的一系列事件](http://mp.weixin.qq.com/wiki/2/5baf56ce4947d35003b86a9805634b1e.html)**。

首先我們得釐清一下訊息與事件的回覆，當你收到使用者訊息後（訊息由微信伺服器推送到你的伺服器），在你對訊息進行一些處理後，不管是選擇回覆一個訊息還是什麼不都回給使用者，你也應該給微信伺服器一個 “答覆”，如果是選擇回覆一條訊息，就直接返回一個訊息xml就好，如果選擇不作任何回覆，你也得回覆一個空字串或者字串 `SUCCESS`（不然使用者就會看到 `該公眾號暫時無法提供服務`）。

## 基本使用

在 SDK 中呢，使用 `setMessageHandler(callable $callback)` 來設定訊息處理函式：

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

// 從專案例項中得到服務端應用例項。
$server = $app->server;

$server->setMessageHandler(function ($message) {
    // $message->FromUserName // 使用者的 openid
    // $message->MsgType // 訊息型別：event, text....
    return "您好！歡迎關注我!";
});

$response = $server->serve();

$response->send(); // Laravel 裡請使用：return $response;
```

這裡我們使用 `setMessageHandler` 傳入了一個 **閉包（[Closure](http://php.net/manual/en/class.closure.php)）**，該閉包接收一個引數 `$message` 為訊息物件（Collection），這裡需要注意的時，與 2.0 不同，2.0 當中我們對訊息與事件做了區分，還對訊息進行了分類（按 MsgType）。在 3.0 後，**所有的訊息包括事件都會使用 `setMessageHandler` 來處理**，也就是說你可能需要在裡面進行一些判斷，例如：

```php
$server->setMessageHandler(function ($message) {
    switch ($message->MsgType) {
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
        // ... 其它訊息
        default:
            return '收到其它訊息';
            break;
    }

    // ...
});
```

當然，因為這裡 `setMessageHandler` 接收一個 [`callable`](http://php.net/manual/zh/language.types.callable.php) 的引數，所以你不一定要傳入一個 Closure 閉包，你可以選擇傳入一個函式名，一個 `[$class, $method]` 或者 `Foo::bar` 這樣的型別。

> :heart: 注意，預設沒有驗證是否為微信的請求，部署上線建議關掉 debug 模式。

某些情況，我們需要直接使用 `$message` 引數，那麼怎麼在 `setMessageHandler` 閉包外呼叫呢？

```php
    $message = $server->getMessage();
```
> 注意：`$message` 是一個數組型別的資料，使用的時候這樣使用：`$message['ToUserName']`

## 請求訊息的屬性

當你接收到使用者發來的訊息時，可能會提取訊息中的相關屬性，那麼請參考：

請求訊息基本屬性(以下所有訊息都有的基本屬性)：

    $message->ToUserName    接收方帳號（該公眾號 ID）
    $message->FromUserName  傳送方帳號（OpenID, 代表使用者的唯一標識）
    $message->CreateTime    訊息建立時間（時間戳）
    $message->MsgId         訊息 ID（64位整型）

### 文字：

    $message->MsgType  text
    $message->Content  文字訊息內容

### 圖片：

    $message->MsgType  image
    $message->PicUrl   圖片連結

### 語音：

    $message->MsgType        voice
    $message->MediaId        語音訊息媒體id，可以呼叫多媒體檔案下載介面拉取資料。
    $message->Format         語音格式，如 amr，speex 等
    $message->Recognition * 開通語音識別後才有

    > 請注意，開通語音識別後，使用者每次傳送語音給公眾號時，微信會在推送的語音訊息XML資料包中，增加一個 `Recongnition` 欄位

### 影片：

    $message->MsgType       video
    $message->MediaId       影片訊息媒體id，可以呼叫多媒體檔案下載介面拉取資料。
    $message->ThumbMediaId  影片訊息縮圖的媒體id，可以呼叫多媒體檔案下載介面拉取資料。

### 小影片：

    $message->MsgType     shortvideo
    $message->MediaId     影片訊息媒體id，可以呼叫多媒體檔案下載介面拉取資料。
    $message->ThumbMediaId    影片訊息縮圖的媒體id，可以呼叫多媒體檔案下載介面拉取資料。

### 事件：

    $message->MsgType     event
    $message->Event       事件型別 （如：subscribe(訂閱)、unsubscribe(取消訂閱) ...， CLICK 等）

    # 掃描帶引數二維碼事件
    $message->EventKey    事件KEY值，比如：qrscene_123123，qrscene_為字首，後面為二維碼的引數值
    $message->Ticket      二維碼的 ticket，可用來換取二維碼圖片

    # 上報地理位置事件
    $message->Latitude    23.137466   地理位置緯度
    $message->Longitude   113.352425  地理位置經度
    $message->Precision   119.385040  地理位置精度

    # 自定義選單事件
    $message->EventKey    事件KEY值，與自定義選單介面中KEY值對應，如：CUSTOM_KEY_001, www.qq.com

### 地理位置：

    $message->MsgType     location
    $message->Location_X  地理位置緯度
    $message->Location_Y  地理位置經度
    $message->Scale       地圖縮放大小
    $message->Label       地理位置資訊

### 連結：

    $message->MsgType      link
    $message->Title        訊息標題
    $message->Description  訊息描述
    $message->Url          訊息連結

## 回覆訊息

回覆的訊息可以為 `null`，此時 SDK 會返回給微信一個 "SUCCESS"，你也可以回覆一個普通字串，比如：`歡迎關注 overtrue.`，此時 SDK 會對它進行一個封裝，產生一個 [`EasyWeChat\Message\Text`](https://github.com/EasyWeChat/message/blob/master/src/Text.php) 型別的訊息並在最後的 `$server->serve();` 時生成對應的訊息 XML 格式。

如果你想返回一個自己手動拼的原生 XML 格式訊息，請返回一個 [`EasyWeChat\Message\Raw`](https://github.com/EasyWeChat/message/blob/master/src/Raw.php) 例項即可。

## 訊息轉發給客服系統

參見：[多客服訊息轉發](message-transfer.html)

關於訊息的使用，請參考 [`訊息`](messages.html) 章節。
