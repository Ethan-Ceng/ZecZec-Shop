# 使用者


使用者資訊的獲取是微信開發中比較常用的一個功能了，以下所有的使用者資訊的獲取與更新，都是**基於微信的 `openid` 的，並且是已關注當前賬號的**，其它情況可能無法正常使用。

## 獲取例項

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$userService = $app->user;
```

## API 列表

### 獲取使用者資訊

```php
$userService->get($openId);
$userService->batchGet($openIds);
```

獲取單個：

```php
$user = $userService->get($openId);

echo $user->nickname; // or $user['nickname']
```

獲取多個：

```php
$users = $userService->batchGet([$openId1, $openId2, ...]);
```

### 獲取使用者列表

```php
$userService->lists($nextOpenId = null);  // $nextOpenId 可選
```

 example:

 ```php
 $users = $userService->lists();

 // result
 {
  "total": 2,
  "count": 2,
  "data": {
    "openid": [
      "",
      "OPENID1",
      "OPENID2"
    ]
  },
  "next_openid": "NEXT_OPENID"
}

$users->total; // 2
 ```

### 修改使用者備註

```php
$userService->remark($openId, $remark); // 成功返回boolean
```

example:

```php
$userService->remark($openId, "殭屍粉");
```

### 獲取使用者所屬使用者組ID

```php
$userService->group($openId);
```

example:

```php
$userGroupId = $userService->group($openId);
```

## 其它

- [使用者標籤](user-tag.html)
- [使用者分組](user-group.html)

關於使用者管理請參考微信官方文件：http://mp.weixin.qq.com/wiki/ `使用者管理` 章節。