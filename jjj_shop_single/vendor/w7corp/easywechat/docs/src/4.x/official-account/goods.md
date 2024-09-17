# 返佣商品

> 微信文件：https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&key=11533749572M9ODP&version=1&lang=zh_CN&platform=2

## 匯入商品

每次呼叫支援批次匯入不超過1000條的商品資訊。每分鐘單個商戶全域性呼叫次數不得超過200次。每天呼叫次數不得超過100萬次。每次請求包大小不超過2M。

```php
$data = [
    [
        'pid' => 'pid001',
        'image_info' => [
            'main_image_list' => [
                [
                    'url' => 'http://www.google.com/a.jpg',
                ],
                [
                    'url' => 'http://www.google.com/b.jpg',
                ],
            ],
        ],
        
        //...
    ],
    
    //...
];

$result = $app->goods->add($data);

// $result:
//{
//    "errcode": 0,
//    "errmsg": "ok",
//    "status_ticket": "115141102647330200"
//}
```

`status_ticket` 用於獲取此次匯入的詳細結果。

## 更新商品

更新時，欄位不填代表不更新該欄位（此處的欄位不填，代表無此欄位，而不是把欄位的值設為空，設為空即代表更新該欄位為空）。

對於字串型別的選填欄位，如副標題，若清空不展示，則可設定為空；對於數字型別的選填欄位，如原價，若清空不展示，則需設定為0。

> 基本欄位更新中 `pid` 為必填欄位，且無法修改

```php
$data = [
    [
        'pid' => 'pid001',
        'image_info' => [
            'main_image_list' => [
                [
                    'url' => 'http://www.baidu.com/c.jpg',
                ],
                [
                    'url' => 'http://www.baidu.com/d.jpg',
                ],
            ],
        ],
        
        //...
    ],
    
    //...
];
 
$result = $app->goods->update($data);
 
// $result:
//{
//    "errcode": 0,
//    "errmsg": "ok",
//    "status_ticket": "115141102647330200"
//}
```

> 說明：匯入商品和更新商品使用的是同一個介面。
 
## 查詢匯入/更新商品狀態
 
用於查詢匯入或更新商品的結果，當匯入或更新商品失敗時，若為系統錯誤可進行重試；若為其他錯誤，請排查解決後進行重試。

```php
$status_ticket = '115141102647330200';

$result = $app->goods->status($status_ticket);

// $result:
//{
//    "errcode": 0,
//    "errmsg": "ok",
//    "result": {
//        "succ_cnt": 2,
//        "fail_cnt": 0,
//        "total_cnt": 2,
//        "progress": "100.00%",
//        "statuses": [
//            {
//                "pid": "pid001",
//                "ret": 0,
//                "err_msg": "success",
//                "err_msg_zh_cn": "成功"
//            },
//            {
//                "pid": "pid002",
//                "ret": 0,
//                "err_msg": "success",
//                "err_msg_zh_cn": "成功"
//            }
//        ]
//    }
//}
```

## 獲取單個商品資訊

使用該介面獲取已匯入的商品資訊，供驗證資訊及抽查匯入情況使用。

```php
$pid = 'pid001';

$app->goods->get($pid);
```

> 返回結果中的 `product` 欄位內容與 `匯入商品介面` 欄位一致，匯入時未設定的值有可能獲取時仍會返回，但顯示為空

## 分頁獲取商品資訊

使用該介面可獲取已匯入的全量商品資訊，供全量驗證資訊使用。

```php
$context = '';  // page 為 1 時傳空即可。當 page 大於 1 時必填，填入上一次訪問本介面返回的 page_context。
$page = 1;      // 頁碼
$size = 10;     // 每頁資料大小，目前限制為100以內，注意一次全量驗證過程中該引數的值需保持不變

$app->goods->list($context, $page, $size);
```

> 返回結果中的 `product` 欄位內容與 `匯入商品介面` 欄位一致，匯入時未設定的值有可能獲取時仍會返回，但顯示為空。
> `page_context` 欄位用於獲取下一頁資料時使用。
