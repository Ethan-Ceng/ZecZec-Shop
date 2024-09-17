# 資料統計與分析

透過資料介面，開發者可以獲取與公眾平臺官網統計模組類似但更靈活的資料，還可根據需要進行高階處理。

>
> 1. 介面側的公眾號資料的資料庫中僅儲存了 **2014年12月1日之後**的資料，將查詢不到在此之前的日期，即使有查到，也是不可信的髒資料；
> 2. 請開發者在呼叫介面獲取資料後，將資料儲存在自身資料庫中，即加快下次使用者的訪問速度，也降低了微信側介面呼叫的不必要損耗。
> 3. 額外注意，獲取圖文群發每日資料介面的結果中，只有**中間頁閱讀人數+原文頁閱讀人數+分享轉發人數+分享轉發次數+收藏次數 >=3** 的結果才會得到統計，過小的閱讀量的圖文訊息無法統計。

## 示例

```php
$userSummary = $app->data_cube->userSummary('2014-12-07', '2014-12-08');

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

## API

    $from   示例： `2014-02-13` 獲取資料的起始日期
    $to     示例： `2014-02-18` 獲取資料的結束日期，`$to`允許設定的最大值為昨日

    `$from` 和 `$to` 的差值需小於 “最大時間跨度”（比如最大時間跨度為 1 時，`$from` 和 `$to` 的差值只能為 0，才能小於 1 ），否則會報錯

+ `array userSummary(string $from, string $to)` 獲取使用者增減資料, 最大時間跨度：**7**;
+ `array userCumulate(string $from, string $to)` 獲取累計使用者資料, 最大時間跨度：**7**;
+ `array articleSummary(string $from, string $to)` 獲取圖文群發每日資料, 最大時間跨度：**1**;
+ `array articleTotal(string $from, string $to)` 獲取圖文群發總資料, 最大時間跨度：**1**;
+ `array userReadSummary(string $from, string $to)` 獲取圖文統計資料, 最大時間跨度：**3**;
+ `array userReadHourly(string $from, string $to)` 獲取圖文統計分時資料, 最大時間跨度：**1**;
+ `array userShareSummary(string $from, string $to)` 獲取圖文分享轉發資料, 最大時間跨度：**7**;
+ `array userShareHourly(string $from, string $to)` 獲取圖文分享轉發分時資料, 最大時間跨度：**1**;
+ `array upstreamMessageSummary(string $from, string $to)` 獲取訊息傳送概況資料, 最大時間跨度：**7**;
+ `array upstreamMessageHourly(string $from, string $to)` 獲取訊息傳送分時資料, 最大時間跨度：**1**;
+ `array upstreamMessageWeekly(string $from, string $to)` 獲取訊息傳送週數據, 最大時間跨度：**30**;
+ `array upstreamMessageMonthly(string $from, string $to)` 獲取訊息傳送月資料, 最大時間跨度：**30**;
+ `array upstreamMessageDistSummary(string $from, string $to)` 獲取訊息傳送分佈資料, 最大時間跨度：**15**;
+ `array upstreamMessageDistWeekly(string $from, string $to)` 獲取訊息傳送分佈週數據, 最大時間跨度：**30**;
+ `array upstreamMessageDistMonthly(string $from, string $to)` 獲取訊息傳送分佈月資料, 最大時間跨度：**30**;
+ `array interfaceSummary(string $from, string $to)` 獲取介面分析資料, 最大時間跨度：**30**;
+ `array interfaceSummaryHourly(string $from, string $to)` 獲取介面分析分時資料, 最大時間跨度：**1**;
+ `array cardSummary(string $from, string $to, int $condSource = 0)` 獲取普通卡券分析分時資料, 最大時間跨度：**1**;
+ `array freeCardSummary(string $from, string $to, int $condSource = 0, string $cardId = '')` 獲取免費券分析分時資料, 最大時間跨度：**1**;
+ `array memberCardSummary(string $from, string $to, int $condSource = 0)` 獲取會員卡分析分時資料, 最大時間跨度：**1**;
