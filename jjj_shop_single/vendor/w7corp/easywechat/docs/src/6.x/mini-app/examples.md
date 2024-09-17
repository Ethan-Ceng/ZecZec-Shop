# 示例

> 👏🏻 歡迎點選本頁下方 "幫助我們改善此頁面！" 連結參與貢獻更多的使用示例！

<details>
    <summary>生成小程式碼（wxacode.getUnlimited）</summary>

[官方文件：wxacode.getUnlimited](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html)

```php
try {
    $response = $app->getClient()->postJson('/wxa/getwxacodeunlimit', [
        'scene' => '123',
        'page' => 'pages/index/index',
        'width' => 430,
        'check_path' => false,
    ]);
    
    $path = $response->saveAs('/tmp/wxacode-123.png');
} catch (\Throwable $e) {
    // 失敗
    echo $e->getMessage();
}
```
</details>

<details>
    <summary>獲取手機號（phonenumber.getPhoneNumber）</summary>

[官方文件：phonenumber.getPhoneNumber](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/phonenumber/phonenumber.getPhoneNumber.html)

```php
// routes/api.php
use EasyWeChat\MiniApp\Application;
Route::post('getPhoneNumber', function () {
    // $app 例項化步驟這裡省略 
    $data = [
      'code' => (string) request()->get('code'),
    ];

    return $app->getClient()->postJson('wxa/business/getuserphonenumber', $data);
  }
}
```
</details>

<!--
<details>
    <summary>標題</summary>
內容
</details>
-->
