# 卡券
-

> Version `>=3.1.2`

## 獲取例項

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$card = $app->card;
```


## API列表

### 獲取卡券顏色

```php
$card->getColors();
```

example:

```php
$result = $card->getColors();
```



### 建立卡券

建立卡券介面是微信卡券的基礎介面，用於建立一類新的卡券，獲取card_id，建立成功並透過稽核後，商家可以透過文件提供的其他介面將卡券下發給使用者，每次成功領取，庫存數量相應扣除。

```php
$card->create($cardType, $baseInfo, $especial);
```

- `cardType` string - 是要新增卡券的型別
- `baseInfo` array  - 為卡券的基本資料
- `especial` array  - 是擴充套件欄位

example:

```php
<?php

	$cardType = 'GROUPON';

    $baseInfo = [
        'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/2aJY6aCPatSeibYAyy7yct9zJXL9WsNVL4JdkTbBr184gNWS6nibcA75Hia9CqxicsqjYiaw2xuxYZiaibkmORS2oovdg/0',
        'brand_name' => '測試商戶造夢空間',
        'code_type' => 'CODE_TYPE_QRCODE',
        'title' => '測試',
        'sub_title' => '測試副標題',
        'color' => 'Color010',
        'notice' => '測試使用時請出示此券',
        'service_phone' => '15311931577',
        'description' => "測試不可與其他優惠同享\n如需團購券發票，請在消費時向商戶提出\n店內均可使用，僅限堂食",

        'date_info' => [
          'type' => 'DATE_TYPE_FIX_TERM',
          'fixed_term' => 90, //表示自領取後多少天內有效，不支援填寫0
          'fixed_begin_term' => 0, //表示自領取後多少天開始生效，領取後當天生效填寫0。
        ],

        'sku' => [
          'quantity' => '0', //自定義code時設定庫存為0
        ],

        'location_id_list' => ['461907340'],  //獲取門店位置poi_id，具備線下門店的商戶為必填

        'get_limit' => 1,
        'use_custom_code' => true, //自定義code時必須為true
        'get_custom_code_mode' => 'GET_CUSTOM_CODE_MODE_DEPOSIT',  //自定義code時設定
        'bind_openid' => false,
        'can_share' => true,
        'can_give_friend' => false,
        'center_title' => '頂部居中按鈕',
        'center_sub_title' => '按鈕下方的wording',
        'center_url' => 'http://www.qq.com',
        'custom_url_name' => '立即使用',
        'custom_url' => 'http://www.qq.com',
        'custom_url_sub_title' => '6個漢字tips',
        'promotion_url_name' => '更多優惠',
        'promotion_url' => 'http://www.qq.com',
        'source' => '造夢空間',
      ];

    $especial = [
      'deal_detail' => 'deal_detail',
    ];

    $result = $card->create($cardType, $baseInfo, $especial);
```



### 建立二維碼

開發者可呼叫該介面生成一張卡券二維碼供使用者掃碼後新增卡券到卡包。

自定義Code碼的卡券呼叫介面時，POST資料中需指定code，非自定義code不需指定，指定openid同理。指定後的二維碼只能被使用者掃描領取一次。

```php
$card->QRCode($cards);
```

- `cards` array - 卡券相關資訊

example:

```php
//領取單張卡券
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

$result = $card->QRCode($cards);
```

```php
//領取多張卡券
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

