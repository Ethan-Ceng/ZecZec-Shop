# 使用者標籤


使用者標籤的使用就非常簡單了，基本的增刪改查。

## 獲取例項

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$tag = $app->user_tag; // $user['user_tag']
```

## API

### 獲取所有標籤

```php
$tag->lists();
```

example:

```php
$tags = $tag->lists();

// {
//     "tags": [
//         {
//             "id": 0,
//             "name": "標籤1",
//             "count": 72596
//         },
//         {
//             "id": 1,
//             "name": "標籤2",
//             "count": 36
//         },
//         ...
//     ]
// }

var_dump($tags->tags[0]['name']) // “標籤1”
```

### 建立標籤

```php
$tag->create($name);
```

example:

```php
$tag->create('測試標籤');
```

### 修改標籤資訊

```php
$tag->update($tagId, $name);
```

example:

```php
$tag->update(12, "新的名稱");
```

### 刪除標籤

```php
$tag->delete($tagId);
```

example:

```php
$tag->delete($tagId);
```

### 獲取指定 openid 使用者身上的標籤

```php
$userTags = $tag->userTags($openId);
//
// {
//     "tagid_list":["標籤1","標籤2"]
// }
```

### 獲取標籤下粉絲列表

```php
$tag->usersOfTag($tagId, $nextOpenId = '');
// $nextOpenId：第一個拉取的OPENID，不填預設從頭開始拉取

// {
//   "count":2,//這次獲取的粉絲數量
//   "data":{//粉絲列表
//      "openid":[
//          "ocYxcuAEy30bX0NXmGn4ypqx3tI0",
//          "ocYxcuBt0mRugKZ7tGAHPnUaOW7Y"
//      ]
//   },
//   "next_openid":"ocYxcuBt0mRugKZ7tGAHPnUaOW7Y"//拉取列表最後一個使用者的openid
// }
```

### 批次為使用者打標籤

```php
$openIds = [$openId1, $openId2, ...];
$tag->batchTagUsers($openIds, $tagId);
```


### 批次為使用者取消標籤

```php
$openIds = [$openId1, $openId2, ...];
$tag->batchUntagUsers($openIds, $tagId);
```

關於使用者管理請參考微信官方文件：http://mp.weixin.qq.com/wiki/ `使用者管理` 章節。
