# 自定義選單

## 讀取（查詢）已設定選單


```php
$list = $app->menu->list();
```

## 獲取當前選單

```php
$current = $app->menu->current();
```

## 新增選單

### 新增普通選單

```php
$buttons = [
    [
        "type" => "click",
        "name" => "今日歌曲",
        "key"  => "V1001_TODAY_MUSIC"
    ],
    [
        "name"       => "選單",
        "sub_button" => [
            [
                "type" => "view",
                "name" => "搜尋",
                "url"  => "http://www.soso.com/"
            ],
            [
                "type" => "view",
                "name" => "影片",
                "url"  => "http://v.qq.com/"
            ],
            [
                "type" => "click",
                "name" => "贊一下我們",
                "key" => "V1001_GOOD"
            ],
        ],
    ],
];
$app->menu->create($buttons);
```

以上將會建立一個普通選單。

### 添加個性化選單

與建立普通選單不同的是，需要在 `create()` 方法中將個性化匹配規則作為第二個引數傳進去：

```php
$buttons = [
    // ...
];
$matchRule = [
    "tag_id" => "2",
    "sex" => "1",
    "country" => "中國",
    "province" => "廣東",
    "city" => "廣州",
    "client_platform_type" => "2",
    "language" => "zh_CN"
];
$app->menu->create($buttons, $matchRule);
```

## 刪除選單

有兩種刪除方式，一種是**全部刪除**，另外一種是**根據選單 ID 來刪除**(刪除個性化選單時用，ID 從查詢介面獲取)：

```php
$app->menu->delete(); // 全部
$app->menu->delete($menuId);
```

## 測試個性化選單

```php
$app->menu->match($userId);
```

> `$userId` 可以是粉絲的 OpenID，也可以是粉絲的微訊號。

返回 `$menu` 與指定的 `$userId` 匹配的選單項。
