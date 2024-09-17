# 模板訊息

## 獲取小程式模板庫標題列表

```
$app->template_message->list($offset, $count);
```

## 獲取模板庫某個模板標題下關鍵詞庫

```
$app->template_message->get($id);
```

## 組合模板並新增至帳號下的個人模板庫

```
$app->template_message->add($id, $keywordIdList);
```

## 獲取帳號下已存在的模板列表

```
$app->template_message->getTemplates($offset, $count);
```

## 刪除帳號下的某個模板

```
$app->template_message->delete($templateId);
```

## 傳送模板訊息

```php
$app->template_message->send([
    'touser' => 'user-openid',
    'template_id' => 'template-id',
    'page' => 'index',
    'form_id' => 'form-id',
    'data' => [
        'keyword1' => 'VALUE',
        'keyword2' => 'VALUE2',
        // ...
    ],
]);
```
