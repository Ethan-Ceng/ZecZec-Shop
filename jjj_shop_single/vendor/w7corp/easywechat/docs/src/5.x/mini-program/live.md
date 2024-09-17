# 訂閱訊息

> 微信文件：https://developers.weixin.qq.com/miniprogram/dev/framework/liveplayer/live-player-plugin.html

> tips:微信規定以下兩個介面呼叫限制共享 **500次/天** 建議開發者自己做快取，合理分配呼叫頻次。

## 獲取直播房間列表

```php
$app->live->getRooms();
```

## 獲取回放源影片

```php
$roomId = 1;    //直播房間id

$app->live->getPlaybacks($roomId);
```