$result = $card->QRCode($cardList);
```

請求成功返回值示例：

```php
array(4) {
  ["ticket"]=>
  string(96) "gQHa7joAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xLzdrUFlQMHJsV3Zvanc5a2NzV1N5AAIEJUVyVwMEAKd2AA=="
  ["expire_seconds"]=>
  int(7776000)
  ["url"]=>
  string(43) "http://weixin.qq.com/q/7kPYP0rlWvojw9kcsWSy"
  ["show_qrcode_url"]=>
  string(151) "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQHa7joAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xLzdrUFlQMHJsV3Zvanc5a2NzV1N5AAIEJUVyVwMEAKd2AA%3D%3D"
}
```

成功返回值列表說明：

|       引數名       | 描述                                       |
| :-------------: | :--------------------------------------- |
|     ticket      | 獲取的二維碼ticket，憑藉此ticket呼叫[透過ticket換取二維碼介面](http://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1443433542&token=&lang=zh_CN)可以在有效時間內換取二維碼。 |
| expire_seconds  | 二維碼的有效時間                                 |
|       url       | 二維碼圖片解析後的地址，開發者可根據該地址自行生成需要的二維碼圖片        |
| show_qrcode_url | 二維碼顯示地址，點選後跳轉二維碼頁面                       |



### ticket 換取二維碼圖片

獲取二維碼 ticket 後，開發者可用 ticket 換取二維碼圖片。

```php
$card->showQRCode($ticket);
```

- `ticket` string  - 獲取的二維碼 ticket，憑藉此 ticket 可以在有效時間內換取二維碼。

example:

```php
$ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
$result = $card->showQRCode($ticket);
```


### ticket 換取二維碼連結

```php
$card->getQRCodeUrl($ticket);  //獲取的二維碼ticket
```

example:

```php
$ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
$card->getQRCodeUrl($ticket);
```

### JSAPI 卡券批次下發到使用者

微信卡券：JSAPI 卡券

```php
$cards = [
    ['card_id' => 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY', 'outer_id' => 2],
    ['card_id' => 'pdkJ9uJ37aU-tyRj4_grs8S45k1c', 'outer_id' => 3],
];
$json = $card->jsConfigForAssign($cards); // 返回 json 格式
```

返回 json，在模板裡的用法：

```html
wx.addCard({
    cardList: <?= $json ?>, // 需要開啟的卡券列表
    success: function (res) {
        var cardList = res.cardList; // 新增的卡券列表資訊
    }
});
```

### 建立貨架介面

開發者需呼叫該介面建立貨架連結，用於卡券投放。建立貨架時需填寫投放路徑的場景欄位。

```php
$card->createLandingPage($banner, $pageTitle, $canShare, $scene, $cards);
```

- `banner` string -頁面的 banner 圖;
- `pageTitle` string - 頁面的 title
- `canShare` bool - 頁面是不是可以分享，true 或 false
- `scene`  string - 投放頁面的場景值，具體值請參考下面的 example
- `cards`  array - 卡券列表，每個元素有兩個欄位

example:

```php
$banner     = 'http://mmbiz.qpic.cn/mmbiz/iaL1LJM1mF9aRKPZJkmG8xXhiaHqkKSVMMWeN3hLut7X7hicFN';
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



### 匯入code介面

在自定義code卡券成功建立並且透過稽核後，必須將自定義code按照與發券方的約定數量呼叫匯入code介面匯入微信後臺。

```php
$card->deposit($card_id, $code);
```

- `cardId` string - 要匯入code的卡券ID
- `code` string - 要匯入微信卡券後臺的自定義 code，最多100個

example:

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';
$code    = ['11111', '22222', '33333'];

$result = $card->deposit($cardId, $code);
```



### 查詢匯入code數目

```php
$card->getDepositedCount($cardId);  //要匯入code的卡券ID
```

example:

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$result = $card->getDepositedCount($cardId);
```



### 核查code介面

為了避免出現匯入差錯，強烈建議開發者在查詢完code數目的時候核查code介面校驗code匯入微信後臺的情況。

```php
$card->checkCode($cardId, $code);
```

example:

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$code = ['807732265476', '22222', '33333'];

$result = $card->checkCode($cardId, $code);
```



### 圖文訊息群髮卡券

特別注意：目前該介面僅支援填入非自定義code的卡券,自定義code的卡券需先進行code匯入後呼叫。

```php
$card->getHtml($cardId);
```

example:

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$result = $card->getHtml($cardId);
```



### 設定測試白名單

同時支援“openid”、“username”兩種欄位設定白名單，總數上限為10個。

```php
$card->setTestWhitelist($openids); // 使用 openid
$card->setTestWhitelistByUsername($usernames); // 使用 username
```

- `openids` array - 測試的openid列表
- `usernames` array  - 測試的微訊號列表

example:

```php
// by openid
$openids   = [$openId, $openId2, $openid3...];
$result = $card->setTestWhitelist($openids);

// by username
$usernames = ['tianye0327', 'iovertrue'];
$result = $card->setTestWhitelistByUsername($usernames);
```

### 查詢Code介面

```php
$card->getCode($code, $checkConsume, $cardId);
```

- checkConsume  是否校驗code核銷狀態，true和false

example:

```php
$code          = '736052543512';
$checkConsume = true;
$cardId       = 'pdkJ9uDgnm0pKfrTb1yV0dFMO_Gk';

$result = $card->getCode($code, $checkConsume, $cardId);
```



### 核銷Code介面

```php
$card->consume($code);

// 或者指定 cardId

$card->consume($code, $cardId);
```

example:

```php
$cardId = 'pdkJ9uDmhkLj6l5bm3cq9iteQBck';
$code    = '789248558333';

$result = $card->consume($code);

//或

$result = $card->consume($code, $cardId);
```



### Code解碼介面

```php
$card->decryptCode($encryptedCode);
```

example:

```php
$encryptedCode = 'XXIzTtMqCxwOaawoE91+VJdsFmv7b8g0VZIZkqf4GWA60Fzpc8ksZ/5ZZ0DVkXdE';

$result = $card->decryptCode($encryptedCode);
```



### 獲取使用者已領取卡券介面

用於獲取使用者卡包裡的，屬於該appid下所有**可用卡券，包括正常狀態和未生效狀態**。

```php
$card->getUserCards($openid, $cardId);
```

example:

```php
$openid  = 'odkJ9uDUz26RY-7DN1mxkznfo9xU';
$cardId = ''; //卡券ID。不填寫時預設查詢當前appid下的卡券。

$result = $card->getUserCards($openid, $cardId);
```



### 檢視卡券詳情

開發者可以呼叫該介面查詢某個card_id的建立資訊、稽核狀態以及庫存數量。

```php
$card->getCard($cardId);
```

example:

```php
$cardId = 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY';

$result = $card->getCard($cardId);
```



### 批次查詢卡列表

```php
$card->lists($offset, $count, $statusList);
```

- `offset` int - 查詢卡列表的起始偏移量，從0開始
- `count` int - 需要查詢的卡片的數量
- `statusList` -  支援開發者拉出指定狀態的卡券列表，詳見example

example:

```php
$offset      = 0;
$count       = 10;

//CARD_STATUS_NOT_VERIFY,待稽核；
//CARD_STATUS_VERIFY_FAIL,稽核失敗；
//CARD_STATUS_VERIFY_OK，透過稽核；
//CARD_STATUS_USER_DELETE，卡券被商戶刪除；
//CARD_STATUS_DISPATCH，在公眾平臺投放過的卡券；
$statusList = 'CARD_STATUS_VERIFY_OK';

$result = $card->lists($offset, $count, $statusList);
```



### 更改卡券資訊介面

支援更新所有卡券型別的部分通用欄位及特殊卡券中特定欄位的資訊。

```php
$card->update($cardId, $type, $baseInfo);
```

- `type` string - 卡券型別

example:

```php
$cardId = 'pdkJ9uCzKWebwgNjxosee0ZuO3Os';

$type = 'groupon';

$baseInfo = [
    'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/2aJY6aCPatSeibYAyy7yct9zJXL9WsNVL4JdkTbBr184gNWS6nibcA75Hia9CqxicsqjYiaw2xuxYZiaibkmORS2oovdg/0',
    'center_title' => '頂部居中按鈕',
    'center_sub_title' => '按鈕下方的wording',
    'center_url' => 'http://www.baidu.com',
    'custom_url_name' => '立即使用',
    'custom_url' => 'http://www.qq.com',
    'custom_url_sub_title' => '6個漢字tips',
    'promotion_url_name' => '更多優惠',
    'promotion_url' => 'http://www.qq.com',
];

$result = $card->update($cardId, $type, $baseInfo);
```



### 設定微信買單介面

```php
$card->setPayCell($cardId, $isOpen);
```

- `isOpen` string - 是否開啟買單功能，填 true/false，不填預設 true

example:

```php
$cardId = 'pdkJ9uH7u11R-Tu1kilbaW_zDFow';
$isOpen = true;

$result = $card->setPayCell($cardId, $isOpen);
```



### 修改庫存介面

```php
$card->increaseStock($cardId, $amount); // 增加庫存
$card->reductStock($cardId, $amount); // 減少庫存
```

- `cardId` string - 卡券 ID
- `amount` int - 修改多少庫存

example:

```php
$cardId = 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY';

$result = $card->increaseStock($cardId, 100);
```


### 更改Code介面

```php
$card->updateCode($code, $newCode, $cardId);
```

- `newCode` string - 變更後的有效Code碼

example:

```php
$code     = '148246271394';
$newCode = '659266965266';
$cardId  = '';

$result = $card->updateCode($code, $newCode, $cardId);
```



### 刪除卡券介面

```php
$card->delete($cardId);
```

example:

```php
$cardId = 'pdkJ9uItT7iUpBp4GjZp8Cae0Vig';

$result = $card->delete($cardId);
```



### 設定卡券失效

```php
$card->disable($code, $cardId);
```

example:

```php
$code    = '736052543512';
$cardId = '';

$result = $card->disable($code, $cardId);
```



### 會員卡介面啟用

```php
$result = $card->activate($info);
```

- `info` - 需要啟用的會員卡資訊

example:

```php
$activate = [
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

$result = $card->activate($activate);
```



### 設定開卡欄位介面

```php
$card->activateUserForm($cardId, $requiredForm, $optionalForm);
```

- `requiredForm` array - 會員卡啟用時的必填選項
- `optionalForm` array - 會員卡啟用時的選填項

example:

```php
$cardId = 'pdkJ9uJYAyfLXsUCwI2LdH2Pn1AU';

$requiredForm = [
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
];

$optionalForm = [
    'optional_form' => [
        'common_field_id_list' => [
            'USER_FORM_INFO_FLAG_EMAIL',
        ],
        'custom_field_list' => [
            '喜歡的食物',
        ],
    ],
];

$result = $card->activateUserForm($cardId, $requiredForm, $optionalForm);
```



### 拉取會員資訊介面

```php
$card->getMemberCardUser($cardId, $code);
```

example:

```php
$cardId = 'pbLatjtZ7v1BG_ZnTjbW85GYc_E8';
$code    = '916679873278';

$result = $card->getMemberCardUser($cardId, $code);
```



### 更新會員資訊

```php
$card->updateMemberCardUser($updateUser);
```

- `updateUser` array - 可以更新的會員資訊

example:

```php
$updateUser = [
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

$result = $card->updateMemberCardUser($updateUser);
```



### 新增子商戶

```php
$card->craeteSubMerchant($brandName, $logoUrl, $protocol, $endTime, $primaryCategoryId, $secondaryCategoryId, $agreementMediaId, $operatorMediaId, $appId); 
```

- `brand_name` string - 子商戶名稱（12個漢字內），該名稱將在制券時填入並顯示在卡券頁面上
- `logo_url`  string - 子商戶 logo，可透過上傳 logo 介面獲取。該 logo 將在制券時填入並顯示在卡券頁面上
- `protocol`  string - 授權函ID，即透過上傳臨時素材介面上傳授權函後獲得的 meida_id
- `primary_category_id`  int - 一級類目id,可以透過本文件中介面查詢
- `secondary_category_id` int - 二級類目id，可以透過本文件中介面查詢
- `agreement_media_id`  string - 營業執照或個體工商戶營業執照彩照或掃描件
- `operator_media_id`  string - 營業執照內登記的經營者身份證彩照或掃描件
- `app_id`  string - 子商戶的公眾號 app_id，配置後子商戶卡券券面上的 app_id 為該 app_id, app_id 須經過認證

example:

```php
$info = [
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

$result = $card->createSubMerchant($info);
```

### 更新子商戶

```php
$card->updateSubMerchant($merchantId, $info);
```

- `$merchantId` int - 子商戶 ID
- `$info` array - 引數與建立子商戶引數一樣

example:

```php
$info = [
  //...
];
$result = $card->updateSubMerchant('12', $info);
```

### 卡券開放類目查詢介面

```php
$card->getCategories();
```

example:

```php
$result = $card->getCategories();
```

關於卡券介面的使用請參閱官方文件：http://mp.weixin.qq.com/wiki/
