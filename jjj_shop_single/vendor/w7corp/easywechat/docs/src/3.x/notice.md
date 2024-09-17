# 模板訊息

模板訊息僅用於公眾號向用戶傳送重要的服務通知，只能用於符合其要求的服務場景中，如信用卡刷卡通知，商品購買成功通知等。不支援廣告等營銷類訊息以及其它所有可能對使用者造成騷擾的訊息。

## 獲取例項

```php
<?php
use EasyWeChat\Foundation\Application;
// ...
$app = new Application($options);

$notice = $app->notice;
```

### API

+ `boolean setIndustry($industryId1, $industryId2)` 修改賬號所屬行業；
+ `array getIndustry()` 返回所有支援的行業列表，用於做下拉選擇行業視覺化更新；
+ `string  addTemplate($shortId)` 新增模板並獲取模板ID；
+ `collection send($message)` 傳送模板訊息, 返回訊息ID；
+ `array  getPrivateTemplates()` 獲取所有模板列表；
+ `array  deletePrivateTemplate($templateId)` 刪除指定ID的模板。

非連結呼叫方法：

```php
$messageId = $notice->send([
        'touser' => 'user-openid',
        'template_id' => 'template-id',
        'url' => 'xxxxx',
        'data' => [
            //...
        ],
    ]);
```

鏈式呼叫方法:

    設定模板ID：template / templateId / uses
    設定接收者openId: to / receiver
    設定詳情連結：url / link / linkTo
    設定模板資料：data / with

    以上方法都支援 `withXXX` 與 `andXXX` 形式鏈式呼叫

```php
$messageId = $notice->to($userOpenId)->uses($templateId)->andUrl($url)->data($data)->send();
// 或者
$messageId = $notice->to($userOpenId)->url($url)->template($templateId)->andData($data)->send();
// 或者
$messageId = $notice->withTo($userOpenId)->withUrl($url)->withTemplate($templateId)->withData($data)->send();
// 或者
$messageId = $notice->to($userOpenId)->url($url)->withTemplateId($templateId)->send();
// ... ...
```

## 示例:

### 模板

```
{{ first.DATA }}

商品明細：

名稱：{{ name.DATA }}
價格：{{ price.DATA }}

{{ remark.DATA }}
```

傳送模板訊息：

```php
$userId = 'OPENID';
$templateId = 'ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY';
$url = 'http://overtrue.me';
$data = array(
         "first"  => "恭喜你購買成功！",
         "name"   => "巧克力",
         "price"  => "39.8元",
         "remark" => "歡迎再次購買！",
        );

$result = $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($userId)->send();
var_dump($result);

// {
//      "errcode":0,
//      "errmsg":"ok",
//      "msgid":200228332
//  }
```

結果：

![notice-demo](http://7u2jwa.com1.z0.glb.clouddn.com/QQ20160111-0@2x.png)

## 模板資料

為了方便大家開發，我們拓展支援以下格式的模板資料，其它格式的資料可能會導致介面呼叫失敗：

- 所有資料項顏色一樣的（這是方便的一種方式）:

    ```php
    $data = array(
        "first"    => "恭喜你購買成功！",
        "keynote1" => "巧克力",
        "keynote2" => "39.8元",
        "keynote3" => "2014年9月16日",
        "remark"   => "歡迎再次購買！",
    );
    ```
  預設顏色為'#173177', 你可以透過 `defaultColor($color)` 來修改

- 獨立設定每個模板項顏色的：

    + 簡便型：

        ```php
        $data = array(
            "first"    => array("恭喜你購買成功！", '#555555'),
            "keynote1" => array("巧克力", "#336699"),
            "keynote2" => array("39.8元", "#FF0000"),
            "keynote3" => array("2014年9月16日", "#888888"),
            "remark"   => array("歡迎再次購買！", "#5599FF"),
        );
        ```
    + 複雜型（也是微信官方唯一支援的方式，估計沒有人想這麼用）：

        ```php
        $data = array(
            "first"    => array("value" => "恭喜你購買成功！", "color" => '#555555'),
            "keynote1" => array("value" => "巧克力", "color" => "#336699"),
            "keynote2" => array("value" => "39.8元","color" => "#FF0000"),
            "keynote3" => array("value" => "2014年9月16日", "color" => "#888888"),
            "remark"   => array("value" => "歡迎再次購買！", "color" => "#5599FF"),
        );
        ```

關於模板訊息的使用請參考 [微信官方文件](http://mp.weixin.qq.com/wiki/)
