# 卡券

-

## 獲取例項

```php
$card = $app->card;
```

## 通用功能

### 獲取卡券顏色

```php
$card->colors();
```

### 卡券開放類目查詢

```php
$card->categories();
```

### 建立卡券

建立卡券介面是微信卡券的基礎介面，用於建立一類新的卡券，獲取 card_id，建立成功並透過稽核後，商家可以透過文件提供的其他介面將卡券下發給使用者，每次成功領取，庫存數量相應扣除。

```php
$card->create($cardType = 'member_card', array $attributes);
```

> - `attributes` array 卡券資訊

示例：

```php
<?php

	$cardType = 'GROUPON';

    $attributes = [
      'base_info' => [
          'brand_name' => '微信餐廳',
          'code_type' => 'CODE_TYPE_TEXT',
          'title' => '132元雙人火鍋套餐',
          //...
      ],
      'advanced_info' => [
          'use_condition' => [
              'accept_category' => '鞋類',
              'reject_category' => '阿迪達斯',
              'can_use_with_other_discount' => true,
          ],
          //...
      ],
    ];

$result = $card->create($cardType, $attributes);
```

### 獲取卡券詳情

```php
$cardInfo = $card->get($cardId);
```

### 批次查詢卡列表

```php
$card->list($offset = 0, $count = 10, $statusList = 'CARD_STATUS_VERIFY_OK');
```

> - `offset` int - 查詢卡列表的起始偏移量，從 0 開始
> - `count` int - 需要查詢的卡片的數量
> - `statusList` - 支援開發者拉出指定狀態的卡券列表，詳見 example

示例：

```php
// CARD_STATUS_NOT_VERIFY, 待稽核；
// CARD_STATUS_VERIFY_FAIL, 稽核失敗；
// CARD_STATUS_VERIFY_OK， 透過稽核；
// CARD_STATUS_USER_DELETE，卡券被商戶刪除；
// CARD_STATUS_DISPATCH，在公眾平臺投放過的卡券；

$result = $card->list($offset, $count, 'CARD_STATUS_NOT_VERIFY');
```

### 更改卡券資訊介面

支援更新所有卡券型別的部分通用欄位及特殊卡券中特定欄位的資訊。

```php
$card->update($cardId, $type, $attributes = []);
```

> - `type` string - 卡券型別

示例：

```php
$cardId = 'pdkJ9uCzKWebwgNjxosee0ZuO3Os';

$type = 'groupon';

$attributes = [
  'base_info' => [
    'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/2aJY6aCPatSeibYAyy7yct9zJXL9WsNVL4JdkTbBr184gNWS6nibcA75Hia9CqxicsqjYiaw2xuxYZiaibkmORS2oovdg/0',
    'center_title' => '頂部居中按鈕',
    'center_sub_title' => '按鈕下方的wording',
    'center_url' => 'http://www.easywechat.com',
    'custom_url_name' => '立即使用',
    'custom_url' => 'http://www.qq.com',
    'custom_url_sub_title' => '6個漢字tips',
    'promotion_url_name' => '更多優惠',
    'promotion_url' => 'http://www.qq.com',
  ],
  //...
];

$result = $card->update($cardId, $type, $attributes);
```

### 刪除卡券

```php
$card->delete($cardId);
```

### 建立二維碼

開發者可呼叫該介面生成一張卡券二維碼供使用者掃碼後新增卡券到卡包。

自定義 Code 碼的卡券呼叫介面時，POST 資料中需指定 code，非自定義 code 不需指定，指定 openid 同理。指定後的二維碼只能被使用者掃描領取一次。

```php
$card->createQrCode($cards);
```

> - `cards` array - 卡券相關資訊

示例：

```php
// 領取單張卡券
$cards = [
    'action_name' => 'QR_CARD',
    'expire_seconds' => 1800,
    'action_info' => [
      'card' => [
        'card_id' => 'pdkJ9uFS2WWCFfbbEfsAzrzizVyY',
        'is_unique_code' => false,
        'outer_id' => 1,
      ],
    ],
  ];

$result = $card->createQrCode($cards);
```

```php
// 領取多張卡券
$cards = [
    'action_name' => 'QR_MULTIPLE_CARD',
    'action_info' => [
      'multiple_card' => [
        'card_list' => [
          ['card_id' => 'pdkJ9uFS2WWCFfbbEfsAzrzizVyY'],
        ],
      ],
    ],
  ];

$result = $card->createQrCode($cards);
```

請求成功返回值示例：

