# 訊息


我把微信的 API 裡的所有“訊息”都按型別抽象出來了，也就是說，你不用區分它是回覆訊息還是主動推送訊息，免去了你去手動拼裝微信那幫 SB 那麼噁心的 XML 以及亂七八糟命名不統一的 JSON 了，我替你承受這份苦，不要問是誰，我是雷鋒他弟弟，雷管。

在閱讀以下內容時請忽略是**接收訊息**還是**回覆訊息**，後面我會給你講它們的區別。

## 訊息型別

訊息分為以下幾種：`文字`、`圖片`、`影片`、`聲音`、`連結`、`座標`、`圖文`、`文章` 和一種特殊的 `原始訊息`。

另外還有一種特殊的訊息型別：**素材訊息**，用於群發或者客服時傳送已有素材用。

> 注意：回覆訊息與客服訊息裡的圖文型別為：**圖文**，群發與素材中的圖文為**文章**

所有的訊息類都在 `EasyWeChat\Message` 這個名稱空間下， 下面我們來分開講解：

### 文字訊息

屬性列表：

```
- content 文字內容
```

```php
<?php

use EasyWeChat\Message\Text;

$text = new Text(['content' => '您好！overtrue。']);

// or
$text = new Text();
$text->content = '您好！overtrue。';

// or
$text = new Text();
$text->setAttribute('content', '您好！overtrue。');
```

### 圖片訊息

屬性列表：

```
- media_id 媒體資源 ID
```

```php
<?php

use EasyWeChat\Message\Image;

$text = new Image(['media_id' => $mediaId]);

// or
$text = new Image();
$text->media_id = $mediaId; // or $text->mediaId = $media;

// or
$text = new Image();
$text->setAttribute('media_id', $mediaId);
```


### 影片訊息

屬性列表：

```
- title 標題
- description 描述
- media_id 媒體資源 ID
- thumb_media_id 封面資源 ID
```

```php
<?php

use EasyWeChat\Message\Video;

$video = new Video([
        'title' => $title,
        'media_id' => $mediaId,
        'description' => '...',
        // ...
    ]);

// or
$video = new Video();
$video->media_id = $mediaId; // or $video->mediaId = $media;
$video->description = 'video description...'; // or $video->description = $description;
// ...

// or
$video = new Video();
$video->setAttribute('media_id', $mediaId);
// ...
```

### 聲音訊息

屬性列表：

```
- media_id 媒體資源 ID
```

```php
<?php

use EasyWeChat\Message\Voice;

$voice = new Voice(['media_id' => $mediaId]);

// or
$voice = new Voice();
$voice->media_id = $mediaId; // or $voice->mediaId = $media;

// or
$voice = new Voice();
$voice->setAttribute('media_id', $mediaId);
```

### 連結訊息

> 微信目前不支援回覆連結訊息

### 座標訊息

> 微信目前不支援回覆座標訊息

### 圖文訊息

屬性列表：

```
- title 標題
- description 描述
- image 圖片連結
- url 連結 URL
```

```php
<?php
use EasyWeChat\Message\News;

$news = new News([
        'title'       => $title,
        'description' => '...',
        'url'         => $url,
        'image'       => $image,
        // ...
    ]);

// or
$news = new News();
$news->title = 'EasyWeChat';
$news->description = '微信 SDK ...';
// ...

```

### 文章訊息

屬性列表：

```
- title 標題
- author 作者
- content 具體內容
- thumb_media_id 圖文訊息的封面圖片素材id（必須是永久mediaID）
- digest 圖文訊息的摘要，僅有單圖文訊息才有摘要，多圖文此處為空
- source_url 來源 URL
- show_cover 是否顯示封面，0 為 false，即不顯示，1 為 true，即顯示
```

```php
<?php
use EasyWeChat\Message\Article;

$article = new Article([
        'title'   => 'EasyWeChat',
        'author'  => 'overtrue',
        'content' => 'EasyWeChat 是一個開源的微信 SDK，它... ...',
        // ...
    ]);

// or
$article = new Article();
$article->title   = 'EasyWeChat';
$article->author  = 'overtrue';
$article->content = '微信 SDK ...';
// ...
```


### 素材訊息

素材訊息用於群發與客服訊息時使用。

屬性就一個：`media_id`。

在構造時有兩個引數：

- `$type` 素材型別，目前只支援：`mpnews`、 `mpvideo`、`voice`、`image` 等。
- `$mediaId` 素材 ID，從介面查詢或者上傳後得到。


```php
use EasyWeChat\Message\Material;

$material = new Material('mpnews', $mediaId);
```

以上呢，是所有微信支援的基本訊息型別。

> 需要注意的是，你不需要關心微信的訊息欄位叫啥，因為這裡我們使用了更標準的命名，然後最終在中間做了轉換，所以你不需要關注。

### 原始訊息

原始訊息是一種特殊的訊息，它的場景是：**你不想使用其它訊息型別，你想自己手動拼訊息**。比如，回覆訊息時，你想自己拼 XML，那麼你就直接用它就可以了：

```php
use EasyWeChat\Message\Raw;

$message = new Raw('<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>12345678</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<Image>
<MediaId><![CDATA[media_id]]></MediaId>
</Image>
</xml>');
```

比如，你要用於客服訊息(客服訊息是JSON結構)：

```php
use EasyWeChat\Message\Raw;

$message = new Raw('{
    "touser":"OPENID",
    "msgtype":"text",
    "text":
    {
         "content":"Hello World"
    }
}');
```

總之，就是直接寫微信介面要求的格式內容就好，此型別訊息在 SDK 中不存在轉換行為，所以請注意不要寫錯格式。

## 在 SDK 中使用訊息

### 在服務端回覆訊息

在 [服務端](server.html) 一節中，我們講了回覆訊息的寫法：

```php
// ... 前面部分省略
$app = new Application($options);
$server = $app->server;

$server->setMessageHandler(function ($message) {
    return "您好！歡迎關注我!";
});

$server->serve()->send();
```

上面 `return` 了一句普通的文字內容，這裡只是為了方便大家，實際上最後會有一個隱式轉換為 `Text` 型別的動作。

如果你要回復其它型別的訊息，就需要返回一個具體的例項了，比如回覆一個圖片型別的訊息：

```php
use EasyWeChat\Message\Image;
// ...
$server->setMessageHandler(function ($message) {
    return new Image(['media_id' => '........']);
});
// ...
```

#### 回覆多圖文訊息

多圖文訊息其實就是單圖文訊息的一個數組而已了：

```php
use EasyWeChat\Message\News;

// ...
$server->setMessageHandler(function ($message) {
    $news1 = new News(...);
    $news2 = new News(...);
    $news3 = new News(...);
    $news4 = new News(...);

    return [$news1, $news2, $news3, $news4];
});
// ...
```


### 作為客服訊息傳送

在客服訊息裡的使用也一樣，都是直接傳入訊息例項即可：

```php
use EasyWeChat\Message\Text;

$message = new Text(['content' => 'Hello world!']);

$result = $app->staff->message($message)->to($openId)->send();
//...
```

#### 傳送多圖文訊息

多圖文訊息其實就是單圖文訊息的一個數組而已了：

```php
$news1 = new News(...);
$news2 = new News(...);
$news3 = new News(...);
$news4 = new News(...);

$app->staff->message([$news1, $news2, $news3, $news4])->to($openId)->send();
```

### 群發訊息

請參考：[群發訊息](broadcast.html)

## 訊息轉發給客服系統

參見：[多客服訊息轉發](message-transfer.html)
