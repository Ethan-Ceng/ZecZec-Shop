# 語義理解

> 貌似此介面已經下線，呼叫無正確返回值

+ `query($keyword, $categories, $optional = [])` 語義理解:

  + `$keyword` 為關鍵字
  + `$categories` 需要使用的服務型別，多個用 “,” 隔開字串，不能為空;
  + `$optional` 為其它屬性：
    + `latitude`  `float`  緯度座標，與經度同時傳入；與城市二選一傳入
    + `longitude`  `float`  經度座標，與緯度同時傳入；與城市二選一傳入
    + `city`   `string`  城市名稱，與經緯度二選一傳入
    + `region` `string`  區域名稱，在城市存在的情況下可省；與經緯度二選一傳入
    + `uid`  `string` 使用者唯一id（非開發者id），使用者區分公眾號下的不同使用者（建議填入使用者openid），如果為空，則無法使用上下文理解功能。appid和uid同時存在的情況下，才可以使用上下文理解功能。

> 注：單類別意圖比較明確，識別的覆蓋率比較大，所以如果只要使用特定某個類別，建議將 category 只設置為該類別。

示例：

```php
$result = $app->semantic->query('查一下明天從北京到上海的南航機票', "flight,hotel", array('city' => '北京', 'uid' => '123456'));
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