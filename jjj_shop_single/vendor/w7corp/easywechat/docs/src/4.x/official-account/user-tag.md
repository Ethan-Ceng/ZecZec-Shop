# 使用者標籤

## 獲取所有標籤

```php
$app->user_tag->list();
```

示例：

```php
$tags = $app->user_tag->list();

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
```

## 建立標籤

```php
$app->user_tag->create($name);
```

示例：

```php
$app->user_tag->create('測試標籤');
```

## 修改標籤資訊

```php
$app->user_tag->update($tagId, $name);
```

示例：

```php
$app->user_tag->update(12, "新的名稱");
```

## 刪除標籤

```php
$app->user_tag->delete($tagId);
```

## 獲取指定 openid 使用者所屬的標籤

```php
$userTags = $app->user_tag->userTags($openId);
//
// {
//     "tagid_list":["標籤1","標籤2"]
// }
```

## 獲取標籤下使用者列表

```php
$app->user_tag->usersOfTag($tagId, $nextOpenId = '');
// $nextOpenId：第一個拉取的OPENID，不填預設從頭開始拉取

// {
//   "count":2, // 這次獲取的粉絲數量
//   "data":{ // 粉絲列表
//      "openid":[
//          "ocYxcuAEy30bX0NXmGn4ypqx3tI0",
//          "ocYxcuBt0mRugKZ7tGAHPnUaOW7Y"
//      ]
//   },
//   "next_openid":"ocYxcuBt0mRugKZ7tGAHPnUaOW7Y"//拉取列表最後一個使用者的openid
// }
```

## 批次為使用者新增標籤

```php
$openIds = [$openId1, $openId2, ...];
$app->user_tag->tagUsers($openIds, $tagId);
```


## 批次為使用者移除標籤

```php
$openIds = [$openId1, $openId2, ...];
$app->user_tag->untagUsers($openIds, $tagId);
```
