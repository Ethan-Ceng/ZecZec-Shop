# 搖一搖周邊


搖一搖周邊是微信線上下的全新功能, 為線下商戶提供近距離連線使用者的能力, 並支援線下商戶向周邊使用者提供個性化營銷、互動及資訊推薦等服務。

## 獲取例項

```php
<?php
use EasyWeChat\Foundation\Application;
// ...
$app = new Application($options);

$shakearound = $app->shakearound;

```

## API

> 特別提醒：
1、下述所有的介面呼叫的方法引數都要嚴格按照方法引數前的型別傳入相應型別的實參，否則可能會得到非預期的結果。
2、涉及需要傳入裝置id（$deviceIdentifier）的引數時，該引數是一個以 `device_id` 或包含 `uuid` `major` `minor` 為key的關聯陣列。
3、涉及需要傳入裝置id列表（$deviceIdentifiers）的引數時，該引數是一個二維陣列，第一層為索引型別，第二層為關聯型別（$deviceIdentifier）。

```php
// 引數$deviceIdentifier的實參形式：
['device_id' => 10097]
// 或
[
    'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
    'major' => 10001,
    'minor' => 12102,
]
// 引數$deviceIdentifiers的實參形式：
[
    ['device_id' => 10097],
    ['device_id' => 10098],
]
// 或
[
    [
        'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
        'major' => 10001,
        'minor' => 12102,
    ],
    [
        'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
        'major' => 10001,
        'minor' => 12103,
    ]
]
```

### 開通搖一搖周邊

