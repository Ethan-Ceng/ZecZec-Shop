# 模板訊息

模板訊息僅用於公眾號向用戶傳送重要的服務通知，只能用於符合其要求的服務場景中，如信用卡刷卡通知，商品購買成功通知等。不支援廣告等營銷類訊息以及其它所有可能對使用者造成騷擾的訊息。

## 修改賬號所屬行業

```php
$app->template_message->setIndustry($industryId1, $industryId2);
```

## 獲取支援的行業列表

```php
$app->template_message->getIndustry();
```

## 新增模板

在公眾號後臺獲取 `$shortId` 並新增到賬戶。

```php
$app->template_message->addTemplate($shortId);
```

## 獲取所有模板列表

```php
$app->template_message->getPrivateTemplates();
```

## 刪除模板

```php
$app->template_message->deletePrivateTemplate($templateId);
```

## 傳送模板訊息

```php
$app->template_message->send([
        'touser' => 'user-openid',
        'template_id' => 'template-id',
        'url' => 'https://easywechat.org',
        'miniprogram' => [
                'appid' => 'xxxxxxx',
                'pagepath' => 'pages/xxx',
        ],
        'data' => [
            'key1' => 'VALUE',
            'key2' => 'VALUE2',
            ...
        ],
    ]);
```
> 如果 url 和 miniprogram 欄位都傳，會優先跳轉小程式。

## 傳送一次性訂閱訊息

```php
$app->template_message->sendSubscription([
        'touser' => 'user-openid',
        'template_id' => 'template-id',
        'url' => 'https://easywechat.org',
        'scene' => 1000,
        'data' => [
            'key1' => 'VALUE',
            'key2' => 'VALUE2',
            ...
        ],
    ]);
```

> 如果你想為傳送的內容欄位指定顏色，你可以將 "data" 部分寫成下面 4 種不同的樣式，不寫 `color` 將會是預設黑色：

```php
'data' => [
    'foo' => '你好',  // 不需要指定顏色
    'bar' => ['你好', '#F00'], // 指定為紅色
    'baz' => ['value' => '你好', 'color' => '#550038'], // 與第二種一樣
    'zoo' => ['value' => '你好'], // 與第一種一樣
]
```
