# 素材管理


在微信裡的圖片，音樂，影片等等都需要先上傳到微信伺服器作為素材才可以在訊息中使用。

> 請注意：

>     1. 限制：
>       - 圖片（image）: 1M，支援 bmp/png/jpeg/jpg/gif 格式
>       - 語音（voice）：2M，播放長度不超過 60s，支援 mp3/wma/wav/amr 格式
>       - 影片（video）：10MB，支援MP4格式
>       - 縮圖（thumb）：64KB，支援JPG格式

>     2. `media_id` 是可複用的；
>     3. 素材分為 `臨時素材` 與 `永久素材`， 臨時素材媒體檔案在後臺儲存時間為3天，即 3 天后 `media_id` 失效；
>     4. 新增的永久素材也可以在公眾平臺官網素材管理模組中看到；
>     5. 永久素材的數量是有上限的，請謹慎新增。圖文訊息素材和圖片素材的上限為5000，其他型別為1000；

## 獲取例項

```php
<?php
use EasyWeChat\Foundation\Application;

$app = new Application($options);

// 永久素材
$material = $app->material;
// 臨時素材
$temporary = $app->material_temporary;
```

## 永久素材 API：

### 上傳圖片:

> 注意：微信圖片上傳服務有敏感檢測系統，圖片內容如果含有敏感內容，如色情，商品推廣，虛假資訊等，上傳可能失敗。

```php
$result = $material->uploadImage("/path/to/your/image.jpg");  // 請使用絕對路徑寫法！除非你正確的理解了相對路徑（好多人是沒理解對的）！
var_dump($result);
// {
//    "media_id":MEDIA_ID,
//    "url":URL
// }
```

> `url` 只有上傳圖片素材有返回值。

### 上傳聲音

語音**大小不超過 5M**，**長度不超過 60 秒**，支援 `mp3/wma/wav/amr` 格式。

```php
$result = $material->uploadVoice("/path/to/your/voice.mp3"); // 請使用絕對路徑寫法！除非你正確的理解了相對路徑（好多人是沒理解對的）！
$mediaId = $result->media_id;
// {
//    "media_id":MEDIA_ID,
// }
```

### 上傳影片

```php
$result = $material->uploadVideo("/path/to/your/video.mp4", "影片標題", "影片描述"); // 請使用絕對路徑寫法！除非你正確的理解了相對路徑（好多人是沒理解對的）！
$mediaId = $result->media_id;
// {
//    "media_id":MEDIA_ID,
// }
```

### 上傳縮圖

用於影片封面或者音樂封面。

```php
$result = $material->uploadThumb("/path/to/your/thumb.jpg"); // 請使用絕對路徑寫法！除非你正確的理解了相對路徑（好多人是沒理解對的）！
$mediaId = $result->media_id;
// {
//    "media_id":MEDIA_ID,
// }
```

### 上傳永久圖文訊息

圖文訊息沒有臨時一說。

```php
use EasyWeChat\Message\Article;
// 上傳單篇圖文
$article = new Article([
    'title' => 'xxx',
    'thumb_media_id' => $mediaId,
    //...
  ]);
$material->uploadArticle($article);

// 或者多篇圖文
$material->uploadArticle([$article, $article2, ...]);
```

### 修改永久圖文訊息

有三個引數：

- `$mediaId` 要更新的文章的 `mediaId`
- `$article` 文章內容，`Article` 例項或者 全欄位陣列
- `$index` 要更新的文章在圖文訊息中的位置（多圖文訊息時，此欄位才有意義，單圖片忽略此引數），第一篇為 0；

```php
$result = $material->updateArticle($mediaId, new Article(...));
$mediaId = $result->media_id;

// or

$result = $material->updateArticle($mediaId, [
    'title'          => 'xxx',
    'thumb_media_id' => 'xxx',
    // ...
  ]);

// 指定更新多圖文中的第 2 篇
$result = $material->updateArticle($mediaId, new Article(...), 1); // 第 2 篇
```


### 上傳永久文章內容圖片

> 注意：微信圖片上傳服務有敏感檢測系統，圖片內容如果含有敏感內容，如色情，商品推廣，虛假資訊等，上傳可能失敗。

返回值中 url 就是上傳圖片的 URL，可用於後續群發中，放置到圖文訊息中。

