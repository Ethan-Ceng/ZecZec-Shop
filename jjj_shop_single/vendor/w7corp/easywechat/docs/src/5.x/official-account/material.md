# 素材管理

在微信裡的圖片，音樂，影片等等都需要先上傳到微信伺服器作為素材才可以在訊息中使用。

### 上傳圖片

> 注意：微信圖片上傳服務有敏感檢測系統，圖片內容如果含有敏感內容，如色情，商品推廣，虛假資訊等，上傳可能失敗。

```php
$result = $app->material->uploadImage("/path/to/your/image.jpg");
// {
//    "media_id":MEDIA_ID,
//    "url":URL
// }
```

> `url` 只有上傳圖片素材有返回值。

### 上傳語音

語音 **大小不超過 5M**，**長度不超過 60 秒**，支援 `mp3/wma/wav/amr` 格式。

```php
$result = $app->material->uploadVoice("/path/to/your/voice.mp3");
// {
//    "media_id":MEDIA_ID,
// }
```

### 上傳影片

```php
$result = $app->material->uploadVideo("/path/to/your/video.mp4", "影片標題", "影片描述");
// {
//    "media_id":MEDIA_ID,
// }
```

### 上傳縮圖

用於影片封面或者音樂封面。

```php
$result = $app->material->uploadThumb("/path/to/your/thumb.jpg");
// {
//    "media_id":MEDIA_ID,
// }
```

### 上傳圖文訊息

```php
use EasyWeChat\Kernel\Messages\Article;

// 上傳單篇圖文
$article = new Article([
    'title' => 'xxx',
    'thumb_media_id' => $mediaId,
    //...
  ]);
$app->material->uploadArticle($article);

// 或者多篇圖文
$app->material->uploadArticle([$article, $article2, ...]);
```

### 修改圖文訊息

有三個引數：

> - `$mediaId` 要更新的文章的 `mediaId`
> - `$article` 文章內容，`Article` 例項或者 全欄位陣列
> - `$index` 要更新的文章在圖文訊息中的位置（多圖文訊息時，此欄位才有意義，單圖片忽略此引數），第一篇為 0；

```php
$result = $app->material->updateArticle($mediaId, new Article(...));

// or

$result = $app->material->updateArticle($mediaId, [
   'title' => 'EasyWeChat 4.0 釋出了！',
    'thumb_media_id' => 'qQFxUQGO21Li4YrSn3MhnrqtRp9Zi3cbM9uBsepvDmE', // 封面圖片 mediaId
    'author' => 'overtrue', // 作者
    'show_cover' => 1, // 是否在文章內容顯示封面圖片
    'digest' => '這裡是文章摘要',
    'content' => '這裡是文章內容，你可以放很長的內容',
    'source_url' => 'https://www.easywechat.com',
  ]);

// 指定更新多圖文中的第 2 篇
$result = $app->material->updateArticle($mediaId, new Article(...), 1); // 第 2 篇
```

### 上傳圖文訊息圖片

返回值中 url 就是上傳圖片的 URL，可用於後續群發中，放置到圖文訊息中。

```php
$result = $app->material->uploadArticleImage($path);
//{
//    "url":  "http://mmbiz.qpic.cn/mmbiz/gLO17UPS6FS2xsypf378iaNhWacZ1G1UplZYWEYfwvuU6Ont96b1roYsCNFwaRrSaKTPCUdBK9DgEHicsKwWCBRQ/0"
//}
```

### 獲取永久素材

```php
$resource = $app->material->get($mediaId);
```

如果請求的素材為圖文訊息，則響應如下：

```json
{
 "news_item": [
       {
       "title":TITLE,
       "thumb_media_id"::THUMB_MEDIA_ID,
       "show_cover_pic":SHOW_COVER_PIC(0/1),
       "author":AUTHOR,
       "digest":DIGEST,
       "content":CONTENT,
       "url":URL,
       "content_source_url":CONTENT_SOURCE_URL
       },
       //多圖文訊息有多篇文章
    ]
  }
```

如果返回的是影片訊息素材，則內容如下：

```json
{
  "title": TITLE,
  "description": DESCRIPTION,
  "down_url": DOWN_URL
}
```

其他型別的素材訊息，則響應為 `EasyWeChat\Kernel\Http\StreamResponse` 例項，開發者可以自行儲存為檔案。例如

```php
$stream = $app->material->get($mediaId);

if ($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    // 以內容 md5 為檔名
    $stream->save('儲存目錄');

    // 自定義檔名，不需要帶字尾
    $stream->saveAs('儲存目錄', '檔名');
}
```

### 獲取永久素材列表

> - `$type` 素材的型別，圖片（`image`）、影片（`video`）、語音 （`voice`）、圖文（`news`）
> - `$offset` 從全部素材的該偏移位置開始返回，可選，預設 `0`，0 表示從第一個素材 返回
> - `$count` 返回素材的數量，可選，預設 `20`, 取值在 1 到 20 之間

```php
$app->material->list($type, $offset, $count);
```

示例：

```php
$list = $app->material->list('image', 0, 10);
```

圖片、語音、影片 等型別的返回如下

```json
{
  "total_count": TOTAL_COUNT,
  "item_count": ITEM_COUNT,
  "item": [
    {
      "media_id": MEDIA_ID,
      "name": NAME,
      "update_time": UPDATE_TIME,
      "url": URL
    }
    //可能會有多個素材
  ]
}
```

永久圖文訊息素材列表的響應如下：

```json
{
  "total_count": TOTAL_COUNT,
  "item_count": ITEM_COUNT,
  "item": [
    {
      "media_id": MEDIA_ID,
      "content": {
        "news_item": [
          {
            "title": TITLE,
            "thumb_media_id": THUMB_MEDIA_ID,
            "show_cover_pic": SHOW_COVER_PIC(0 / 1),
            "author": AUTHOR,
            "digest": DIGEST,
            "content": CONTENT,
            "url": URL,
            "content_source_url": CONTETN_SOURCE_URL
          }
          //多圖文訊息會在此處有多篇文章
        ]
      },
      "update_time": UPDATE_TIME
    }
    //可能有多個圖文訊息item結構
  ]
}
```

### 獲取素材計數

```php
$stats = $app->material->stats();

// {
//   "voice_count":COUNT,
//   "video_count":COUNT,
//   "image_count":COUNT,
//   "news_count":COUNT
// }
```

### 刪除永久素材；

```php
$app->material->delete($mediaId);
```

### 文章預覽

文章預覽請參閱 “訊息群發” 章節。
