# 訂閱訊息

> 微信文件：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.addTemplate.html

## 組合模板並新增至帳號下的個人模板庫

```php
$tid = 563;     // 模板標題 id，可透過介面獲取，也可登入小程式後臺檢視獲取
$kidList = [1, 2];      // 開發者自行組合好的模板關鍵詞列表，可以透過 `getTemplateKeywords` 方法獲取
$sceneDesc = '提示使用者圖書到期';    // 服務場景描述，非必填

$app->subscribe_message->addTemplate($tid, $kidList, $sceneDesc);
```

## 刪除帳號下的個人模板

```php
$templateId = 'bDmywsp2oEHjwAadTGKkUHpC0RgBVPvfAM7Cu1s03z8';

$app->subscribe_message->deleteTemplate($templateId);
```

## 獲取小程式賬號的類目

```php
$app->subscribe_message->getCategory();
```

## 獲取模板標題的關鍵詞列表

```php
$tid = 563;     // 模板標題 id，可透過介面獲取，也可登入小程式後臺檢視獲取

$app->subscribe_message->getTemplateKeywords($tid);
```

## 獲取帳號所屬類目下的公共模板標題

```php
$ids = [612, 613];  // 類目 id
$start = 0;         // 用於分頁，表示從 start 開始。從 0 開始計數。  
$limit = 30;        // 用於分頁，表示拉取 limit 條記錄。最大為 30。

$app->subscribe_message->getTemplateTitles($ids, $start, $limit);
```

## 獲取當前帳號下的個人模板列表

```php
$app->subscribe_message->getTemplates();
```

## 傳送訂閱訊息

```php
$data = [
    'template_id' => 'bDmywsp2oEHjwAadTGKkUJ-eJEiMiOf7H-dZ7wjdw80', // 所需下發的訂閱模板id
    'touser' => 'oSyZp5OBNPBRhG-7BVgWxbiNZm',     // 接收者（使用者）的 openid
    'page' => '',       // 點選模板卡片後的跳轉頁面，僅限本小程式內的頁面。支援帶引數,（示例index?foo=bar）。該欄位不填則模板無跳轉。
    'data' => [         // 模板內容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
        'date01' => [
            'value' => '2019-12-01',
        ],
        'number01' => [
            'value' => 10,
        ],
    ],
];

$app->subscribe_message->send($data);
```
