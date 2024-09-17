# 群發

微信的群發訊息介面有各種亂七八糟的注意事項及限制，具體請閱讀微信官方文件。

## 傳送訊息

以下所有方法均有第二個引數 `$to` 用於指定接收物件：

>  - 當 `$to` 為整型時為標籤 id
>  - 當 `$to` 為陣列時為使用者的 openid 列表（至少兩個使用者的 openid）
>  - 當 `$to` 為 `null` 時表示全部使用者

```php
$app->broadcasting->sendMessage(Message $message, array | int $to = null);
```

下面的別名方法 `sendXXX` 都是基於上面 `sendMessage` 方法的封裝。

### 文字訊息

```php
$app->broadcasting->sendText("大家好！歡迎使用 EasyWeChat。");

// 指定目標使用者
// 至少兩個使用者的 openid，必須是陣列。
$app->broadcasting->sendText("大家好！歡迎使用 EasyWeChat。", [$openid1, $openid2]);

// 指定標籤組使用者
$app->broadcasting->sendText("大家好！歡迎使用 EasyWeChat。", $tagId); // $tagId 必須是整型數字
```

### 圖文訊息

```php
$app->broadcasting->sendNews($mediaId);
$app->broadcasting->sendNews($mediaId, [$openid1, $openid2]);
$app->broadcasting->sendNews($mediaId, $tagId);
```

### 圖片訊息

```php
$app->broadcasting->sendImage($mediaId);
$app->broadcasting->sendImage($mediaId, [$openid1, $openid2]);
$app->broadcasting->sendImage($mediaId, $tagId);
```

### 語音訊息

```php
$app->broadcasting->sendVoice($mediaId);
$app->broadcasting->sendVoice($mediaId, [$openid1, $openid2]);
$app->broadcasting->sendVoice($mediaId, $tagId);
```

### 影片訊息

用於群發的影片訊息，需要先建立訊息物件，

```php
// 1. 先上傳影片素材用於群發：
$video = '/path/to/video.mp4';
$videoMedia = $app->media->uploadVideoForBroadcasting($video, '影片標題', '影片描述');

// 結果如下：
//{
//  "type":"video",
//  "media_id":"IhdaAQXuvJtGzwwc0abfXnzeezfO0NgPK6AQYShD8RQYMTtfzbLdBIQkQziv2XJc",
//  "created_at":1398848981
//}

// 2. 使用上面得到的 media_id 群發影片訊息
$app->broadcasting->sendVideo($videoMedia['media_id']);
```

### 卡券訊息

```php
$app->broadcasting->sendCard($cardId);
$app->broadcasting->sendCard($cardId, [$openid1, $openid2]);
$app->broadcasting->sendCard($cardId, $tagId);
```

### 傳送預覽群發訊息給指定的 `openId` 使用者

```php
$app->broadcasting->previewText($text, $openId);
$app->broadcasting->previewNews($mediaId, $openId);
$app->broadcasting->previewVoice($mediaId, $openId);
$app->broadcasting->previewImage($mediaId, $openId);
$app->broadcasting->previewVideo($message, $openId);
$app->broadcasting->previewCard($cardId, $openId);
```

### 傳送預覽群發訊息給指定的微訊號使用者

> $wxanme 是使用者的微訊號，比如：notovertrue

```php
$app->broadcasting->previewTextByName($text, $wxname);
$app->broadcasting->previewNewsByName($mediaId, $wxname);
$app->broadcasting->previewVoiceByName($mediaId, $wxname);
$app->broadcasting->previewImageByName($mediaId, $wxname);
$app->broadcasting->previewVideoByName($message, $wxname);
$app->broadcasting->previewCardByName($cardId, $wxname);
```

### 刪除群發訊息

```php
$app->broadcasting->delete($msgId);
```

### 查詢群發訊息傳送狀態

```php
$app->broadcasting->status($msgId);
```