```json
{
  "errcode": 0,
  "errmsg": "ok",
  "ticket": "gQHB8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0JIV3lhX3psZmlvSDZmWGVMMTZvAAIEsNnKVQMEIAMAAA==", //獲取ticket後需呼叫換取二維碼介面獲取二維碼圖片，詳情見欄位說明。
  "expire_seconds": 1800,
  "url": "http://weixin.qq.com/q/BHWya_zlfioH6fXeL16o ",
  "show_qrcode_url": "https://mp.weixin.qq.com/cgi-bin/showqrcode?  ticket=gQH98DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0czVzRlSWpsamlyM2plWTNKVktvAAIE6SfgVQMEgDPhAQ%3D%3D"
}
```

### ticket 換取二維碼圖片

獲取二維碼 ticket 後，開發者可用 ticket 換取二維碼圖片。

```php
$card->getQrCode($ticket);
```

> - `ticket` string> - 獲取的二維碼 ticket，憑藉此 ticket 可以在有效時間內換取二維碼。

示例：

```php
$ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
$result = $card->getQrCode($ticket);
```

### ticket 換取二維碼連結

```php
$card->getQrCodeUrl($ticket);
```

示例：

```php
$ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
$card->getQrCodeUrl($ticket);
```

### 建立貨架介面

開發者需呼叫該介面建立貨架連結，用於卡券投放。建立貨架時需填寫投放路徑的場景欄位。

```php
$card->createLandingPage($banner, $pageTitle, $canShare, $scene, $cards);
```

> - `banner` string -頁面的 banner 圖;
> - `pageTitle` string - 頁面的 title
> - `canShare` bool - 頁面是不是可以分享，true 或 false
> - `scene` string - 投放頁面的場景值，具體值請參考下面的 example
> - `cards` array - 卡券列表，每個元素有兩個欄位

示例：

```php
$banner = 'http://mmbiz.qpic.cn/mmbiz/iaL1LJM1mF9aRKPZJkmG8xXhiaHqkKSVMMWeN3hLut7X7hicFN';
$pageTitle = '惠城優惠大派送';
$canShare  = true;

//SCENE_NEAR_BY          附近
//SCENE_MENU             自定義選單
//SCENE_QRCODE             二維碼
//SCENE_ARTICLE             公眾號文章
//SCENE_H5                 h5頁面
//SCENE_IVR                 自動回覆
//SCENE_CARD_CUSTOM_CELL 卡券自定義cell
$scene = 'SCENE_NEAR_BY';

$cardList = [
    ['card_id' => 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY', 'thumb_url' => 'http://test.digilinx.cn/wxApi/Uploads/test.png'],
    ['card_id' => 'pdkJ9uJ37aU-tyRj4_grs8S45k1c', 'thumb_url' => 'http://test.digilinx.cn/wxApi/Uploads/aa.jpg'],
];

$result = $card->createLandingPage($banner, $pageTitle, $canShare, $scene, $cardList);
```

### 圖文訊息群髮卡券

> 特別注意：目前該介面僅支援填入非自定義 code 的卡券,自定義 code 的卡券需先進行 code 匯入後呼叫。

```php
$card->getHtml($cardId);
```

示例：

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$result = $card->getHtml($cardId);
```

### 設定測試白名單

同時支援“openid”、“username”兩種欄位設定白名單，總數上限為 10 個。

```php
$card->setTestWhitelist($openids); // 使用 openid
$card->setTestWhitelistByName($usernames); // 使用 username
```

> - `openids` array - 測試的 openid 列表
> - `usernames` array> - 測試的微訊號列表

示例：

```php
// by openid
$openids   = [$openId, $openId2, $openid3...];
$result = $card->setTestWhitelist($openids);

// by username
$usernames = ['tianye0327', 'iovertrue'];
$result = $card->setTestWhitelistByName($usernames);
```

### 獲取使用者已領取卡券介面

用於獲取使用者卡包裡的，屬於該 appid 下所有**可用卡券，包括正常狀態和未生效狀態**。

```php
$card->getUserCards($openid, $cardId);
```

示例：

```php
$openid  = 'odkJ9uDUz26RY-7DN1mxkznfo9xU';
$cardId = ''; // 卡券ID。不填寫時預設查詢當前 appid 下的卡券。

$result = $card->getUserCards($openid, $cardId);
```

### 設定微信買單介面

```php
$card->setPayCell($cardId, $isOpen = true);
```

> - `isOpen` string - 是否開啟買單功能，填 true/false，不填預設 true

示例：

```php
$cardId = 'pdkJ9uH7u11R-Tu1kilbaW_zDFow';

$result = $card->setPayCell($cardId); // isOpen = true
$result = $card->setPayCell($cardId, $isOpen);
```

### 修改庫存介面

```php
$card->increaseStock($cardId, $amount); // 增加庫存
$card->reductStock($cardId, $amount); // 減少庫存
```

> - `cardId` string - 卡券 ID
> - `amount` int - 修改多少庫存

示例：

```php
$cardId = 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY';

