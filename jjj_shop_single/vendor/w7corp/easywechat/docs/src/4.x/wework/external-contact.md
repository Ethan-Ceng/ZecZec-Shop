# 外部聯絡人管理

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


### 獲取離職成員的客戶列表

```php
$pageId = 0;
$pageSize = 1000;
$app->external_contact->getUnassigned($pageId, $pageSize);
```

### 離職成員的外部聯絡人再分配

```php
$externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACH71w';
$handoverUserId = 'zhangsan';
$takeoverUserId = 'lisi';

$app->external_contact->transfer($externalUserId, $handoverUserId, $takeoverUserId);
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

### 獲取員工行為資料

```php
$userIds = [
    'zhangsan',
    'lisi'
];

$from = 1536508800;
$to = 1536940800;

$app->external_contact_statistics->userBehavior($userIds, $from, $to);
```


