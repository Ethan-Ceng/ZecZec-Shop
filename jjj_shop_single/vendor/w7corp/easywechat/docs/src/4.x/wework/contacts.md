# 通訊錄

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx', // 通訊錄的 secret
    //...
];

$contacts = Factory::work($config);
```

## 成員管理
### 建立成員

```php
$data = [
    "userid" => "overtrue",
    "name" => "超哥",
    "english_name" => "overtrue"
    "mobile" => "1818888888",
];
$contacts->user->create($data);
```

### 讀取成員

```php
$contacts->user->get('overtrue');
```

### 更新成員

```php
$contacts->user->update('overtrue', [
    "isleader": 0,
    'position' => 'PHP 醬油工程師',
    //...
]);
```

### 刪除成員

```php
$contacts->user->delete('overtrue');
// 或者刪除多個
$contacts->user->delete(['overtrue', 'zhangsan', 'wangwu']);
```

### 獲取部門成員

```php
$contacts->user->getDepartmentUsers($departmentId);
// 遞迴獲取子部門下面的成員
$contacts->user->getDepartmentUsers($departmentId, true);
```

### 獲取部門成員詳情

```php
$contacts->user->getDetailedDepartmentUsers($departmentId);
// 遞迴獲取子部門下面的成員
$contacts->user->getDetailedDepartmentUsers($departmentId, true);
```

### 使用者 ID 轉為 openid

```php
$contacts->user->userIdToOpenid($userId);
// 或者指定應用 ID
$contacts->user->userIdToOpenid($userId, $agentId);
```

### openid 轉為使用者 ID

```php
$contacts->user->openidToUserId($openid);
```

### 手機號轉為使用者 ID

```php
$contacts->user->mobileToUserId($mobile);
```

### 二次驗證

企業在成員驗證成功後，呼叫如下介面即可讓成員加入成功

```php
$contacts->user->accept($userId);
```

### 邀請成員

企業可透過介面批次邀請成員使用企業微信，邀請後將透過簡訊或郵件下發通知。

```php
$params = [
    'user' => ['UserID1', 'UserID2', 'UserID3'],    // 成員ID列表, 最多支援1000個
    'party' => ['PartyID1', 'PartyID2'],            // 部門ID列表，最多支援100個
    'tag' => ['TagID1', 'TagID2'],                  // 標籤ID列表，最多支援100個
];

$contacts->user->invite($params);
```

> `user`, `party`, `tag` 三者不能同時為空

### 獲取邀請二維碼

```php
$sizeType = 1;  // qrcode尺寸型別，1: 171 x 171; 2: 399 x 399; 3: 741 x 741; 4: 2052 x 2052

$contacts->user->getInvitationQrCode($sizeType);
```

## 部門管理

### 建立部門

```php
$contacts->department->create([
        'name' => '廣州研發中心',
        'parentid' => 1,
        'order' => 1,
        'id' => 2,
    ]);
```

### 更新部門

```php
$contacts->department->update($id, [
        'name' => '廣州研發中心',
        'parentid' => 1,
        'order' => 1,
    ]);
```

### 刪除部門

```php
$contacts->department->delete($id);
```

### 獲取部門列表

```php
$contacts->department->list();
// 獲取指定部門及其下的子部門
$contacts->department->list($id);
```

## 標籤管理

### 建立標籤

```php
$contacts->tag->create($tagName, $tagId);
```

### 更新標籤名字

```php
$contacts->tag->update($tagId, $tagName);
```

### 刪除標籤

```php
$contacts->tag->delete($tagId);
```

### 獲取標籤列表

```php
$contacts->tag->list();
```

### 獲取標籤成員(標籤詳情)

```php
$contacts->tag->get($tagId);
```

### 增加標籤成員

```php
$contacts->tag->tagUsers($tagId, [$userId1, $userId2, ...]);

// 指定部門
$contacts->tag->tagDepartments($tagId, [$departmentId1, $departmentId2, ...]);
```


### 刪除標籤成員

```php
$contacts->tag->untagUsers($tagId, [$userId1, $userId2, ...]);

// 指定部門
$contacts->tag->untagDepartments($tagId, [$departmentId1, $departmentId2, ...]);
```




