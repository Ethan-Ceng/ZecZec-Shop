# 門店小程式

## 拉取門店小程式類目

```php
$app->store->categories();
```

## 建立門店小程式

> 說明：建立門店小程式提交後需要公眾號管理員確認通過後才可進行稽核。如果主管理員 24 小時超時未確認，才能再次提交。

```php
$app->store->createMerchant($baseInfo);
```

> - `$baseInfo` 為門店小程式的基本資訊陣列，**`qualification_list` 欄位為類目相關證件的臨時素材 `mediaid` 如果 `second_catid` 對應的 `sensitive_type` 為 1 ，則 `qualification_list` 欄位需要填 支援 0~5 個 `mediaid`，例如 `mediaid1`。`headimg_mediaid` 欄位為頭像 --- 臨時素材 `mediaid`。`mediaid` 用現有的 `media/upload` 介面得到的,獲取連結： [臨時素材](../basic-services/media.md) ( 支援 PNG\JPEG\JPG\GIF 格式的圖片，後續加上其他格式)**

示例：

```php

$info = [
    "first_catid"        => 476, //categories 介面獲取的一級類目id
    "second_catid"       => 477, //categories 介面獲取的二級類目id
    "qualification_list" =>  "RTZgKZ386yFn5kQSWLTxe4bqxwgzGBjs3OE02cg9CVQk1wRVE3c8fjUFX7jvpi-P",
    "headimg_mediaid"    => "RTZgKZ386yFn5kQSWLTxe4bqxwgzGBjs3OE02cg9CVQk1wRVE3c8fjUFX7jvpi-P",
    "nickname"           => "hardenzhang308",
    "intro"              => "hardenzhangtest",
    "org_code"           => "",
    "other_files"        => ""
];

$result = $app->store->createMerchant($info);
```

> 注意：建立門店小程式的稽核結果,會以事件形式推送給商戶填寫的回撥 URL

## 查詢門店小程式稽核結果

```php
$app->store->getStatus($baseInfo);
```

## 修改門店小程式資訊

```php
$app->store->updateMerchant($data);
```

> - `$data` 需要更新的部分資料，目前僅支援門店頭像和門店小程式介紹，**若有填寫內容則為覆蓋更新,若無內容則視為不修改,維持原有內容。`headimg_mediaid`、`intro` 欄位參考建立門店小程式**

示例：

```php
$data = [
    "headimg_mediaid" => "RTZgKZ386yFn5kQSWLTxe4bqxwgzGBjs3OE02cg9CVQk1wRVE3c8fjUFX7jvpi-P",
    "intro"           => "麥辣雞腿堡套餐,麥樂雞,全家桶",
];

$result = $app->store->updateMerchant($data);
```

## 從騰訊地圖拉取省市區資訊

```php
$app->store->districts();
```

## 在騰訊地圖中搜索門店

```php
$app->store->searchFromMap($districtId, $keyword);
```

> - `$districtId` 為從騰訊地圖拉取的地區 `id`
> - `$keyword` 為搜尋的關鍵詞

## 在騰訊地圖中建立門店

```php
$app->store->createFromMap($baseInfo);
```

示例：

```php
$baseInfo = [
    "name"       => "hardenzhang",
    "longitude"  => "113.323753357",
    "latitude"   => "23.0974903107",
    "province"   => "廣東省",
    "city"       => "廣州市",
    "district"   => "海珠區",
    "address"    => "TIT",
    "category"   => "類目1:類目2",
    "telephone"  => "12345678901",
    "photo"      => "http://mmbiz.qpic.cn/mmbiz_png/tW66AWE2K6ECFPcyAcIZTG8RlcR0sAqBibOm8gao5xOoLfIic9ZJ6MADAktGPxZI7MZLcadZUT36b14NJ2cHRHA/0?wx_fmt=png",
    "license"    => "http://mmbiz.qpic.cn/mmbiz_png/tW66AWE2K6ECFPcyAcIZTG8RlcR0sAqBibOm8gao5xOoLfIic9ZJ6MADAktGPxZI7MZLcadZUT36b14NJ2cHRHA/0?wx_fmt=png",
    "introduct"  => "test",
    "districtid" => "440105",
];
```

> - `$baseInfo`: 門店相關資訊

> 事件推送 --- 騰訊地圖中建立門店的稽核結果。騰訊地圖稽核週期為 3 個工作日，請在期間內留意稽核結果事件推送。提交後未當即返回事件推送即為稽核中，請耐心等待。

## 新增門店

```php
$app->store->create($baseInfo);
```

示例：

```php
$baseInfo = [
    "poi_id"             => "",
    "map_poi_id"         => "2880741500279549033",
    "pic_list"           => "['list' => ['http://mmbiz.qpic.cn/mmbiz_jpg/tW66AWvE2K4EJxIYOVpiaGOkfg0iayibiaP2xHOChvbmKQD5uh8ymibbEKlTTPmjTdQ8ia43sULLeG1pT2psOfPic4kTw/0?wx_fmt=jpeg']]",
    "contract_phone"     => "1111222222",
    "credential"         => "22883878-0",
    "qualification_list" => "RTZgKZ386yFn5kQSWLTxe4bqxwgzGBjs3OE02cg9CVQk1wRVE3c8fjUFX7jvpi-P"
];
```

> - `$baseInfo`: 門店相關資訊。`pic_list` 門店圖片，可傳多張圖片 `pic_list`

> 事件推送 - 建立門店的稽核結果

## 更新門店資訊

```php
$app->store->update($baseInfo);
```

> - `$baseInfo`: 門店相關資訊。

> 果要更新門店的圖片，實際相當於走一次重新為門店新增圖片的流程，之前的舊圖片會全部廢棄。並且如果重新新增的圖片中有與之前舊圖片相同的，此時這個圖片不需要重新稽核。
