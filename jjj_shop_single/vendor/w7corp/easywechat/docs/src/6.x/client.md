# API 呼叫

與以往版本不同的是，SDK 不再內建具體 API 的邏輯，所有的 API 均交由開發者自行呼叫，以更新使用者備註為例：

```php
$api = $app->getClient();

$response = $api->post('/cgi-bin/user/info/updateremark', [
    'json' => [
            "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
            "remark" => "pangzi"
        ]
    ]);

// or
$response = $api->postJson('/cgi-bin/user/info/updateremark', [
    "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
    "remark" => "pangzi"
]);
```

## 語法說明

```php
get(string $uri, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
post(string $uri, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
postJson(string $url, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
patch(string $uri, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
put(string $uri, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
delete(string $uri, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
```

`$options` 為請求引數，可以指定 `query`/`body`/`json`/`xml`/`headers` 等等，具體請參考：[HttpClientInterface::OPTIONS_DEFAULTS](https://github.com/symfony/symfony/blob/6.1/src/Symfony/Contracts/HttpClient/HttpClientInterface.php)

---

## 請求引數

### GET

```php
$response = $api->get('/cgi-bin/user/list'， [
    'next_openid' => 'OPENID1',
]);
```

### POST

```php
$response = $api->post('/cgi-bin/user/info/updateremark', [
    'body' => \json_encode([
            "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
            "remark" => "pangzi"
        ])
    ]);
```

或者可以簡寫為：

```php
$response = $api->postJson('/cgi-bin/user/info/updateremark', [
    "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
    "remark" => "pangzi"
]);
```

或者指定 xml 格式：

```php
$response = $api->postXml('/mmpaymkttransfers/promotion/transfers', [
    'mch_appid' => $app->getConfig()['app_id'],
    'mchid' => $app->getConfig()['mch_id'],
    'partner_trade_no' => '202203081646729819743',
    'openid' => 'ogn1H45HCRxVRiEMLbLLuABbxxxx',
    'check_name' => 'FORCE_CHECK',
    're_user_name'=> 'overtrue',
    'amount' => 100,
    'desc' => '理賠',
 ]);
```

### 請求證書

你可以在請求支付時指定證書，以微信支付 V2 為例：

```php
$response = $api->post('/mmpaymkttransfers/promotion/transfers', [
    'xml' => [
        'mch_appid' => $app->getConfig()['app_id'],
        'mchid' => $app->getConfig()['mch_id'],
        'partner_trade_no' => '202203081646729819743',
        'openid' => 'ogn1H45HCRxVRiEMLbLLuABbxxxx',
        'check_name' => 'FORCE_CHECK',
        're_user_name'=> 'overtrue',
        'amount' => 100,
        'desc' => '理賠',
    ],
    'local_cert' => $app->getConfig()['cert_path'],
    'local_pk' => $app->getConfig()['key_path'],
    ]);
```

> 參考：[symfony/http-client#options](https://symfony.com/doc/current/reference/configuration/framework.html#local-cert)

### 檔案上傳

你有兩種上傳檔案的方式可以選擇：

#### 從指定路徑上傳

```php
use EasyWeChat\Kernel\Form\File;
use EasyWeChat\Kernel\Form\Form;

$options = Form::create(
    [
        'media' => File::fromPath('/path/to/image.jpg'),
    ]
)->toArray();

$response = $api->post('cgi-bin/media/upload?type=image', $options);
```

#### 從二進位制內容上傳

```php
use EasyWeChat\Kernel\Form\File;
use EasyWeChat\Kernel\Form\Form;

$options = Form::create(
    [
        'media' => File::withContents($contents, 'image.jpg'), // 注意：請指定檔名
    ]
)->toArray();

$response = $api->post('cgi-bin/media/upload?type=image', $options);
```

#### 簡化寫法 <version-tag>6.4.0+</version-tag>

上面的兩種傳法都可以簡寫為下面的方式：

```php
// withFile(string $localPath, string $formName = 'file', string $filename = null)
$media = $client->withFile($path, 'media')->post('cgi-bin/media/upload?type=image');

// withFileContents(string $contents, string $formName = 'file', string $filename = null)
$media = $client->withFileContents($contents, 'media', 'filename.png')->post('cgi-bin/media/upload?type=image');
```

## 自定義 access_token

```php
$client->withAccessToken('access_token');
$client->get('xxxx');
$client->post('xxxx');
//...
```

## 預置引數的傳遞 <version-tag>6.4.0+</version-tag>

在呼叫 API 的時候難免有的需要傳遞賬號的一些資訊，尤其是支付相關的 API，例如[查詢訂單](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_2.shtml)：

```php
$client->get('v3/pay/transactions/id/1217752501201407033233368018', [
    'mchid' => $app->getAccount()->getMchid(),
]);
```

不得不把商戶號這種基礎資訊再讀取傳遞一遍，比較麻煩，設計瞭如下的簡化方案：

```php
$client->withMchid()->get('v3/pay/transactions/id/1217752501201407033233368018');
```

原理就是 `with` + `配置 key`：

```php
$client->withAppid()->post('/path/to/resources', [...]);
$client->withAppid()->withMchid()->post('/path/to/resources', [...]);
```

也可以自定義值：

```php
$client->withAppid('12345678')->post('/path/to/resources', [...]);
// or
$client->with('appid', '123456')->post('/path/to/resources', [...]);
```

還可以設定別名：把 `appid` 作為引數 `mch_appid` 值使用：

```php
$client->withAppidAs('mch_appid')->post('/path/to/resources', [...]);
```

其它通用方法：

```php
$client->with('appid')->post(...)
$client->with(['appid', 'mchid'])->post(...)
$client->with(['appid' => '1234565', 'mchid'])->post(...)
```

---

## 處理響應

API Client 基於 [symfony/http-client](https://github.com/symfony/http-client) 實現，你可以透過以下方式對響應值進行訪問：

### 異常處理 <version-tag>6.3.0+</version-tag>

當請求失敗，例如狀態碼不為 200 時，預設訪問響應內容都會丟擲異常：

```php
$response->getContent(); // 這裡會丟擲異常
```

如果你不希望預設丟擲異常，而希望自己處理，可以在配置檔案指定 `http.throw` 引數為 `false`：

```php
$config = [
  //...
  'http' => [
    'throw' => false,
    //...
  ],
];
```

這樣，你就可以在呼叫 API 時，自己處理異常：

```php
$options = [
    'query' => [
        'openid' => 'oDF3iY9ffA-hqb2vVvbr7qxf6A0Q',
    ]
];
$response = $api->get('/cgi-bin/user/get', $options);

if ($response->isFailed()) {
    // 出錯了，處理異常
}

return $response;
```

或者不改變預設配置的情況下，在呼叫請求時單獨設定`throw(false)`，若該請求失敗，也可以自己處理異常。

```php
// $options 同上文，這裡省略
$response = $api->get('/cgi-bin/user/get', $options)->throw(false);

if ($response->isFailed()) {
    // 出錯了，處理異常
}

return $response;
```

### 陣列式訪問

EasyWeChat 增強了 API 響應物件，比如增加了陣列式訪問，你可以不用每次 `toArray` 後再取值，更加便捷美觀：

```php
$response = $api->get('/foo/bar');

$response['foo']; // "bar"
isset($response['foo']); // true
```

### 獲取狀態碼

```php
$response->getStatusCode();
// 200
```

### 判斷業務是否成功/失敗 <version-tag>6.3.0+</version-tag>

比如狀態碼是 200，但是公眾號介面返回 40029 code 錯誤：

```php
$response->isSuccessful();  // false
$response->isFailed();      // true
```

### 獲取響應頭

```php
$response->getHeaders();
// ['content-type' => ['application/json;encoding=utf-8'], '...']

$response->getHeader('content-type');
// ['application/json;encoding=utf-8']

$response->getHeaderLine('content-type');
// 'application/json;encoding=utf-8'
```

### 獲取響應內容

```php
$response->getContent();
$response->getContent(false); // 失敗不丟擲異常
// {"foo":"bar"}

// 獲取 json 轉換後的陣列格式
$response->toArray();
$response->toArray(false); // 失敗不丟擲異常
// ["foo" => "bar"]

// 獲取 json
$response->toJson();
$response->toJson(false);
// {"foo":"bar"}

// 將內容轉換成流返回
$response->toStream();
$response->toStream(false); // 失敗不丟擲異常
```

### 轉換為 PSR-7 Response <version-tag>6.6.0+</version-tag>

如果你希望直接將 API 響應轉換成 [PSR-7 規範](https://www.php-fig.org/psr/psr-7/) Response，可以使用 `toPsrResponse` 方法：

```php
$psrResponse = $response->toPsrResponse();
```

比如在 Laravel 中就可以這樣使用：

```php
return $response->toPsrResponse();
```

### 儲存到檔案 <version-tag>6.3.0+</version-tag>

你可以方便的將內容直接儲存到檔案：

```php
$path = $response->saveAs('/path/to/file.jpg');
// /path/to/file.jpg
```

### 轉換為 Data URLs <version-tag>6.3.0+</version-tag>

你可以將內容轉換為[Data URLs](https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Basics_of_HTTP/Data_URIs)

```php
$dataUrl = $response->toDataUrl();
// data:image/png,%89PNG%0D%0A...
```

### 獲取其他上下文資訊

如："response_headers", "redirect_count", "start_time", "redirect_url" 等：

```php
$httpInfo = $response->getInfo();

// 獲取指定資訊
$startTime = $response->getInfo('start_time');

// 獲取請求日誌
$httpLogs = $response->getInfo('debug');
```

:book: 更多使用請參考： [HTTP client: Processing Responses](https://symfony.com/doc/current/http_client.html#processing-responses)

---

## 非同步請求

所有的請求都是非同步的，當你第一次訪問 `$response` 時才會真正的請求，比如：

```php
// 這段程式碼會立即執行，並不會發起網路請求
$response = $api->postJson('/cgi-bin/user/info/updateremark', [
    "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
    "remark" => "pangzi"
]);

// 當你嘗試訪問 $response 的資訊時，才會發起請求並等待返回
$contentType = $response->getHeaders()['content-type'][0];

// 嘗試獲取響應內容將阻塞執行，直到接收到完整的響應內容
$content = $response->getContent();
```

## 並行請求

由於請求天然是非同步的，那麼你可以很簡單實現並行請求：

```php
$responses = [
    $api->get('/cgi-bin/user/get'),
    $api->post('/cgi-bin/user/info/updateremark', ['body' => ...]),
    $api->post('/cgi-bin/user/message/custom/send', ['body' => ...]),
];

// 訪問任意一個 $response 時將執行併發請求：
foreach ($responses as $response) {
    $content = $response->getContent();
    // ...
}
```

當然你也可以給每個請求分配名字獨立訪問：

```php
$responses = [
    'users' => $api->get('/cgi-bin/user/get'),
    'remark' => $api->post('/cgi-bin/user/info/updateremark', ['body' => ...]),
    'message' => $api->post('/cgi-bin/user/message/custom/send', ['body' => ...]),
];

// 訪問任意一個 $response 時將執行併發請求：
$responses['users']->toArray();
```

## 失敗重試 <version-tag>6.1.0+</version-tag>

預設在公眾號、小程式開啟了重試機制，你可以透過全域性配置或者手動開啟重試特性。

> 🚨 不建議在支付模組使用重試功能，因為一旦重試導致支付資料異常，可能造成無法挽回的損失。

### 方式一：全域性配置

在支援重試的模組裡增加如下配置可以完成重試機制的全域性啟用：

```php
    'http' => [
        //...
        'retry' => true, // 使用預設配置
        // 'retry' => [
        //     // 僅以下狀態碼重試
        //     'http_codes' => [429, 500]
        //     'max_retries' => 3
        //     // 請求間隔 (毫秒)
        //     'delay' => 1000,
        //     // 如果設定，每次重試的等待時間都會增加這個係數
        //     // (例如. 首次:1000ms; 第二次: 3 * 1000ms; etc.)
        //     'multiplier' => 0.1
        // ],
    ],
```

### 方式二：手動開啟

如果你不想使用基於配置的全域性重試機制，你可以使用 `HttpClient::retry()` 方法來開啟失敗重試能力：

```php
$app->getClient()->retry()->get('/foo/bar');
```

當然，你可以在 `retry` 配置中自定義重試的配置，如下所示：

```php
$app->getClient()->retry([
    'max_retries' => 2,
    //...
])->get('/foo/bar');
```

### 自定義重試策略

如果覺得引數不能滿足需求，你還可以自己實現 [`Symfony\Component\HttpClient\RetryStrategyInterface`](https://github.com/symfony/symfony/blob/6.1/src/Symfony/Component/HttpClient/Retry/RetryStrategyInterface.php) 介面來自定義重試策略，然後呼叫 `retryUsing` 方法來使用它。

> 💡 建議繼承基類來拓展，以實現預設重試類的基礎功能。

```php
class MyRetryStrategy extends \Symfony\Component\HttpClient\Retry\GenericRetryStrategy
{
    public function shouldRetry(AsyncContext $context, ?string $responseContent, ?TransportExceptionInterface $exception): ?bool
    {
        // 你的自定義邏輯
        // if (...) {
        //     return false;
        // }

        return parent::shouldRetry($context, $responseContent, $exception);
    }
}
```

使用自定義重試策略：

```php
$app->getClient()->retryUsing(new MyRetryStrategy())->get('/foo/bar');
```

## 更多使用方法

:book: 更多使用請參考：[symfony/http-client](https://github.com/symfony/http-client)
