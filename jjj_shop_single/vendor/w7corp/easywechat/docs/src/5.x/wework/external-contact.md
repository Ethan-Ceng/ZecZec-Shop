# 客戶聯絡

## 獲取例項

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx',
    ...
];

$app = Factory::work($config);

// 基礎介面
$app->external_contact;

// 「聯絡我」
$app->contact_way;

// 訊息管理
$app->external_contact_message;

// 資料統計
$app->external_contact_statistics;
```

## 基礎介面

### 獲取配置了客戶聯絡功能的成員列表

```php
$app->external_contact->getFollowUsers();
```

### 獲取外部聯絡人列表

```php
$userId = 'zhangsan';

$app->external_contact->list($userId);
```

### 獲取外部聯絡人詳情

```php
$externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACH71w';

$app->external_contact->get($externalUserId);
```

### 批次獲取客戶詳情

```php
$userId = 'zhangsai';
$cursor = '';
$limit = 100;

$app->external_contact->batchGetByUser(string $userId, string $cursor, int $limit);
```


### 修改客戶備註資訊

```php
$data  = [
    "userid"=>'員工id',
    "external_userid"=>'客戶id',
    "remark"=> '新備註',
    "description"=>'新描述',
    "remark_company"=>'新公司',
    "remark_mobiles"=>[ '電話1','電話2'],
    "remark_pic_mediaid"=> "MEDIAID"
];

$app->external_contact->remark($data);
```



### 獲取離職成員的客戶列表

```php
$pageId = 0;
$pageSize = 1000;
$app->external_contact->getUnassigned($pageId, $pageSize);
```

### 分配成員的客戶(離職或在職)

```php
$externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACH71w';
$handoverUserId = 'zhangsan';
$takeoverUserId = 'lisi';
$transferSuccessMessage = '您好，您的服務已升級，後續將由我的同事張三@騰訊接替我的工作，繼續為您服務。'; //不填則使用預設文案

$app->external_contact->transfer($externalUserId, $handoverUserId, $takeoverUserId, $transferSuccessMessage);
```


### 離職成員的群再分配

```php
$chatIds = ['群聊id1', '群聊id2'];
$takeoverUserId = '接替群主userid';

$app->external_contact->transferGroupChat($chatIds, $takeoverUserId);
```



### 查詢客戶接替結果

```php
$externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACH71w';
$handoverUserId = 'zhangsan';
$takeoverUserId = 'lisi';

$app->external_contact->getTransferResult($externalUserId, $handoverUserId, $takeoverUserId);
```


## 客戶群管理

### 獲取客戶群列表

```php
$params = [
    "status_filter" => 0,
    "owner_filter" => [
        "userid_list" => ["abel"],
        "partyid_list" => [7]
    ],
    "offset" => 0,
    "limit" => 100
];

$app->external_contact->getGroupChats(array $params);
```

### 獲取客戶群詳情

```php
$chatId = 'wrOgQhDgAAMYQiS5ol9G7gK9JVAAAA';

$app->external_contact->getGroupChat(string $chatId);
```
## 客戶朋友圈


### 獲取企業全部的發表列表
```php
$params = [
    'start_time' => 1605000000,
    'end_time' => 1605172726,
    'creator' => 'zhangshan',
    'filter_type' => 1,
    'cursor' => 'CURSOR',
    'limit' => 10
];

$app->external_contact_moment->list(array $params);
```

### 獲取客戶朋友圈企業發表的列表

```php
$momentId = 'momxxx';
$cursor = 'CURSOR';
$limit = 10;

$app->external_contact_moment->getTasks(string $momentId, string $cursor, int $limit);
```

### 獲取客戶朋友圈發表時選擇的可見範圍

```php
$momentId = 'momxxx';
$userId = 'xxx';
$cursor = 'CURSOR';
$limit = 10;

$app->external_contact_moment->getCustomers(string $momentId, string $userId, string $cursor, int $limit);
```

### 獲取客戶朋友圈發表後的可見客戶列表

```php
$momentId = 'momxxx';
$userId = 'xxx';
$cursor = 'CURSOR';
$limit = 10;

$app->external_contact_moment->getSendResult(string $momentId, string $userId, string $cursor, int $limit);
```

### 獲取客戶朋友圈的互動資料

```php
$momentId = 'momxxx';
$userId = 'xxx';

$app->external_contact_moment->getComments(string $momentId, string $userId);
```

## 客戶標籤管理

> 注意: 對於新增/刪除/編輯企業客戶標籤介面，目前僅支援使用“客戶聯絡”secret所獲取的accesstoken來呼叫。
> 原文: https://work.weixin.qq.com/api/doc/90000/90135/92117

### 獲取企業標籤庫

```php
$tagIds = [
    "etXXXXXXXXXX",
    "etYYYYYYYYYY"
];

$app->external_contact->getCorpTags(array $tagIds=[]);
```

### 新增企業客戶標籤

```php
$params = [
    "group_id" => "GROUP_ID",
    "group_name" => "GROUP_NAME",
    "order" => 1,
    "tag" => [
        [
            "name" => "TAG_NAME_1",
            "order" => 1
        ],
        [
            "name" => "TAG_NAME_2",
            "order" => 2
        ]
    ]
];

$app->external_contact->addCorpTag(array $params);
```


### 編輯企業客戶標籤

```php
$id = 'TAG_ID';
$name = 'NEW_TAG_NAME';
$order = 1;