$result = $card->increaseStock($cardId, 100);
```

## 卡券 Code

### 匯入 code 介面

在自定義 code 卡券成功建立並且透過稽核後，必須將自定義 code 按照與發券方的約定數量呼叫匯入 code 介面匯入微信後臺。

```php
$card->code->deposit($cardId, $codes);
```

> - `cardId` string - 要匯入 code 的卡券 ID
> - `codes` array - 要匯入微信卡券後臺的自定義 code，最多 100 個

示例：

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';
$codes    = ['11111', '22222', '33333'];

$result = $card->code->deposit($cardId, $codes);
```

### 查詢匯入 code 數目

```php
$card->code->getDepositedCount($cardId);  // 要匯入 code 的卡券 ID
```

示例：

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$result = $card->code->getDepositedCount($cardId);
```

### 核查 code 介面

為了避免出現匯入差錯，強烈建議開發者在查詢完 code 數目的時候核查 code 介面校驗 code 匯入微信後臺的情況。

```php
$card->code->check($cardId, $codes);
```

示例：

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$codes = ['807732265476', '22222', '33333'];

$result = $card->code->check($cardId, $codes);
```

### 查詢 Code 介面

```php
$card->code->get($code, $cardId, $checkConsume = true);
```

> - checkConsume 是否校驗 code 核銷狀態，true 和 false

示例：

```php
$code = '736052543512';
$cardId = 'pdkJ9uDgnm0pKfrTb1yV0dFMO_Gk';

$result = $card->code->get($code, $cardId);
$result = $card->code->get($code, $cardId, false); // check_consume = false
```

### 核銷 Code 介面

```php
$card->code->consume($code);
// 或者指定 cardId
$card->code->consume($code, $cardId);
```

示例：

```php
$code = '789248558333';
$cardId = 'pdkJ9uDmhkLj6l5bm3cq9iteQBck';

$result = $card->code->consume($code);
// 或
$result = $card->code->consume($code, $cardId);
```

### Code 解碼介面

```php
$card->code->decrypt($encryptedCode);
```

示例：

```php
$encryptedCode = 'XXIzTtMqCxwOaawoE91+VJdsFmv7b8g0VZIZkqf4GWA60Fzpc8ksZ/5ZZ0DVkXdE';

$result = $card->code->decrypt($encryptedCode);
```

### 更改 Code 介面

```php
$card->code->update($code, $newCode, $cardId);
```

> - `newCode` string - 變更後的有效 Code 碼

示例：

```php
$code = '148246271394';
$newCode = '659266965266';
$cardId = '';

$result = $card->code->update($code, $newCode, $cardId);
```

### 設定卡券失效

```php
$card->code->disable($code, $cardId);
```

示例：

```php
$code    = '736052543512';
$cardId = '';

$result = $card->code->disable($code, $cardId);
```

## 通用卡券

## 卡券啟用

```php
$result = $card->general_card->activate($info);
```

## 撤銷啟用

```php
$result = $card->general_card->deactivate(string $cardId, string $code);
```

## 更新使用者資訊

```php
$result = $card->general_card->updateUser(array $info);
```

## 會員卡

### 會員卡啟用

```php
$result = $card->member_card->activate($info);
```

> - `info` - 需要啟用的會員卡資訊

示例：

```php
$info = [
      'membership_number'        => '357898858', //會員卡編號，由開發者填入，作為序列號顯示在使用者的卡包裡。可與Code碼保持等值。
      'code'                     => '916679873278', //建立會員卡時獲取的初始code。
      'activate_begin_time'      => '1397577600', //啟用後的有效起始時間。若不填寫預設以建立時的 data_info 為準。Unix時間戳格式
      'activate_end_time'        => '1422724261', //啟用後的有效截至時間。若不填寫預設以建立時的 data_info 為準。Unix時間戳格式。
      'init_bonus'               => '持白金會員卡到店消費，可享8折優惠。', //初始積分，不填為0。
      'init_balance'             => '持白金會員卡到店消費，可享8折優惠。', //初始餘額，不填為0。
      'init_custom_field_value1' => '白銀', //建立時欄位custom_field1定義型別的初始值，限制為4個漢字，12位元組。
      'init_custom_field_value2' => '9折', //建立時欄位custom_field2定義型別的初始值，限制為4個漢字，12位元組。
      'init_custom_field_value3' => '200', //建立時欄位custom_field3定義型別的初始值，限制為4個漢字，12位元組。
];

$result = $card->member_card->activate($info);
```

### 設定開卡欄位

