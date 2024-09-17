# 訊息

我把微信的 API 裡的所有“訊息”都按型別抽象出來了，也就是說，你不用區分它是回覆訊息還是主動推送訊息，免去了你去手動拼裝微信的 XML 以及亂七八糟命名不統一的 JSON 了。

在閱讀以下內容時請忽略是 **接收訊息** 還是 **回覆訊息**，後面我會給你講它們的區別。

## 訊息型別

訊息分為以下幾種：`文字`、`圖片`、`影片`、`聲音`、`連結`、`座標`、`圖文`、`文章` 和一種特殊的 `原始訊息`。

另外還有一種特殊的訊息型別：**素材訊息**，用於群發或者客服時傳送已有素材用。

> 注意：回覆訊息與客服訊息裡的圖文型別為：**圖文**，群發與素材中的圖文為**文章**

所有的訊息類都在 `EasyWeChat\Kernel\Messages` 這個名稱空間下， 下面我們來分開講解：

### 文字訊息

屬性列表：

> - `content` 文字內容

```php
use EasyWeChat\Kernel\Messages\Text;

$text = new Text('您好！overtrue。');

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

use EasyWeChat\Kernel\Messages\Image;

$image = new Image($mediaId);
```

### 影片訊息

屬性列表：

> - `title` 標題
> - `description` 描述
> - `media_id` 媒體資源 ID
> - `thumb_media_id` 封面資源 ID

```php

use EasyWeChat\Kernel\Messages\Video;

$video = new Video($mediaId, [
        'title' => $title,
        'description' => '...',
    ]);
```

### 聲音訊息

屬性列表：

> - `media_id` 媒體資源 ID

```php
use EasyWeChat\Kernel\Messages\Voice;

$voice = new Voice($mediaId);
```

### 連結訊息

> 復連結訊息

### 座標訊息

> 復座標訊息

### 圖文訊息

圖文訊息分為 `NewsItem` 與 `News`，`NewsItem` 為圖文內容條目。

> ，被動回覆訊息與客服訊息介面的圖文訊息型別中圖文數目只能為一條](https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&announce_id=115383153198yAvN&version=&lang=zh_CN&token=)

`NewsItem` 屬性：

> - `title` 標題
> - `description` 描述
> - `image` 圖片連結
> - `url` 連結 URL

```php
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

$items = [
    new NewsItem([
        'title'       => $title,
        'description' => '...',
        'url'         => $url,
        'image'       => $image,
        // ...
    ]),
];
$news = new News($items);
```

### 文章

屬性列表：

> - `title` 標題
> - `author` 作者
> - `content` 具體內容
> - `thumb_media_id` 圖文訊息的封面圖片素材 id（必須是永久 mediaID）
> - `digest` 圖文訊息的摘要，僅有單圖文訊息才有摘要，多圖文此處為空
> - `source_url` 來源 URL
> - `show_cover` 是否顯示封面，0 為 false，即不顯示，1 為 true，即顯示

```php
use EasyWeChat\Kernel\Messages\Article;

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

> 素材訊息不支援被動回覆，如需被動回覆素材訊息，首先組裝後，再 News 方法返回。

屬性就一個：`media_id`。

在構造時有兩個引數：

> - `$type` 素材型別，目前只支援：`mpnews`、 `mpvideo`、`voice`、`image` 等。
> - `$mediaId` 素材 ID，從介面查詢或者上傳後得到。

```php
use EasyWeChat\Kernel\Messages\Media;

$media = new Media($mediaId, 'mpnews');
```

以上呢，是所有微信支援的基本訊息型別。

> 需要注意的是，你不需要關心微信的訊息欄位叫啥，因為這裡我們使用了更標準的命名，然後最終在中間做了轉換，所以你不需要關注。

### 原始訊息

原始訊息是一種特殊的訊息，它的場景是：**你不想使用其它訊息型別，你想自己手動拼訊息**。比如，回覆訊息時，你想自己拼 XML，那麼你就直接用它就可以了：

```php
use EasyWeChat\Kernel\Messages\Raw;

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

比如，你要用於客服訊息(客服訊息是 JSON 結構)：

```php
use EasyWeChat\Kernel\Messages\Raw;

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

在 [服務端](server) 一節中，我們講了回覆訊息的寫法：

```php
// ... 前面部分省略
$app->server->push(function ($message) {
    return "您好！歡迎關注我!";
});

$response = $server->serve();
```

上面 `return` 了一句普通的文字內容，這裡只是為了方便大家，實際上最後會有一個隱式轉換為 `Text` 型別的動作。

如果你要回復其它型別的訊息，就需要返回一個具體的例項了，比如回覆一個圖片型別的訊息：

```php
use EasyWeChat\Kernel\Messages\Image;
// ...
$app->server->push(function ($message) {
    return new Image('media-id');
});
// ...
```

#### 回覆多圖文訊息

> ，被動回覆訊息與客服訊息介面的圖文訊息型別中圖文數目只能為一條](https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&announce_id=115383153198yAvN&version=&lang=zh_CN&token=)

多圖文訊息其實就是單圖文訊息的一個數組而已了：

```php
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

// ...
$app->server->push(function ($message) {
   $news = new NewsItem(...);
   return new News([$news]);
});
// ...
```

### 作為客服訊息傳送

在客服訊息裡的使用也一樣，都是直接傳入訊息例項即可：

```php
use EasyWeChat\Kernel\Messages\Text;

$message = new Text('Hello world!');

$result = $app->customer_service->message($message)->to($openId)->send();
//...
```

#### 傳送多圖文訊息

> ，被動回覆訊息與客服訊息介面的圖文訊息型別中圖文數目只能為一條](https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&announce_id=115383153198yAvN&version=&lang=zh_CN&token=)

多圖文訊息其實就是單圖文訊息組成的一個 News 物件而已：

```php
$news1 = new NewsItem(...);
$news = new News([$news1]);

$app->customer_service->message($news)->to($openId)->send();
```

### 群發訊息

請參考：[群發訊息](broadcasting)

## 訊息轉發給客服系統

參見：[多客服訊息轉發](message-transfer)
