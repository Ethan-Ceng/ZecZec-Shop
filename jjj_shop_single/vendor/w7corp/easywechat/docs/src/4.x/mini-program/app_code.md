# 小程式碼

## 獲取小程式碼

### 介面A: 適用於需要的碼數量較少的業務場景

API:

```
$app->app_code->get(string $path, array $optional = []);
```

其中 `$optional` 為以下可選引數：

>  - **width** Int - 預設 430 二維碼的寬度
>  - **auto_color**  預設 false  自動配置線條顏色，如果顏色依然是黑色，則說明不建議配置主色調
>  - **line_color**  陣列，`auto_color` 為 `false` 時生效，使用 rgb 設定顏色 例如 ，示例：`["r" => 0,"g" => 0,"b" => 0]`。

示例程式碼：

```php
$response = $app->app_code->get('path/to/page');
// 或者
$response = $app->app_code->get('path/to/page', [
    'width' => 600,
    //...
]);

// 或者指定顏色
$response = $app->app_code->get('path/to/page', [
    'width' => 600,
    'line_color' => [
        'r' => 105,
        'g' => 166,
        'b' => 134,
    ],
]);

// $response 成功時為 EasyWeChat\Kernel\Http\StreamResponse 例項，失敗時為陣列或者你指定的 API 返回格式

// 儲存小程式碼到檔案
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->save('/path/to/directory');
}

// 或
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->saveAs('/path/to/directory', 'appcode.png');
}
```

### 介面B：適用於需要的碼數量極多，或僅臨時使用的業務場景

API:

```
$app->app_code->getUnlimit(string $scene, array $optional = []);
```

> 其中 $scene 必填，$optinal 與 get 方法一致，多一個 page 引數。

示例程式碼：

```php
$response = $app->app_code->getUnlimit('scene-value', [
    'page'  => 'path/to/page',
    'width' => 600,
]);
// $response 成功時為 EasyWeChat\Kernel\Http\StreamResponse 例項，失敗為陣列或你指定的 API 返回型別

// 儲存小程式碼到檔案
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->save('/path/to/directory');
}
// 或
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->saveAs('/path/to/directory', 'appcode.png');
}
```

## 獲取小程式二維碼

API:

```
$app->app_code->getQrCode(string $path, int $width = null);
```

> 其中 $path 必填，其餘引數可留空。

示例程式碼：

```php
$response = $app->app_code->getQrCode('/path/to/page');

// $response 成功時為 EasyWeChat\Kernel\Http\StreamResponse 例項，失敗為陣列或你指定的 API 返回型別

// 儲存小程式碼到檔案
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->save('/path/to/directory');
}

// 或
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->saveAs('/path/to/directory', 'appcode.png');
}
```

##
