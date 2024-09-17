# 臨時素材

它的使用是不基於應用的，或者說基於任何一個應用都能訪問這些 API，所以在用法上是直接呼叫 work 例項的 `media` 屬性：

**上傳的媒體檔案限制：**

所有檔案size必須大於5個位元組

>  - 圖片（image）：2MB，支援JPG,PNG格式
>  - 語音（voice）：2MB，播放長度不超過60s，支援AMR格式
>  - 影片（video）：10MB，支援MP4格式
>  - 普通檔案（file）：20MB

## 上傳圖片

> 注意：微信圖片上傳服務有敏感檢測系統，圖片內容如果含有敏感內容，如色情，商品推廣，虛假資訊等，上傳可能失敗。

```php
$app->media->uploadImage($path); // $path 為本地檔案路徑
```

## 上傳聲音

```php
$app->media->uploadVoice($path);
```

## 上傳影片

```php
$app->media->uploadVideo($path, $title, $description);
```

## 上傳普通檔案

```php
$app->media->uploadFile($path);
```

## 獲取素材

```php
$app->media->get($mediaId);
```