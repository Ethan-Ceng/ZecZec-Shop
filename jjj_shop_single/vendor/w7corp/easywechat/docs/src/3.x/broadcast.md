# 群發


微信的群發訊息介面有各種亂七八糟的注意事項及限制，具體請閱讀微信官方文件：http://mp.weixin.qq.com/wiki/15/5380a4e6f02f2ffdc7981a8ed7a40753.html

## 獲取例項

```php
<?php
use EasyWeChat\Foundation\Application;
// ...
$app = new Application($options);

$broadcast = $app->broadcast;

```

## API

> 注意：

    下面提到的 `$messageType` 、`$message` 可以是：

    - `$messageType = Broadcast::MSG_TYPE_NEWS;` 圖文訊息型別，所對應的 `$message` 為 media_id
    - `$messageType = Broadcast::MSG_TYPE_TEXT;` 文字訊息型別，所對應的 `$message` 為一個文字字串
    - `$messageType = Broadcast::MSG_TYPE_VOICE;` 語音訊息型別，所對應的 `$message` 為 media_id
    - `$messageType = Broadcast::MSG_TYPE_IMAGE;` 圖片訊息型別，所對應的 `$message` 為 media_id
    - `$messageType = Broadcast::MSG_TYPE_CARD;` 卡券訊息型別，所對應的 `$message` 為 card_id
    - `$messageType = Broadcast::MSG_TYPE_VIDEO;` 影片訊息為兩種情況：
        - 影片訊息型別，群發影片訊息給**組或預覽群發影片訊息**給使用者時所對應的 `$message` 為`media_id`
        - 群發影片訊息**給指定使用者**時所對應的 `$message` 為一個數組 `['MEDIA_ID', 'TITLE', 'DESCRIPTION']`


### 群發訊息給所有粉絲

```php
$broadcast->send($messageType, $message);

// 別名方式
$broadcast->sendText("大家好！歡迎使用 EasyWeChat。");
$broadcast->sendNews($mediaId);
$broadcast->sendVoice($mediaId);
$broadcast->sendImage($mediaId);
//影片：
// - 群發給組使用者，或者預覽群發影片時 $message 為 media_id
// - 群發給指定使用者時為陣列：[$media_Id, $title, $description]
$broadcast->sendVideo($message);
$broadcast->sendCard($cardId);
```

### 群發訊息給指定組

```php
$broadcast->send($messageType, $message, $groupId);

// 別名方式
$broadcast->sendText($text, $groupId);
$broadcast->sendNews($mediaId, $groupId);
$broadcast->sendVoice($mediaId, $groupId);
$broadcast->sendImage($mediaId, $groupId);
$broadcast->sendVideo($message, $groupId);
$broadcast->sendCard($cardId, $groupId);
```

### 群發訊息給指定使用者

至少兩個使用者的openid，必須是陣列。

```php
$broadcast->send($messageType, $message, [$openId1, $openId2]);

// 別名方式
$broadcast->sendText($text, [$openId1, $openId2]);
$broadcast->sendNews($mediaId, [$openId1, $openId2]);
$broadcast->sendVoice($mediaId, [$openId1, $openId2]);
$broadcast->sendImage($mediaId, [$openId1, $openId2]);
$broadcast->sendVideo($message, [$openId1, $openId2]);
$broadcast->sendCard($cardId, [$openId1, $openId2]);
```

### 傳送預覽群發訊息給指定的 `openId` 使用者

```php
$broadcast->preview($messageType, $message, $openId);

// 別名方式
$broadcast->previewText($text, $openId);
$broadcast->previewNews($mediaId, $openId);
$broadcast->previewVoice($mediaId, $openId);
$broadcast->previewImage($mediaId, $openId);
$broadcast->previewVideo($message, $openId);
$broadcast->previewCard($cardId, $openId);
```

### 傳送預覽群發訊息給指定的微訊號使用者

```php
$broadcast->previewByName($messageType, $message, $wxname);

// 別名方式
$broadcast->previewTextByName($text, $wxname);
$broadcast->previewNewsByName($mediaId, $wxname);
$broadcast->previewVoiceByName($mediaId, $wxname);
$broadcast->previewImageByName($mediaId, $wxname);
$broadcast->previewVideoByName($message, $wxname);
$broadcast->previewCardByName($cardId, $wxname);
```

### 刪除群發訊息

```php
$broadcast->delete($msgId);
```

### 查詢群發訊息傳送狀態

```php
$broadcast->status($msgId);
```

有關群發信息的更多細節請參考微信官方文件：http://mp.weixin.qq.com/wiki/
