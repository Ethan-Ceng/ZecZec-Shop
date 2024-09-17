# 會話內容存檔

> 企業需要使用會話內容存檔應用secret所獲取的accesstoken來呼叫。
> 原文: https://work.weixin.qq.com/api/doc/90000/90135/91614


### 會話存檔相關SDK

- [wework-msgaudit](https://github.com/aa24615/wework-msgaudit)


### 獲取會話內容存檔開啟成員列表
```php
$type = 1;

$app->msg_audit->getPermitUsers(string $type);
```

### 獲取會話同意情況

- 單聊

```php
$info = [
    [
        "userid" => "XuJinSheng1",
        "exteranalopenid" => "wmeDKaCQAAGd9oGiQWxVsAKwV2HxNAAA1"
    ],
    [
        "userid" => "XuJinSheng2",
        "exteranalopenid" => "wmeDKaCQAAGd9oGiQWxVsAKwV2HxNAAA2"
    ],
    [
        "userid" => "XuJinSheng3",
        "exteranalopenid" => "wmeDKaCQAAGd9oGiQWxVsAKwV2HxNAAA3"
    ]
];

$app->msg_audit->getSingleAgreeStatus(array $info);
```

- 群聊

```php
$roomId = 'wrjc7bDwAASxc8tZvBErFE02BtPWyAAA';

$app->msg_audit->getRoomAgreeStatus(string $roomId);
```

### 獲取會話內容存檔內部群資訊

```php
$roomId = 'wrjc7bDwAASxc8tZvBErFE02BtPWyAAA';

$app->msg_audit->getRoom(string $roomId);
```



