# OA

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx',
    //...
];

$app = Factory::work($config);
```

## 打卡

### 獲取企業所有打卡規則

```php
$app->oa->corpCheckinRules();
```

### 獲取員工打卡規則

```php
$app->oa->checkinRules(int $datetime, array $userList);
```

### 獲取打卡記錄資料

> $type: 打卡型別 1：上下班打卡；2：外出打卡；3：全部打卡

```php

// 全部打卡資料
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"]);

// 獲取上下班打卡
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"], 1);

// 獲取外出打卡
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"], 2);

```

### 獲取打卡日報資料

```php
$app->oa->checkinDayData(int $startTime, int $endTime, array $userids);
```

### 獲取打卡月報資料

```php
$app->oa->checkinMonthData(int $startTime, int $endTime, array $userids);
```

### 獲取打卡人員排班資訊

```php
 $params = [
            'groupid' => 226,
            'items' => [
                [
                    'userid' => 'james',
                    'day' => 5,
                    'schedule_id' => 234
                ]
            ],
            'yearmonth' => 202012
        ];
$app->oa->setCheckinSchedus(array $params);
```

### 為打卡人員排班

```php
$app->oa->checkinSchedus(int $startTime, int $endTime, array $userids);
```

### 錄入打卡人員人臉資訊

```php
$app->oa->addCheckinUserface(string $userid, string $userface)
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