```php
$result = $material->uploadArticleImage($path);
$url = $result->url;
//{
//    "url":  "http://mmbiz.qpic.cn/mmbiz/gLO17UPS6FS2xsypf378iaNhWacZ1G1UplZYWEYfwvuU6Ont96b1roYsCNFwaRrSaKTPCUdBK9DgEHicsKwWCBRQ/0"
//}
```

### 獲取永久素材

```php
$resource = $material->get($mediaId);
```

如果請求的素材為圖文訊息，則響應如下：

```
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

```
{
  "title":TITLE,
  "description":DESCRIPTION,
  "down_url":DOWN_URL,
}
```

其他型別的素材訊息，則響應的直接為素材的內容，開發者可以自行儲存為檔案。例如

```
$image = $material->get($mediaId);
file_put_contents('/foo/abc.jpg', $image);
```

### 獲取永久素材列表

參考：[微信公眾平臺開發者文件：獲取永久素材列表](http://mp.weixin.qq.com/wiki/12/2108cd7aafff7f388f41f37efa710204.html)

- `$type`   素材的型別，圖片（`image`）、影片（`video`）、語音 （`voice`）、圖文（`news`）
- `$offset` 從全部素材的該偏移位置開始返回，可選，預設 `0`，0 表示從第一個素材 返回
- `$count`  返回素材的數量，可選，預設 `20`, 取值在 1 到 20 之間

```php
$material->lists($type, $offset, $count);
```

example:

```
$lists = $material->lists('image', 0, 10);
```

圖片、語音、影片 等型別的返回如下

```
{
   "total_count": TOTAL_COUNT,
   "item_count": ITEM_COUNT,
   "item": [{
       "media_id": MEDIA_ID,
       "name": NAME,
       "update_time": UPDATE_TIME,
       "url":URL
   },
   //可能會有多個素材
   ]
}
```

永久圖文訊息素材列表的響應如下：

```
{
   "total_count": TOTAL_COUNT,
   "item_count": ITEM_COUNT,
   "item": [{
       "media_id": MEDIA_ID,
       "content": {
           "news_item": [{
               "title": TITLE,
               "thumb_media_id": THUMB_MEDIA_ID,
               "show_cover_pic": SHOW_COVER_PIC(0 / 1),
               "author": AUTHOR,
               "digest": DIGEST,
               "content": CONTENT,
               "url": URL,
               "content_source_url": CONTETN_SOURCE_URL
           },
           //多圖文訊息會在此處有多篇文章
           ]
        },
        "update_time": UPDATE_TIME
    },
    //可能有多個圖文訊息item結構
  ]
}
```


### 獲取素材計數

```php
$stats = $material->stats();

// {
//   "voice_count":COUNT,
//   "video_count":COUNT,
//   "image_count":COUNT,
//   "news_count":COUNT
// }
```

### 刪除永久素材；

```php
$material->delete($mediaId);
```


## 臨時素材 API

上傳的臨時多媒體檔案有格式和大小限制，如下：

- 圖片（image）: 1M，支援 `JPG` 格式
- 語音（voice）：2M，播放長度不超過 `60s`，支援 `AMR\MP3` 格式
- 影片（video）：10MB，支援 `MP4` 格式
- 縮圖（thumb）：64KB，支援 `JPG` 格式

### 上傳圖片

> 注意：微信圖片上傳服務有敏感檢測系統，圖片內容如果含有敏感內容，如色情，商品推廣，虛假資訊等，上傳可能失敗。

```php
$temporary->uploadImage($path);
```

### 上傳聲音

```php
$temporary->uploadVoice($path);
```

### 上傳影片

```php
$temporary->uploadVideo($path, $title, $description);
```

### 上傳縮圖

用於影片封面或者音樂封面。

```php
$temporary->uploadThumb($path);
```

### 獲取臨時素材內容

比如圖片、影片、聲音等二進位制流內容。

```php
$content = $temporary->getStream($mediaId);
file_put_contents('/tmp/abc.jpg', $content);// 請使用絕對路徑寫法！除非你正確的理解了相對路徑（好多人是沒理解對的）！
```

### 下載臨時素材到本地

其實就是上一個 API 的封裝。

```php
$temporary->download($mediaId, "/tmp/", "abc.jpg");
```

引數說明：

  - `$directory` 為目標目錄，
  - `$filename` 為新的檔名，可以為空，預設使用 `$mediaId` 作為檔名。


更多請參考 [微信官方文件](http://mp.weixin.qq.com/wiki) `素材管理` 章節