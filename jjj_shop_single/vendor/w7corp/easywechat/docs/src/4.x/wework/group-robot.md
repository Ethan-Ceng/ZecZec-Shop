# 群機器人

## 使用說明
使用前必須先在群組裡面新增機器人，然後將 `Webhook 地址` 中的 `key` 取出來，作為示例中 `$groupKey` 的值。

> Webhook 地址示例：https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=`ab4f609a-3feb-427c-ae9d-b319ca712d36` 

> 微信文件：https://work.weixin.qq.com/api/doc#90000/90136/91770

## 傳送文字型別訊息

快速傳送文字訊息

```php
// 獲取 Messenger 例項
$messenger = $app->group_robot_messenger;

// 群組 key
$groupKey = 'ab4f609a-3feb-427c-ae9d-b319ca712d36';

$messenger->message('大家好，我是本群的"喝水提醒小助手"')->toGroup($groupKey)->send();
// 或者寫成
$messenger->toGroup($groupKey)->send('大家好，我是本群的"喝水提醒小助手"');
```

使用 `Text` 傳送文字訊息

```php
use EasyWeChat\Work\GroupRobot\Messages\Text;

// 準備訊息
$text = new Text('hello');

// 傳送
$messenger->message($text)->toGroup($groupKey)->send();
```

@某人：

```php
use EasyWeChat\Work\GroupRobot\Messages\Text;

// 透過建構函式傳參
$text = new Text('hello', 'her-cat', '18700000000');
//$text = new Text('hello', ['her-cat', 'overtrue'], ['18700000000', '18700000001']);

// 透過 userId
$text->mention('her-cat');
//$text->mention(['her-cat', 'overtrue']);

// 透過手機號
$text->mentionByMobile('18700000000');
//$text->mentionByMobile(['18700000000', '18700000001']);

// @所有人
$text->mention('@all');
//$text->mentionByMobile('@all');

$messenger->message($text)->toGroup($groupKey)->send();
```

## 傳送 Markdown 型別訊息

```php
use EasyWeChat\Work\GroupRobot\Messages\Markdown;

$content = '
# 標題一
## 標題二
<font color="info">綠色</font>
<font color="comment">灰色</font>
<font color="warning">橙紅色</font>
> 引用文字
';

$markdown = new Markdown($content);

$messenger->message($markdown)->toGroup($groupKey)->send();
```

## 傳送圖片型別訊息

```php
use EasyWeChat\Work\GroupRobot\Messages\Image;

$img = file_get_contents('http://res.mail.qq.com/node/ww/wwopenmng/images/independent/doc/test_pic_msg1.png');

$image = new Image(base64_encode($img), md5($img));

$result = $messenger->message($image)->toGroup($groupKey)->send();
```

## 傳送圖文型別訊息

```php
use EasyWeChat\Work\GroupRobot\Messages\News;
use EasyWeChat\Work\GroupRobot\Messages\NewsItem;

$items = [
    new NewsItem([
        'title' => '中秋節禮品領取',
        'description' => '今年中秋節公司有豪禮相送',
        'url' => 'https://www.easywechat.com',
        'image' => 'http://res.mail.qq.com/node/ww/wwopenmng/images/independent/doc/test_pic_msg1.png',
    ]),

    //...
];

$news = new News($items);

$messenger->message($news)->toGroup($groupKey)->send();
```

## 其他方式

使用 `group_robot` 傳送訊息。

```php
$app->group_robot->message('大家好，我是本群的"喝水提醒小助手"')->toGroup($groupKey)->send();
```
