# 語義理解


微信開放平臺語義理解介面呼叫（http請求）簡單方便，使用者無需掌握語義理解及相關技術，只需根據自己的產品特點，選擇相應的服務即可搭建一套智慧語義服務。

## 獲取例項

```php
<?php

// ... 前面部分省略

$app = new Application($options);

$semantic = $app->semantic;
```

## API

+ `query($keyword, $categories, $other)` 語義理解:

  + `$keyword` 為關鍵字
  + `$categories` 需要使用的服務型別，陣列或者多個用 “，” 隔開字元呂，不能為空;
  + `$other` 為其它屬性：
    + `latitude`  `float`  緯度座標，與經度同時傳入；與城市二選一傳入
    + `longitude`  `float`  經度座標，與緯度同時傳入；與城市二選一傳入
    + `city`   `string`  城市名稱，與經緯度二選一傳入
    + `region` `string`  區域名稱，在城市存在的情況下可省；與經緯度二選一傳入
    + `uid`  `string` 使用者唯一id（非開發者id），使用者區分公眾號下的不同使用者（建議填入使用者openid），如果為空，則無法使用上下文理解功能。appid和uid同時存在的情況下，才可以使用上下文理解功能。

> 注：單類別意圖比較明確，識別的覆蓋率比較大，所以如果只要使用特定某個類別，建議將category只設置為該類別。

example:

```php
$result = $semantic->query('查一下明天從北京到上海的南航機票', "flight,hotel", array('city' => '北京', 'uid' => '123456'));
// 查詢引數：
// {
//    "query":"查一下明天從北京到上海的南航機票",
//    "city":"北京",
//    "category": "flight,hotel",
//    "appid":"wxaaaaaaaaaaaaaaaa",
//    "uid":"123456"
// }
```
返回值示例：

```json
{
    "errcode":0,
    "query":"查一下明天從北京到上海的南航機票",
    "type":"flight",
    "semantic":{
        "details":{
            "start_loc":{
                "type":"LOC_CITY",
                "city":"北京市",
                "city_simple":"北京",
                "loc_ori":"北京"
                },
            "end_loc": {
                "type":"LOC_CITY",
                "city":"上海市",
                "city_simple":"上海",
                "loc_ori":"上海"
              },
            "start_date": {
                "type":"DT_ORI",
                "date":"2014-03-05",
                "date_ori":"明天"
              },
           "airline":"中國南方航空公司"
        },
    "intent":"SEARCH"
}
```

更多詳細內容與協議說明，請檢視 [微信官方文件](http://mp.weixin.qq.com/wiki/)