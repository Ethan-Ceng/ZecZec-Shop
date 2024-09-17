# 使用者組


使用者組的使用就非常簡單了，基本的增刪改查。

## 獲取例項

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$group = $app->user_group; // $user['user_group']
```

## API

### 獲取所有分組

```php
$group->lists();
```

example:

```php
$groups = $group->lists();

// {
//     "groups": [
//         {
//             "id": 0,
//             "name": "未分組",
//             "count": 72596
//         },
//         {
//             "id": 1,
//             "name": "黑名單",
//             "count": 36
//         },
//         ...
//     ]
// }

var_dump($groups->groups[0]['name']) // “未分組”
```

### 建立分組

```php
$group->create($name);
```

example:

```php
$group->create($name);
```

### 修改分組資訊

```php
$group->update($groupId, $name);
```

example:

```php
$group->update($groupId, "新的組名");
```

### 刪除分組

```php
$group->delete($groupId);
```

example:

```php
$group->delete($groupId);
```

### 移動單個使用者到指定分組

```php
$group->moveUser($openId, $groupId);
```

example:

```php
$group->moveUser($openId, $groupId);
```

### 批次移動使用者到指定分組

```php
$group->moveUsers(array $openIds, $groupId);
```

example:

```php
$openIds = [$openId1, $openId2, $openId3 ...];
$group->moveUsers($openIds, $groupId);
```

關於使用者管理請參考微信官方文件：http://mp.weixin.qq.com/wiki/ `使用者管理` 章節。
