# OA

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx',
    //...
];

$app = Factory::work($config);
```

## 獲取打卡資料

API:

```php
mixed checkinRecords(int $startTime, int $endTime, array $userList, int $type = 3)
```

> $type: 打卡型別 1：上下班打卡；2：外出打卡；3：全部打卡

```php
// 全部打卡資料
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"]);

// 獲取上下班打卡
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"], 1);

// 獲取外出打卡
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"], 2);
```

## 獲取審批資料

API:

```php
mixed approvalRecords(int $startTime, int $endTime, int $nextNumber = null)
```

> $nextNumber: 第一個拉取的審批單號，不填從該時間段的第一個審批單拉取

```php
$app->oa->approvalRecords(1492617600, 1492790400);

// 指定第一個拉取的審批單號，不填從該時間段的第一個審批單拉取
$app->oa->approvalRecords(1492617600, 1492790400, '201704240001');
```