```php
$card->member_card->setActivationForm($cardId, $settings);
```

> - `settings` array - 會員卡啟用時的選項

示例：

```php
$cardId = 'pdkJ9uJYAyfLXsUCwI2LdH2Pn1AU';

$settings = [
    'required_form' => [
        'common_field_id_list' => [
            'USER_FORM_INFO_FLAG_MOBILE',
            'USER_FORM_INFO_FLAG_LOCATION',
            'USER_FORM_INFO_FLAG_BIRTHDAY',
        ],
        'custom_field_list' => [
            '喜歡的食物',
        ],
    ],
    'optional_form' => [
        'common_field_id_list' => [
            'USER_FORM_INFO_FLAG_EMAIL',
        ],
        'custom_field_list' => [
            '喜歡的食物',
        ],
    ],
];

$result = $card->member_card->setActivationForm($cardId, $settings);
```

### 拉取會員資訊

```php
$card->member_card->getUser($cardId, $code);
```

示例：

```php
$cardId = 'pbLatjtZ7v1BG_ZnTjbW85GYc_E8';
$code    = '916679873278';

$result = $card->member_card->getUser($cardId, $code);
```

### 更新會員資訊

```php
$card->member_card->updateUser($info);
```

> - `info` array - 可以更新的會員資訊

示例：

```php
$info = [
    'code'                => '916679873278', //卡券Code碼。
    'card_id'             => 'pbLatjtZ7v1BG_ZnTjbW85GYc_E8', //卡券ID。
    'record_bonus'        => '消費30元，獲得3積分', //商家自定義積分消耗記錄，不超過14個漢字。
    'bonus'               => '100', //需要設定的積分全量值，傳入的數值會直接顯示，如果同時傳入add_bonus和bonus,則前者無效。
    'balance'             => '持白金會員卡到店消費，可享8折優惠。', //需要設定的餘額全量值，傳入的數值會直接顯示，如果同時傳入add_balance和balance,則前者無效。
    'record_balance'      => '持白金會員卡到店消費，可享8折優惠。', //商家自定義金額消耗記錄，不超過14個漢字。
    'custom_field_value1' => '100', //建立時欄位custom_field1定義型別的最新數值，限制為4個漢字，12位元組。
    'custom_field_value2' => '200', //建立時欄位custom_field2定義型別的最新數值，限制為4個漢字，12位元組。
    'custom_field_value3' => '300', //建立時欄位custom_field3定義型別的最新數值，限制為4個漢字，12位元組。
];

$result = $card->member_card->updateUser($info);
```

## 子商戶

### 新增子商戶

```php
$card->sub_merchant->create(array $attributes); 
```

示例：

```php
$attributes = [
    'brand_name' => 'overtrue',
    'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/iaL1LJM1mF9aRKPZJkmG8xXhiaHqkKSVMMWeN3hLut7X7hicFNjakmxibMLGWpXrEXB33367o7zHN0CwngnQY7zb7g/0',
    'protocol' => 'qIqwTfzAdJ_1-VJFT0fIV53DSY4sZY2WyhkzZzbV498Qgdp-K5HJtZihbHLS0Ys0',
    'end_time' => '1438990559',
    'primary_category_id' => 1,
    'secondary_category_id' => 101,
    'agreement_media_id' => '',
    'operator_media_id' => '',
    'app_id' => '',
];

$result = $card->sub_merchant->create($attributes);
```

### 更新子商戶

```php
$card->sub_merchant->update(int $merchantId, array $info);
```

> - `$merchantId` int - 子商戶 ID
> - `$info` array - 引數與建立子商戶引數一樣

示例：

```php
$info = [
  //...
];
$result = $card->sub_merchant->update('12', $info);
```

## 特殊票券

### 機票值機

```php
$card->boarding_pass->checkin(array $params);
```

### 更新會議門票 - 更新使用者

```php
$card->meeting_ticket->updateUser(array $params);
```

### 更新電影門票 - 更新使用者

```php
$card->movie_ticket->updateUser(array $params);
```

## JSAPI

### 卡券批次下發到使用者

```php
$cards = [
    ['card_id' => 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY', 'outer_id' => 2],
    ['card_id' => 'pdkJ9uJ37aU-tyRj4_grs8S45k1c', 'outer_id' => 3],
];
$json = $card->jssdk->assign($cards); // 返回 json 格式
```

返回 json，在模板裡的用法：

```html
wx.addCard({ cardList:
<?= $json ?>, // 需要開啟的卡券列表 success: function (res) { var cardList = res.cardList; // 新增的卡券列表資訊 } });
```

### 獲取 Ticket

```php
$card->jssdk->getTicket();
// 強制重新整理
$card->jssdk->getTicket(true);
```
