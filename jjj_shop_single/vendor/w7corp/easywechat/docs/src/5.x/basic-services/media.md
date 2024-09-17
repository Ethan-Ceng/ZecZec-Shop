# 臨時素材

上傳的臨時多媒體檔案有格式和大小限制，如下：

> - 圖片（image）: 2M，支援 `JPG` 格式
> - 語音（voice）：2M，播放長度不超過 `60s`，支援 `AMR\MP3` 格式
> - 影片（video）：10MB，支援 `MP4` 格式
> - 縮圖（thumb）：64KB，支援 `JPG` 格式

## 上傳圖片

> 注意：微信圖片上傳服務有敏感檢測系統，圖片內容如果含有敏感內容，如色情，商品推廣，虛假資訊等，上傳可能失敗。

```php
$app->media->uploadImage($path);
```

## 上傳聲音

```php
$app->media->uploadVoice($path);
```

## 上傳影片

```php
$app->media->uploadVideo($path, $title, $description);
```

## 上傳縮圖

用於影片封面或者音樂封面。

```php
$app->media->uploadThumb($path);
```

## 上傳群發影片

上傳影片獲取 `media_id` 用以建立群發訊息用。

```php
$app->media->uploadVideoForBroadcasting($path, $title, $description);

//{
//  "media_id": "rF4UdIMfYK3efUfyoddYRMU50zMiRmmt_l0kszupYh_SzrcW5Gaheq05p_lHuOTQ",
//  "title": "TITLE",
//  "description": "Description"
//}
```

## 建立群發訊息

不要與上面 **上傳群發影片** 搞混了，上面一個是上傳影片得到 `media_id`，這個是使用該 `media_id` 加標題描述 **建立一條訊息素材** 用來發送給使用者。詳情參見：[訊息群發](../official-account/broadcasting.md)

```php
$app->media->createVideoForBroadcasting($mediaId, $title, $description);

//{
//  "type":"video",
//  "media_id":"IhdaAQXuvJtGzwwc0abfXnzeezfO0NgPK6AQYShD8RQYMTtfzbLdBIQkQziv2XJc",
//  "created_at":1398848981
//}
```

## 獲取臨時素材內容

比如圖片、語音等二進位制流內容，響應為 `EasyWeChat\Kernel\Http\StreamResponse` 例項。

```php
$stream = $app->media->get($mediaId);

if ($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
  // 以內容 md5 為檔名存到本地
  $stream->save('儲存目錄');

  // 自定義檔名，不需要帶字尾
  $stream->saveAs('儲存目錄', '檔名');
}
```

## 獲取 JSSDK 上傳的高畫質語音

```php
$stream = $app->media->getJssdkMedia($mediaId);
$stream->saveAs('儲存目錄', 'custom-name.speex');
```
