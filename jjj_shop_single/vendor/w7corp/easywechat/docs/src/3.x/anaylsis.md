# 資料統計與分析


透過資料介面，開發者可以獲取與公眾平臺官網統計模組類似但更靈活的資料，還可根據需要進行高階處理。

> 1. 介面側的公眾號資料的資料庫中僅儲存了 **2014年12月1日之後**的資料，將查詢不到在此之前的日期，即使有查到，也是不可信的髒資料；
> 2. 請開發者在呼叫介面獲取資料後，將資料儲存在自身資料庫中，即加快下次使用者的訪問速度，也降低了微信側介面呼叫的不必要損耗。
> 3. 額外注意，獲取圖文群發每日資料介面的結果中，只有**中間頁閱讀人數+原文頁閱讀人數+分享轉發人數+分享轉發次數+收藏次數 >=3** 的結果才會得到統計，過小的閱讀量的圖文訊息無法統計。

### 獲取例項

```php
<?php

use EasyWeChat\Foundation\Application;

//...

$app = new Application($options);
$stats = $app->stats;
```

## API

    $from   example: `2014-02-13` 獲取資料的起始日期
    $to     example: `2014-02-18` 獲取資料的結束日期，`$to`允許設定的最大值為昨日

    `$from` 和 `$to` 的差值需小於 “最大時間跨度”（比如最大時間跨度為 1 時，`$from` 和 `$to` 的差值只能為 0，才能小於 1 ），否則會報錯

+ `array userSummary($from, $to)` 獲取使用者增減資料, 最大時間跨度：**7**;
+ `array userCumulate($from, $to)` 獲取累計使用者資料, 最大時間跨度：**7**;
+ `array articleSummary($from, $to)` 獲取圖文群發每日資料, 最大時間跨度：**1**;
+ `array articleTotal($from, $to)` 獲取圖文群發總資料, 最大時間跨度：**1**;
+ `array userReadSummary($from, $to)` 獲取圖文統計資料, 最大時間跨度：**3**;
+ `array userReadHourly($from, $to)` 獲取圖文統計分時資料, 最大時間跨度：**1**;
+ `array userShareSummary($from, $to)` 獲取圖文分享轉發資料, 最大時間跨度：**7**;
+ `array userShareHourly($from, $to)` 獲取圖文分享轉發分時資料, 最大時間跨度：**1**;
+ `array upstreamMessageSummary($from, $to)` 獲取訊息傳送概況資料, 最大時間跨度：**7**;
+ `array upstreamMessageHourly($from, $to)` 獲取訊息傳送分時資料, 最大時間跨度：**1**;
+ `array upstreamMessageWeekly($from, $to)` 獲取訊息傳送週數據, 最大時間跨度：**30**;
+ `array upstreamMessageMonthly($from, $to)` 獲取訊息傳送月資料, 最大時間跨度：**30**;
+ `array upstreamMessageDistSummary($from, $to)` 獲取訊息傳送分佈資料, 最大時間跨度：**15**;
+ `array upstreamMessageDistWeekly($from, $to)` 獲取訊息傳送分佈週數據, 最大時間跨度：**30**;
+ `array upstreamMessageDistMonthly($from, $to)` 獲取訊息傳送分佈月資料, 最大時間跨度：**30**;
+ `array interfaceSummary($from, $to)` 獲取介面分析資料, 最大時間跨度：**30**;
+ `array interfaceSummaryHourly($from, $to)` 獲取介面分析分時資料, 最大時間跨度：**1**;

example:

```php
$userSummary = $stats->userSummary('2014-12-07', '2014-12-08');

var_dump($userSummary);
//
//[
//    {
//        "ref_date": "2014-12-07",
//        "user_source": 0,
//        "new_user": 0,
//        "cancel_user": 0
//    }
//    //後續還有ref_date在begin_date和end_date之間的資料
// ]

```

更多詳細內容與協議說明，請檢視微信官方文件：http://mp.weixin.qq.com/wiki/ **資料統計** 章節