> 提示：
若不是做 [公眾號第三方平臺](https://open.weixin.qq.com/cgi-bin/frame?t=home/wx_plugin_tmpl&lang=zh_CN) 開發，建議直接在微信管理後臺申請開通搖一搖周邊功能。

#### 申請開通

申請開通搖一搖周邊功能。成功提交申請請求後，工作人員會在三個工作日內完成稽核。若稽核不透過，可以重新提交申請請求。若是稽核中，請耐心等待工作人員稽核，在稽核中狀態不能再提交申請請求。

方法

> $shakearound->register(string $name, string $tel, string $email, string $industryId, array $certUrls [, $reason = ''])

引數

> $name 聯絡人姓名，不超過20漢字或40個英文字母
$tel 聯絡人電話
$email 聯絡人郵箱
$industryId 平臺定義的行業代號，具體請檢視連結 [行業代號](http://3gimg.qq.com/shake_nearby/Qualificationdocuments.html)
$certUrls 相關資質檔案的圖片url，圖片需先上傳至微信側伺服器，用“素材管理-上傳圖片素材”介面上傳圖片，返回的圖片URL再配置在此處；當不需要資質檔案時，請傳入空陣列
$reason 可選，申請理由，不超過250漢字或500個英文字母

> 注意：
1、相關資質檔案的圖片是使用本頁面下方的素材管理的介面上傳的，切勿和另一個 [素材管理](material.html) 介面混淆。
2、行業程式碼請務必傳入**字串**型別的實參，否則以數字0開頭的行業程式碼將會被當成八進位制數處理（將轉換為十進位制數），這可能不是期望的。

示例

```php
$result = $shakearound->register('zhang_san', '13512345678', 'weixin123@qq.com', '0118', [], 'test');

/* 返回結果
{
   "data": {

   },
   "errcode": 0,
   "errmsg": "success."
}
*/
var_dump($result->data) // 空陣列
var_dump($result->errcode) // 0
var_dump($result->errmsg) // success.
```

#### 查詢稽核狀態

查詢已經提交的開通搖一搖周邊功能申請的稽核狀態。在申請提交後，工作人員會在三個工作日內完成稽核。

方法

> $shakearound->getStatus()

引數

> 無

示例

```php
$result = $shakearound->getStatus();

/* 返回結果
{
    "data": {
        "apply_time": 1432026025,
        "audit_comment": "test",
        "audit_status": 1,
        "audit_time": 0
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->data['audit_comment']) // test
```

#### 獲取搖一搖的裝置及使用者資訊

獲取裝置資訊，包括UUID、major、minor，以及距離、openID等資訊。

方法

> $shakearound->getShakeInfo(string $ticket [, int $needPoi = null])

引數

> $ticket 搖周邊業務的ticket，可在搖到的URL中得到，ticket生效時間為30分鐘，每一次搖都會重新生成新的ticket
$needPoi 可選，是否需要返回門店poi_id，傳1則返回，否則不返回

示例

```php
$result = $shakearound->getShakeInfo('6ab3d8465166598a5f4e8c1b44f44645', 1);

/* 返回結果
{
   "data": {
       "page_id ": 14211,
       "beacon_info": {
           "distance": 55.00620700469034,
           "major": 10001,
           "minor": 19007,
           "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
       },
       "openid": "oVDmXjp7y8aG2AlBuRpMZTb1-cmA",
       "poi_id":1234
   },
   "errcode": 0,
   "errmsg": "success."
}
*/
var_dump($result->data['page_id']) // 14211
var_dump($result->data['beacon_info']['distance']) // 55.00620700469034
```

### 裝置管理

#### 申請裝置ID

申請配置裝置所需的UUID、Major、Minor。申請成功後返回批次ID，可用返回的批次ID透過“查詢裝置ID申請狀態”介面查詢目前申請的稽核狀態。
一個公眾賬號最多可申請100000個裝置ID，如需申請的裝置ID數超過最大限額，請郵件至zhoubian@tencent.com，郵件格式如下：

> 標題：申請提升裝置ID額度
內容：
1、公眾賬號名稱及appid（wx開頭的字串，在mp平臺可檢視）
2、用途
3、預估需要多少裝置ID

方法

> $shakearound->device()->apply(int $quantity, string $reason [, string $comment = '' [, int $poiId = null]])

引數

> $quantity 申請的裝置ID的數量，單次新增裝置超過500個，需走人工稽核流程
$reason 申請理由，不超過100個漢字或200個英文字母
$comment 可選，備註，不超過15個漢字或30個英文字母
$poiId 可選，裝置關聯的門店ID，關聯門店後，在門店1KM的範圍內有優先搖出資訊的機會

示例

```php
$result = $shakearound->device()->apply(3, '測試', '測試專用', 1234);

/* 返回結果
{
    "data": {
        "apply_id": 123,
        "audit_status": 1,
        "audit_comment": "稽核中"
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->data['apply_id']) // 123
```

#### 查詢裝置ID申請稽核狀態

查詢裝置ID申請的稽核狀態。若單次申請的裝置ID數量小於等於500個，系統會進行快速稽核；若單次申請的裝置ID數量大於500個，則在三個工作日內完成稽核。

方法

> $shakearound->device()->getStatus(int $applyId)

引數

> $applyId 批次ID，申請裝置ID時所返回的批次ID

示例

```php
$result = $shakearound->device()->getStatus(123);

/* 返回結果
{
    "data": {
        "apply_time": 1432026025,
        "audit_comment": "test",
        "audit_status": 1,
        "audit_time": 0
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->data['audit_status']) // 1
```

#### 編輯裝置資訊

> 僅能修改裝置的備註資訊。

方法

> $shakearound->device()->update(array $deviceIdentifier, string $comment)

引數

> $deviceIdentifier 裝置id，裝置編號device_id或UUID、major、minor的關聯陣列，若二者都填，則以裝置編號為優先
$comment 裝置的備註資訊，不超過15個漢字或30個英文字母

示例

```php
$result = $shakearound->device()->update(['device_id' => 10011], 'test');
// 或
$result = $shakearound->device()->update(['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                          'major' => 1002,
                                          'minor' => 1223,
], 'test');

/* 返回結果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->errcode) // 0
```

#### 配置裝置與門店/其他公眾賬號門店的關聯關係

關聯本公眾賬號門店時，支援建立門店後直接關聯在裝置上，無需為稽核透過狀態，搖周邊後臺自動更新門店的最新資訊和狀態。
關聯其他公眾賬號門店時，支援裝置關聯其他公眾賬號的門店，門店需為稽核透過狀態。

> 因為第三方門店不歸屬本公眾賬號，所以未儲存到裝置詳情中，查詢裝置列表介面與獲取搖周邊的裝置及使用者資訊介面不會返回第三方門店。

方法

> $shakearound->device()->bindLocation(array $deviceIdentifier, $poiId [, $type = 1 [, $poiAppid = null]])

引數

> $deviceIdentifier 裝置id，裝置編號device_id或UUID、major、minor的關聯陣列，若二者都填，則以裝置編號為優先
$poiId 裝置關聯的門店ID，關聯門店後，在門店1KM的範圍內有優先搖出資訊的機會。當值為0時，將清除裝置已關聯的門店ID
$type 可選，為1時，關聯的門店和裝置歸屬於同一公眾賬號；為2時，關聯的門店為其他公眾賬號的門店
$poiAppid 可選，當$type為1時該引數為必填

示例

```php
// 關聯本公眾賬號門店
$result = $shakearound->device()->bindLocation(['device_id' => 10011], 1231);
// 或
$result = $shakearound->device()->bindLocation(['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                                'major' => 1002,
                                                'minor' => 1223,
], 1231);

// 關聯其他公眾賬號門店
// wxappid為關聯門店所歸屬的公眾賬號的APPID
$result = $shakearound->device()->bindLocation(['device_id' => 10011], 1231, 2, 'wxappid');
// 或
$result = $shakearound->device()->bindLocation(['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                                'major' => 1002,
                                                'minor' => 1223,
], 1231, 2, 'wxappid');

/* 返回結果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->errcode) // 0
```

#### 查詢裝置列表

查詢已有的裝置ID、UUID、Major、Minor、啟用狀態、備註資訊、關聯門店、關聯頁面等資訊。

##### 根據裝置id批次取回裝置資料

方法

> $shakearound->device()->fetchByIds(array $deviceIdentifiers)

引數

> $deviceIdentifiers 裝置id列表

示例

```php
$result = $shakearound->device()->fetchByIds([
                                                ['device_id' => 10097],
                                                ['device_id' => 10098],
]);
// 或
$result = $shakearound->device()->fetchByIds([
                                                ['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                                 'major' => 10001,
                                                 'minor' => 12102,],
                                                ['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                                 'major' => 10001,
                                                 'minor' => 12103,]
]);

/* 返回結果
{
    "data": {
        "devices": [
            {
                "comment": "",
                "device_id": 10097,
                "major": 10001,
                "minor": 12102,
                "status": 1,
                "last_active_time":1437276018,
                "poi_id": 0,
                "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
            },
            {
                "comment": "",
                "device_id": 10098,
                "major": 10001,
                "minor": 12103,
                "status": 1,
                "last_active_time":1437276018,
                "poi_appid":"wxe3813f5d8c546fc7"
                "poi_id": 123,
                "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
            }
        ],
        "total_count": 151
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->data['devices'][0][device_id]) // 10097
var_dump($result->data['total_count']) // 151
```

##### 分頁批次取回裝置資料

方法

> $shakearound->device()->pagination(int $lastSeen, int $count)

引數

> $lastSeen 前一次查詢列表末尾的裝置編號device_id，第一次查詢last_seen為0
$count 待查詢的裝置數量，不能超過50個

示例

```php
$result = $shakearound->device()->pagination(10097, 3);

// 返回結果同上
```

##### 根據申請時的批次ID分頁批次取回裝置資料

方法

> $shakearound->device()->fetchByApplyId(int $applyId, int $lastSeen, int $count)

引數

> $applyId 批次ID，申請裝置ID時所返回的批次ID
$lastSeen 前一次查詢列表末尾的裝置編號device_id，第一次查詢lastSeen為0
$count 待查詢的裝置數量，不能超過50個

示例

```php
$result = $shakearound->device()->fetchByApplyId(1231, 10097, 3);

// 返回結果同上
```

### 頁面管理

#### 新增頁面

新增搖一搖出來的頁面資訊，包括在搖一搖頁面出現的主標題、副標題、圖片和點選進去的超連結。其中，圖片必須為用素材管理介面上傳至微信側伺服器後返回的連結。

> 注意：
圖片是使用本頁面下方的素材管理的介面上傳的，切勿和另一個 [素材管理](material.html) 介面混淆。

方法

> $shakearound->page()->add(string $title, string $description, strig $pageUrl, string $iconUrl [, string $comment = ''])

引數

> $title 在搖一搖頁面展示的主標題，不超過6個漢字或12個英文字母
$description 在搖一搖頁面展示的副標題，不超過7個漢字或14個英文字母
$pageUrl 點選進去的超連結
$iconUrl 在搖一搖頁面展示的圖片。圖片需先上傳至微信側伺服器，用“素材管理-上傳圖片素材”介面上傳圖片，返回的圖片URL再配置在此處
$comment 可選，頁面的備註資訊，不超過15個漢字或30個英文字母

示例

```php
$result = $shakearound->page()->add('主標題', '副標題', 'https://zb.weixin.qq.com', 'http://3gimg.qq.com/shake_nearby/dy/icon', 'test');

/* 返回結果
{
   "data": {
       "page_id": 28840
   }
   "errcode": 0,
   "errmsg": "success."
}
*/
var_dump($result->data['page_id']) // 28840
```

#### 編輯頁面資訊

編輯搖一搖出來的頁面資訊，包括在搖一搖頁面出現的主標題、副標題、圖片和點選進去的超連結。

方法

> $shakearound->page()->update(int $pageId, string $title, string $description, string $pageUrl, string $iconUrl [, string $comment = ''])

引數

> $pageId 搖周邊頁面唯一ID
$title 在搖一搖頁面展示的主標題，不超過6個漢字或12個英文字母
$description 在搖一搖頁面展示的副標題，不超過7個漢字或14個英文字母
$pageUrl 點選進去的超連結
$iconUrl 在搖一搖頁面展示的圖片。圖片需先上傳至微信側伺服器，用“素材管理-上傳圖片素材”介面上傳圖片，返回的圖片URL再配置在此處
$comment 可選，頁面的備註資訊，不超過15個漢字或30個英文字母

示例

```php
$result = $shakearound->page()->add(28840, '主標題', '副標題', 'https://zb.weixin.qq.com', 'http://3gimg.qq.com/shake_nearby/dy/icon', 'test');

/* 返回結果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->errcode) // 0
```

#### 查詢頁面列表

查詢已有的頁面，包括在搖一搖頁面出現的主標題、副標題、圖片和點選進去的超連結。

##### 根據頁面id批次取回頁面資料

方法

> $shakearound->page()->fetchByIds(array $pageIds)

引數

> $pageIds 頁面的id列表，索引陣列

示例

```php
$result = $shakearound->page()->fetchByIds([28840, 28842]);

/* 返回結果
{
   "data": {
       "pages": [
           {
               "comment": "just for test",
               "description": "test",
               "icon_url": "https://www.baidu.com/img/bd_logo1",
               "page_id": 28840,
               "page_url": "http://xw.qq.com/testapi1",
               "title": "測試1"
           },
           {
               "comment": "just for test",
               "description": "test",
               "icon_url": "https://www.baidu.com/img/bd_logo1",
               "page_id": 28842,
               "page_url": "http://xw.qq.com/testapi2",
               "title": "測試2"
           }
       ],
       "total_count": 2
   },
   "errcode": 0,
   "errmsg": "success."
}
*/
var_dump($result->data['pages'][0]['title']) // 測試1
var_dump($result->data['total_count']) // 2
```

##### 分頁批次取回頁面資料

方法

> $shakearound->page()->pagination(int $begin, int $count)

引數

> $begin 頁面列表的起始索引值
$count 待查詢的頁面數量，不能超過50個

示例

```php
$result = $shakearound->page()->pagination(0,2);

// 返回結果同上
```

#### 刪除頁面

刪除已有的頁面，包括在搖一搖頁面出現的主標題、副標題、圖片和點選進去的超連結。

> 注意：
只有頁面與裝置沒有關聯關係時，才可被刪除。

方法

> $shakearound->page()->delete(int $pageId)

引數

> $pageId 頁面的id

示例

```php
$result = $shakearound->page()->delete(34567);

/* 返回結果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->errcode) // 0
```

### 素材管理

上傳在搖一搖功能中需使用到的圖片素材，素材儲存在微信側伺服器上。圖片格式限定為：jpg,jpeg,png,gif。
若圖片為在搖一搖頁面展示的圖片，則其素材為 `icon` 型別的圖片，圖片大小建議 `120px*120 px` ，限制不超過 `200 px *200 px` ，圖片需為 `正方形` 。
若圖片為申請開通搖一搖周邊功能需要上傳的資質檔案圖片，則其素材為 `license` 型別的圖片，圖片的檔案大小不超過 `2MB` ，尺寸不限，形狀不限。

方法

> $shakearound->material()->uploadImage(string $path [, string $type = 'icon'])

引數

> $path 圖片所在路徑
$type 可選，值為icon或license

示例

```php
$result = $shakearound->material()->uploadImage(__DIR__ . '/stubs/image.jpg');

/* 返回結果
{
    "data": {
        "pic_url": http://shp.qpic.cn/wechat_shakearound_pic/0/1428377032e9dd2797018cad79186e03e8c5aec8dc/120"
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->data['pic_url']) // http://shp.qpic.cn/wechat_shakearound_pic/0/1428377032e9dd2797018cad79186e03e8c5aec8dc/120
```

### 管理裝置與頁面的關係

透過介面申請的裝置ID，需先配置頁面，若未配置頁面，則搖不出頁面資訊。

#### 配置裝置與頁面的關聯關係

配置完成後，在此裝置的訊號範圍內，即可搖出關聯的頁面資訊。
若裝置配置多個頁面，則隨機出現頁面資訊。一個裝置最多可配置30個關聯頁面。

> 注意：
1、配置時傳入該裝置需要關聯的頁面的id列表，該裝置原有的關聯關係將被直接清除。
2、頁面的id列表允許為空（**傳入空陣列**），當頁面的id列表為空時則會清除該裝置的所有關聯關係。

方法

> $shakearound->relation()->bindPage(array $deviceIdentifier, array $pageIds)

引數

> $deviceIdentifier 裝置id，裝置編號device_id或UUID、major、minor的關聯陣列，若二者都填，則以裝置編號為優先
$pageIds 頁面的id列表，索引陣列

示例

```php
$result = $shakearound->relation()->bindPage(['device_id' => 10011], [12345, 23456, 334567]);
// 或
$result = $shakearound->relation()->bindPage(['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                              'major' => 1002,
                                              'minor' => 1223,
], [12345, 23456, 334567]);

/* 返回結果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->errcode) // 0
```

#### 查詢裝置與頁面的關聯關係

##### 查詢指定裝置所關聯的頁面

根據裝置ID或完整的UUID、Major、Minor查詢該裝置所關聯的所有頁面資訊

方法

> $shakearound->relation()->getPageByDeviceId(array $deviceIdentifier [, boolean $raw = false])

> 注意：
該方法預設對返回的資料進行處理後返回一個包含頁面id的索引陣列。若要返回和 `getDeviceByPageId` 方法類似的資料，請傳入 `true` 作為第二個引數。

引數

> $deviceIdentifier 裝置id，裝置編號device_id或UUID、major、minor的關聯陣列，若二者都填，則以裝置編號為優先
$raw 可選，當為true時，返回值和getDeviceByPageId方法類似，否則返回頁面的id列表（索引陣列，無關聯時為空陣列）

示例

```php
$result = $shakearound->relation()->getPageByDeviceId(['device_id' => 10011]);
// 或
$result = $shakearound->relation()->getPageByDeviceId(['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                                       'major' => 1002,
                                                       'minor' => 1223,
]);

// 返回結果
var_dump($result) // [50054,50055]
```

##### 查詢指定頁面所關聯的裝置

指定頁面ID分頁查詢該頁面所關聯的所有的裝置資訊

方法

> $shakearound->relation()->getDeviceByPageId(int $pageId, int $begin, int $count)

引數

> $pageId 指定的頁面id
$begin 關聯關係列表的起始索引值
$count 待查詢的關聯關係數量，不能超過50個

示例

```php
$result = $shakearound->relation()->getDeviceByPageId(50054, 0, 3);

/* 返回結果
{
  "data": {
      "relations": [
          {
              "device_id": 797994,
              "major": 10001,
              "minor": 10023,
              "page_id": 50054,
              "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
          },
          {
              "device_id": 797995,
              "major": 10001,
              "minor": 10024,
              "page_id": 50054,
              "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
          }
      ],
      "total_count": 2
  },
  "errcode": 0,
  "errmsg": "success."
}
*/
var_dump($result->data['relations'][0]['device_id']) // 797994
var_dump($result->data['total_count']) // 2
```

### 搖一搖資料統計

> 此介面無法獲取當天的資料，最早只能獲取前一天的資料。
由於系統在凌晨處理前一天的資料，太早呼叫此介面可能獲取不到資料，建議在早上8：00之後呼叫此介面。

#### 以裝置為維度的資料統計

查詢單個裝置進行搖周邊操作的人數、次數，點選搖周邊訊息的人數、次數。

> 注意：
查詢的最長時間跨度為30天。只能查詢最近90天的資料。

方法

> $shakearound->stats()->deviceSummary(array $deviceIdentifier, int $beginDate, int $endDate)

引數

> $deviceIdentifier 裝置id，裝置編號device_id或UUID、major、minor的關聯陣列，若二者都填，則以裝置編號為優先
$beginDate 起始日期時間戳，最長時間跨度為30天，單位為秒
$endDate 結束日期時間戳，最長時間跨度為30天，單位為秒

示例

```php
$result = $shakearound->stats()->deviceSummary(['device_id' => 10011], 1425052800, 1425139200);
// 或
$result = $shakearound->stats()->deviceSummary(['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                                'major' => 1002,
                                                'minor' => 1223,
], 1425052800, 1425139200);

/* 返回結果
{
   "data": [
       {
           "click_pv": 0,
           "click_uv": 0,
           "ftime": 1425052800,
           "shake_pv": 0,
           "shake_uv": 0
       },
       {
           "click_pv": 0,
           "click_uv": 0,
           "ftime": 1425139200,
           "shake_pv": 0,
           "shake_uv": 0
       }
   ],
   "errcode": 0,
   "errmsg": "success."
}
*/
var_dump($result->data[0]['ftime']) // 1425052800
```

#### 批次查詢裝置統計資料

查詢指定時間商家帳號下的每個裝置進行搖周邊操作的人數、次數，點選搖周邊訊息的人數、次數。

> 只能查詢最近90天內的資料，且一次只能查詢一天。

> 注意：
對於搖周邊人數、搖周邊次數、點選搖周邊訊息的人數、點選搖周邊訊息的次數都為0的裝置，不在結果列表中返回。

方法

> $shakearound->stats()->batchDeviceSummary(int $timestamp, int $pageIndex)

引數

> $timestamp 指定查詢日期時間戳，單位為秒
$pageIndex 指定查詢的結果頁序號，返回結果按搖周邊人數降序排序，每50條記錄為一頁

示例

```php
$result = $shakearound->stats()->batchDeviceSummary(1435075200, 1);

/* 返回結果
{
    "data": {
        "devices": [
            {
                "device_id": 10097,
                "major": 10001,
                "minor": 12102,
                "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
                "shake_pv": 1
                "shake_uv": 2
                "click_pv": 3
                "click_uv": 4
            },
            {
                "device_id": 10098,
                "major": 10001,
                "minor": 12103,
                "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
                "shake_pv": 1
                "shake_uv": 2
                "click_pv": 3
                "click_uv": 4
            }
        ],
    },
    "date":1435075200
    "total_count": 151
    "page_index":1
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->data['devices'][0]['device_id']) // 10097
var_dump($result->total_count) // 151
```

#### 以頁面為維度的資料統計

查詢單個頁面透過搖周邊搖出來的人數、次數，點選搖周邊頁面的人數、次數

> 注意：
查詢的最長時間跨度為30天。只能查詢最近90天的資料。

方法

> $shakearound->stats()->pageSummary(int $pageId, int $beginDate, int $endDate);

引數

> $pageId 指定頁面的頁面ID
$beginDate 起始日期時間戳，最長時間跨度為30天，單位為秒
$endDate 結束日期時間戳，最長時間跨度為30天，單位為秒

示例

```php
$result = $shakearound->stats()->pageSummary(12345, 1425052800, 1425139200);

/* 返回結果
{
   "data": [
       {
           "click_pv": 0,
           "click_uv": 0,
           "ftime": 1425052800,
           "shake_pv": 0,
           "shake_uv": 0
       },
       {
           "click_pv": 0,
           "click_uv": 0,
           "ftime": 1425139200,
           "shake_pv": 0,
           "shake_uv": 0
       }
   ],
   "errcode": 0,
   "errmsg": "success."
}
*/
var_dump($result->data[1]['ftime']) // 1425139200
```
#### 批次查詢頁面統計資料

查詢指定時間商家帳號下的每個頁面進行搖周邊操作的人數、次數，點選搖周邊訊息的人數、次數。

> 注意：
對於搖周邊人數、搖周邊次數、點選搖周邊訊息的人數、點選搖周邊訊息的次數都為0的頁面，不在結果列表中返回。

方法

> $shakearound->stats()->batchPageSummary(int $timestamp, int $pageIndex);

引數

> $timestamp 指定查詢日期時間戳，單位為秒
$pageIndex 指定查詢的結果頁序號，返回結果按搖周邊人數降序排序，每50條記錄為一頁

示例

```php
$result = $shakearound->stats()->batchPageSummary(1435075200, 1);

/* 返回結果
{
    "data": {
        "pages": [
            {
                "page_id":1234
                "click_pv": 1,
                "click_uv": 3,
                "shake_pv": 0,
                "shake_uv": 0
            },
            {
                "page_id":5678
                "click_pv": 1,
                "click_uv": 2,
                "shake_pv": 0,
                "shake_uv": 0
            },
        ],
    },
    "date":1435075200
    "total_count": 151
    "page_index":1
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->data['pages'][0]['click_uv']) // 3
var_dump($result->total_count) // 151
```

### 裝置分組管理

呼叫H5頁面獲取裝置資訊 JS API介面，需要先把裝置分組，微信客戶端只會返回已在分組中的裝置資訊。

#### 新增分組

新建裝置分組，每個帳號下最多隻有1000個分組。

方法

> $shakearound->group()->add(string $name)

引數

> $name 分組名稱，不超過100漢字或200個英文字母

示例

```php
$result = $shakearound->group()->add('test');

/* 返回結果
{
  "data": {
      "group_id" : 123,
      "group_name" : "test"
  },
  "errcode": 0,
  "errmsg": "success."
}
*/
var_dump($result->data['group_id']) // 123
var_dump($result->data['group_name']) // test
```

#### 編輯分組資訊

編輯裝置分組資訊，目前只能修改分組名。

方法

> $shakearound->group()->update(int $groupId, string $name)

引數

> $groupId 分組唯一標識，全域性唯一
$name 分組名稱，不超過100漢字或200個英文字母

示例

```php
$result = $shakearound->group()->update(123, 'newName');

/* 返回結果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->errcode) // 0
```

#### 刪除分組

刪除裝置分組，若分組中還存在裝置，則不能刪除成功。需把裝置移除以後，才能刪除。

> 在執行刪除前，最好先使用 `getDetails` 方法查詢分組詳情，若分組內有裝置，先使用 `removeDevice` 方法移除。

方法

> $shakearound->group()->delete(int $groupId)

引數

> $groupId 分組唯一標識，全域性唯一

示例

```php
$result = $shakearound->group()->delete(123);

/* 返回結果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->errcode) // 0
```

#### 查詢分組列表

查詢賬號下所有的分組。

方法

> $shakearound->group()->lists(int $begin, int $count)

引數

> $begin 分組列表的起始索引值
$count 待查詢的分組數量，不能超過1000個

示例

```php
$result = $shakearound->group()->lists(0, 2);

/* 返回結果
{
    "data": {
        "groups":[
            {
                "group_id" : 123,
                "group_name" : "test1"
            },
            {
                "group_id" : 124,
                "group_name" : "test2"
            }
        ],
        "total_count": 100
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->data['groups'][1]['group_name']) // test2
var_dump($result->data['total_count']) // 100
```

#### 查詢分組詳情

查詢分組詳情，包括分組名，分組id，分組裡的裝置列表。

方法

> $shakearound->group()->getDetails(int $groupId, int $begin, int $count)

引數

> $groupId 分組唯一標識，全域性唯一
$begin 分組裡裝置的起始索引值
$count 待查詢的分組裡裝置的數量，不能超過1000個

示例

```php
$result = $shakearound->group()->getDetails(123, 0, 2);

/* 返回結果
{
    "data": {
        "group_id" : 123,
        "group_name" : "test",
        "total_count": 100,
        "devices" :[
            {
                "device_id" : 123456,
                "uuid" : "FDA50693-A4E2-4FB1-AFCF-C6EB07647825",
                "major" : 10001,
                "minor" : 10001,
                "comment" : "test device1",
                "poi_id" : 12345,
            },
            {
                "device_id" : 123457,
                "uuid" : "FDA50693-A4E2-4FB1-AFCF-C6EB07647825",
                "major" : 10001,
                "minor" : 10002,
                "comment" : "test device2",
                "poi_id" : 12345,
            }
        ]
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->data['devices'][0]['comment']) // test device1
var_dump($result->data['total_count']) // 100
```

#### 新增裝置到分組

新增裝置到分組，每個分組能夠持有的裝置上限為10000，並且每次新增操作的新增上限為1000。

> 只有在搖周邊申請的裝置才能新增到分組。

方法

> $shakearound->group()->addDevice(int $groupId, array $deviceIdentifiers)

引數

> $groupId 分組唯一標識，全域性唯一
$deviceIdentifiers 裝置id列表

示例

```php
$result = $shakearound->group()->addDevice(123, [
                                                    ['device_id' => 10097],
                                                    ['device_id' => 10098],
]);
// 或
$result = $shakearound->group()->addDevice(123, [
                                                    ['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                                    'major' => 10001,
                                                    'minor' => 12102,],
                                                    ['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                                    'major' => 10001,
                                                    'minor' => 12103,]
]);

/* 返回結果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->errcode) // 0
```

#### 從分組中移除裝置

從分組中移除裝置，每次刪除操作的上限為1000。

方法

> $shakearound->group()->removeDevice(int $groupId, array $deviceIdentifiers)

引數

> $groupId 分組唯一標識，全域性唯一
$deviceIdentifiers 裝置id列表

示例

```php
$result = $shakearound->group()->removeDevice(123, [
                                                    ['device_id' => 10097],
                                                    ['device_id' => 10098],
]);
// 或
$result = $shakearound->group()->removeDevice(123, [
                                                    ['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                                    'major' => 10001,
                                                    'minor' => 12102,],
                                                    ['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                                    'major' => 10001,
                                                    'minor' => 12103,]
]);

/* 返回結果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->errcode) // 0
```

### 搖一搖紅包

微信官方目前暫停了搖紅包介面，該介面可能會有所調整，故而暫時不提供該介面的封裝。

> 官方公告詳情請至： [關於搖紅包介面暫停的公告](https://zb.weixin.qq.com/nearby/announce.xhtml?announceId=10047)

### 搖一搖事件通知

使用者進入搖一搖介面，在“周邊”頁卡下搖一搖時，微信會把這個事件推送到開發者填寫的URL（登入公眾平臺進入開發者中心設定）。推送內容包含搖一搖時“周邊”頁卡展示出來的頁面所對應的裝置資訊，以及附近最多五個屬於該公眾賬號的裝置的資訊。當搖出列表時，此事件不推送。

> 搖一搖事件的事件型別：ShakearoundUserShake
關於事件的處理請移步： [事件](events.html)

### 搖一搖周邊錯誤碼

> 搖周邊錯誤碼請移步： [錯誤碼](https://mp.weixin.qq.com/wiki?action=doc&id=mp1443448163&t=0.17525333335674986)

有關搖一搖周邊介面資訊的更多細節請參考微信官方文件相應條目： [微信官方文件](http://mp.weixin.qq.com/wiki/)