$app->external_contact->updateCorpTag(string $id, string $name, int $order = 1);
```



### 刪除企業客戶標籤

```php
$tagId = [
    'TAG_ID_1',
    'TAG_ID_2'
];
$groupId = [
    'GROUP_ID_1',
    'GROUP_ID_2'
];

$app->external_contact->deleteCorpTag(array $tagId,array $groupId);
```



### 編輯客戶企業標籤

```php
$params = [
    "userid" => "zhangsan",
    "external_userid" => "woAJ2GCAAAd1NPGHKSD4wKmE8Aabj9AAA",
    "add_tag" => ["TAGID1", "TAGID2"],
    "remove_tag" => ["TAGID3", "TAGID4"]
];

$app->external_contact->markTags(array $params);
```

## 配置客戶聯絡「聯絡我」方式

>  注意：
> 1. 透過API新增的「聯絡我」不會在管理端進行展示。
> 2. 每個企業可透過API最多配置10萬個「聯絡我」。
> 3. 截止 2019-06-21 官方文件沒有提供獲取所有「聯絡我」列表的介面，請開發者注意自行保管處理 configId，避免無法溯源。

### 增加「聯絡我」方式

```php
$type = 1;
$scene = 1;
$config = [
   'style' => 1,
   'remark' => '渠道客戶',
   'skip_verify' => true,
   'state' => 'teststate',
   'user' => ['UserID1', 'UserID2', 'UserID3'],
];

$app->contact_way->create($type, $scene, $config);

// {
//   "errcode": 0,
//   "errmsg": "ok",
//   "config_id":"42b34949e138eb6e027c123cba77fad7"　　
// }
```

### 獲取「聯絡我」方式

```php
$configId = '42b34949e138eb6e027c123cba77fad7';

$app->contact_way->get($configId);
```

### 更新「聯絡我」方式

```php
$configId = '42b34949e138eb6e027c123cba77fad7';

$config = [
   'style' => 1,
   'remark' => '渠道客戶2',
   'skip_verify' => true,
   'state' => 'teststate2',
   'user' => ['UserID4', 'UserID5', 'UserID6'],
];

$app->contact_way->update($configId, $config);
```

### 刪除「聯絡我」方式

```php
$configId = '42b34949e138eb6e027c123cba77fad7';

$app->contact_way->delete($configId);
```

## 訊息管理

### 新增企業群發訊息模板

```php
$msg = [
    'external_userid' => [
        'woAJ2GCAAAXtWyujaWJHDDGi0mACas1w',
        'wmqfasd1e1927831291723123109r712',
    ],
    'sender' => 'zhangsan',
    'text' => [
        'content' => '文字訊息內容',
    ],
    'image' => [
        'media_id' => 'MEDIA_ID',
    ],
    'link' => [
        'title' => '訊息標題',
        'picurl' => 'https://example.pic.com/path',
        'desc' => '訊息描述',
        'url' => 'https://example.link.com/path',
    ],
    'miniprogram' => [
        'title' => '訊息標題',
        'pic_media_id' => 'MEDIA_ID',
        'appid' => 'wx8bd80126147df384',
        'page' => '/path/index',
    ],
];

$app->external_contact_message->submit($msg);

// {
//     "errcode": 0,
//     "errmsg": "ok",
//     "fail_list":["wmqfasd1e19278asdasdasd"],
//     "msgid":"msgGCAAAXtWyujaWJHDDGi0mACas1w"
// }
```

### 獲取企業群發訊息傳送結果

```php
$msgId = 'msgGCAAAXtWyujaWJHDDGi0mACas1w';

$app->external_contact_message->get($msgId);
```

### 傳送新客戶歡迎語

```php
$welcomeCode = 'WELCOMECODE';

$msg = [
    'text' => [
        'content' => '文字訊息內容',
    ],
    'image' => [
        'media_id' => 'MEDIA_ID',
    ],
    'link' => [
        'title' => '訊息標題',
        'picurl' => 'https://example.pic.com/path',
        'desc' => '訊息描述',
        'url' => 'https://example.link.com/path',
    ],
    'miniprogram' => [
        'title' => '訊息標題',
        'pic_media_id' => 'MEDIA_ID',
        'appid' => 'wx8bd80126147df384',
        'page' => '/path/index',
    ],
];

$app->external_contact_message->sendWelcome($welcomeCode, $msg);
```


## 資料統計

###  獲取「聯絡客戶統計」資料

```php
$userIds = [
    'zhangsan',
    'lisi'
];
$partyIds = [
    'PARTY_ID_1',
    'PARTY_ID_2'
];
$from = 1536508800;
$to = 1536940800;

$app->external_contact_statistics->userBehavior($userIds, $from, $to, $partyIds);
```

###  獲取「群聊資料統計」資料.

- 按群主聚合的方式

```php
$params = [
    'day_begin_time' => 1600272000,
    'day_end_time' => 1600444800,
    'owner_filter' => [
        'userid_list' => ['zhangsan']
    ],
    'order_by' => 2,
    'order_asc' => 0,
    'offset' => 0,
    'limit' => 1000
];

$app->external_contact_statistics->groupChatStatistic(array $params);
```

- 按自然日聚合的方式

```php
$dayBeginTime = 1600272000;
$dayEndTime = 1600444800;
$userIds = ['userid1', 'userid2'];

$app->external_contact_statistics->groupChatStatisticGroupByDay(int $dayBeginTime, int $dayEndTime, array $userIds);
```