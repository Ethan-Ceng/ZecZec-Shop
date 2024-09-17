# 資料統計與分析

獲取小程式概況趨勢：

```php
$app->data_cube->summaryTrend('20170313', '20170313')
```
開始日期與結束日期的格式為 yyyymmdd。

## API

>  - `summaryTrend(string $from, string $to);` 概況趨勢
>  - `dailyVisitTrend(string $from, string $to);` 訪問日趨勢
>  - `weeklyVisitTrend(string $from, string $to);` 訪問周趨勢
>  - `monthlyVisitTrend(string $from, string $to);` 訪問月趨勢
>  - `visitDistribution(string $from, string $to);` 訪問分佈
>  - `dailyRetainInfo(string $from, string $to);` 訪問日留存
>  - `weeklyRetainInfo(string $from, string $to);` 訪問周留存
>  - `monthlyRetainInfo(string $from, string $to);` 訪問月留存
>  - `visitPage(string $from, string $to);` 訪問頁面
>  - `userPortrait(string $from, string $to);` 使用者畫像分佈資料

