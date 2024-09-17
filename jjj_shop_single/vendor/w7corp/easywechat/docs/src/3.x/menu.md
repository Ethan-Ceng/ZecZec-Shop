# 自定義選單


3.0 的選單元件有所簡化，相比 2.x 版本變化如下：

- 去除 `MenuItem` 類，建立選單直接使用陣列不再支援 `callback` 與 `MenuItem` 類似的繁雜的方式
- `set()` 方法與 `addConditional()` 合併為一個方法 `add()`
- `get()` 改名為 `all()`
- `delete()` 與 `deleteById()` 合併為 `destroy()`
- 所有 API 的返回值（非呼叫失敗情況）均為官方文件原樣返回（Collection形式），不再取返回值中部分 `key` 返回。
  > 例如原來的 `get()` 方法，官方返回的陣列為: `{ menu: [...]}`，SDK 取了其中的 `menu` 內容作為返回值，在 3.0 後將直接整體返回。

## 獲取選單模組例項

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$menu = $app->menu;
```

## API 列表

### 讀取（查詢）已設定選單

微信的選單讀取有兩個不同的方式：

一種叫 **[查詢選單](http://mp.weixin.qq.com/wiki/5/f287d1a5b78a35a8884326312ac3e4ed.html)**，在 SDK 中以 `all()` 方法來呼叫：

```php
$menus = $menu->all();
```

另外一種叫 **[獲取自定義選單](http://mp.weixin.qq.com/wiki/14/293d0cb8de95e916d1216a33fcb81fd6.html)**，使用 `current()` 方法來呼叫：

```php
$menus = $menu->current();
```

### 新增選單

#### 新增普通選單

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
$menu->add($buttons);
```

以上將會建立一個普通選單。

#### 添加個性化選單

與建立普通選單不同的是，需要在 `add()` 方法中將個性化匹配規則作為第二個引數傳進去：

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
$menu->add($buttons, $matchRule);
```

### 刪除選單

有兩種刪除方式，一種是**全部刪除**，另外一種是**根據選單 ID 來刪除**(刪除個性化選單時用，ID 從查詢介面獲取)：

```php
$menu->destroy(); // 全部
$menu->destroy($menuId);
```

### 測試個性化選單

```php
$menus = $menu->test($userId);
```

> `$userId` 可以是粉絲的 OpenID，也可以是粉絲的微訊號。

返回 `$menus` 與指定的 `$userId` 匹配的選單項。

更多關於微信自定義選單 API 請參考： http://mp.weixin.qq.com/wiki `自定義選單` 章節。
