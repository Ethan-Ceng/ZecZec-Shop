# 微信客服

## 服務端(接收訊息)
我們在企業微信 ”微信客服” 應用開啟API接收訊息的功能    
將設定頁面的 token 與 aes key 配置到 agents 下對應的應用內   
> 注意: 需要使用“微信客服”secret所獲取的accesstoken來呼叫
```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx',
    // server config
    'token' => 'xxxxxxxxx',
    'aes_key' => 'xxxxxxxxxxxxxxxxxx',

    //...
];

$app = Factory::work($config);
```

接著配置服務端與公眾號的服務端用法一樣：

請參考微信客服文件 https://open.work.weixin.qq.com/api/doc/90000/90135/94670

```php
$app->server->push(function($message){
   // $message['FromUserName'] // 訊息來源
   // $message['MsgType'] // 訊息型別：event ....
    
    return 'Hello easywechat.';
});

$response = $app->server->serve();

$response->send();
```

`$response` 為 `Symfony\Component\HttpFoundation\Response` 例項，根據你的框架情況來決定如何處理響應。

## 客服帳號管理

### 新增客服帳號

```php
$app->kf_account->add(string $name, string $mediaId);
```

### 刪除客服帳號

```php
$app->kf_account->del(string $openKfId);
```

### 修改客服帳號

```php
$app->kf_account->update(string $openKfId, string $name, string $mediaId);
```

### 獲取客服帳號列表

```php
$app->kf_account->list();
```

### 獲取客服帳號連結

```php
$app->kf_account->getAccountLink(string $openKfId, string $scene);
```

## 接待人員管理

### 新增接待人員

```php
$app->kf_servicer->add(string $openKfId, array $userIds);
```

### 刪除接待人員

```php
$app->kf_servicer->del(string $openKfId, array $userIds);
```

### 獲取接待人員列表

```php
$app->kf_servicer->list(string $openKfId);
```

## 會話分配與訊息收發

### 獲取會話狀態

```php
$app->kf_message->state(string $openKfId, string $externalUserId);
```

### 變更會話狀態

```php
$app->kf_message->updateState(string $openKfId, string $externalUserId, int $serviceState, string $serviceUserId);
```

### 讀取訊息

```php
$app->kf_message->sync(string $cursor, string $token, int $limit);
```

### 傳送訊息

```php
$app->kf_message->send(array $params);
```

### 傳送事件響應訊息

```php
$app->kf_message->event(array $params);
```