# 多客服訊息轉發



多客服的訊息轉發絕對是超級的簡單，轉發的訊息型別為 `transfer`：

```php


  // 轉發收到的訊息給客服
  $server->setMessageHandler(function($message) {
      return new \EasyWeChat\Message\Transfer();
  });

  $result = $server->serve();

  echo $result;
```

當然，你也可以指定轉發給某一個客服：

```php
$server->setMessageHandler(function($message) {
    $transfer = new \EasyWeChat\Message\Transfer();
    $transfer->account($account);// 或者 $transfer->to($account);

    return $transfer;
});
```

更多請參考 [微信官方文件](http://mp.weixin.qq.com/wiki/) **多客服訊息轉發** 章節