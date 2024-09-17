# 內容安全介面

## 文字安全內容檢測

用於校驗一段文字是否含有違法內容。

### 頻率限制

單個appid呼叫上限為2000次/分鐘，1,000,000次/天

### 呼叫示例

```php
// 傳入要檢測的文字內容，長度不超過500K位元組
$content = '你好';

$result = $app->content_security->checkText($content);

// 正常返回 0
{
    "errcode": "0",
    "errmsg": "ok"
}

//當 $content 內含有敏感資訊，則返回 87014
{
    "errcode": 87014,
    "errmsg": "risky content"
}
```

## 圖片安全內容檢測

用於校驗一張圖片是否含有敏感資訊。如涉黃、涉及敏感人臉（通常是政治人物）。

### 頻率限制

單個appid呼叫上限為1000次/分鐘，100,000次/天

### 呼叫示例

```php
// 所傳引數為要檢測的圖片檔案的絕對路徑，圖片格式支援PNG、JPEG、JPG、GIF, 畫素不超過 750 x 1334，同時檔案大小以不超過 300K 為宜，否則可能報錯
$result = $app->content_security->checkImage('/path/to/the/image');

// 正常返回 0
{
    "errcode": "0",
    "errmsg": "ok"
}

// 當圖片檔案內含有敏感內容，則返回 87014
{
    "errcode": 87014,
    "errmsg": "risky content"
}
```

## 重要說明

目前上述兩個介面僅支援在小程式中使用，示例中的 `$app` 表示小程式例項，即:

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'wx3cf0f39249eb0exx',
    'secret' => 'f1c242f4f28f735d4687abb469072axx',

    // 下面為可選項
    // 指定 API 呼叫返回結果的型別：array(default)/collection/object/raw/自定義類名
    'response_type' => 'array',

    'log' => [
        'level' => 'debug',
        'file' => __DIR__.'/wechat.log',
    ],
];

$app = Factory::miniProgram($config);
```
