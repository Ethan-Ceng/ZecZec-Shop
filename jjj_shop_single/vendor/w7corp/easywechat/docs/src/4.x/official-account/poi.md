# 門店

## 建立門店

用 POI 介面新建門店時所使用的圖片 url 必須為微信自己域名的 url,因此需要先用上傳圖片接 口上傳圖片並獲取 url,再建立門店。上傳的圖片限制檔案大小限制 1MB,支援 JPG 格式，圖片介面請參考：[臨時素材](../basic-services/media.md)

```php
$app->poi->create($baseInfo);
```

> - `$baseInfo` 為門店的基本資訊陣列

示例：

```php
<?php

$info = array(
         "sid"             => "33788392",
         "business_name"   => "麥當勞",
         "branch_name"     => "藝苑路店",
         "province"        => "廣東省",
         "city"            => "廣州市",
         "district"        => "海珠區",
         "address"         => "藝苑路 11 號",
         "telephone"       => "020-12345678",
         "categories"      => array("美食,快餐小吃"),
         "offset_type"     => 1,
         "longitude"       => 115.32375,
         "latitude"        => 25.097486,
         "photo_list"      => array(
                               array("photo_url" => "https://XXX.com"),
                               array("photo_url" => "https://XXX.com"),
                             ),
         "recommend"       => "麥辣雞腿堡套餐,麥樂雞,全家桶",
         "special"         => "免費 wifi,外賣服務",
         "introduction"    => "麥當勞是全球大型跨國連鎖餐廳,1940 年創立於美國,在世界上大約擁有 3  萬間分店。主要售賣漢堡包,以及薯條、炸雞、汽水、冰品、沙拉、水果等 快餐食品",
         "open_time"       => "8:00-20:00",
         "avg_price"       => 35,
    );

$result = $app->poi->create($info); // true or exception
```

> 注意：新建立的門店在稽核通過後,會以事件形式推送給商戶填寫的回撥 URL

## 獲取指定門店資訊

```php
$app->poi->get($poiId);
```

> - `$poiId` 為門店 ID

示例：

```php
$info = $app->poi->get(271262077);
```

## 獲取門店列表

```php
$app->poi->list($begin, $limit);// begin:0, limit:10
```

> - `$begin` 就是查詢起點，`MySQL` 裡的 `offset`；
> - `$limit` 查詢條數，同 `MySQL` 裡的 `limit`；

> 兩引數均可選

示例：

```php
$pois = $app->poi->list(0, 2);// 取2條記錄
//
//[
//  {
//    "sid": "100",
//    "poi_id": "271864249",
//    "business_name": "麥當勞",
//    "branch_name": "藝苑路店",
//    "address": "藝苑路 11 號",
//    "available_state": 3
//  },
//  {
//    "sid": "101",
//    "business_name": "麥當勞",
//    "branch_name": "赤崗路店",
//    "address": "赤崗路 102 號",
//    "available_state": 4
//  }
//]
```

## 修改門店資訊

商戶可以透過該介面,修改門店的服務資訊,包括:圖片列表、營業時間、推薦、特色服務、簡 介、人均價格、電話 7 個欄位。目前基礎欄位包括(名稱、座標、地址等不可修改)。

```php
$app->poi->update($poiId, $data);
```

> - `$poiId` 為門店 ID
> - `$data` 需要更新的部分資料，**若有填寫內容則為覆蓋更新,若無內容則視為不 修改,維持原有內容。photo_list 欄位為全列表覆蓋,若需要增加圖片,需將之前圖片同樣放入 list 中,在其後增加新增圖片。如:已有 A、B、C 三張圖片,又要增加 D、E 兩張圖,則需要調 用該介面,photo_list 傳入 A、B、C、D、E 五張圖片的連結。**

示例：

```php
$data = array(
         "telephone" => "020-12345678",
         "recommend" => "麥辣雞腿堡套餐,麥樂雞,全家桶",
         //...
        );

$res = $app->poi->update(271262077, $data); //true or exception
```

## 刪除門店

```php
$app->poi->delete($poiId);
```

示例：

```php
$app->poi->delete(271262077);// true or exception
```
