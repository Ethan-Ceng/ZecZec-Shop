# 應用管理

>  企業微信在17年11月對 API 進行了大量的改動，應用管理部分已經沒啥用了

應用管理是企業微信中比較特別的地方，因為它的使用是不基於應用的，或者說基於任何一個應用都能訪問這些 API，所以在用法上是直接呼叫 work 例項的 `agent` 屬性。

```php
$config = [
    ...
];

$app = Factory::work($config);
```

## 應用列表

```php
$agents = $app->agent->list(); // 測試拿不到內容
```

## 應用詳情

```php
$agents = $app->agent->get($agentId); // 只能傳配置檔案中的 id，API 改動所致
```

## 設定應用

```php
$agents = $app->agent->set($agentId, ['foo' => 'bar']);
```

## 設定工作臺自定義展示

### 模版型別資料結構

可以透過介面配置展示型別。具體可設定:

- 關鍵資料型
- 圖片型
- 列表型
- webview型

> 官方文件
> https://open.work.weixin.qq.com/api/doc/90000/90135/92535

### 設定應用在工作臺展示的模版

```php
$params = [
    'agentid' => 1000005,
    'type' => 'image', //展示型別
    'image' => [
        'url' => 'xxxx',
        'jump_url' => 'http://www.qq.com',
        'pagepath' => 'pages/index'
    ],
    'replace_user_data' => true
];

$agents->agent_workbench->setWorkbenchTemplate(array $params);
```

### 獲取應用在工作臺展示的模版

```php
$agentId = 100005;

$agents->agent_workbench->getWorkbenchTemplate(int $agentId);
```


### 設定應用在使用者工作臺展示的資料

```php
$params = [
    'agentid' => 1000005,
    'userid' => 'test', //員工id
    'type' => 'keydata', //展示型別
    'keydata' => [
        'items' => [
            [
                'key' => '待審批',
                'data' => '2',
                'jump_url' => 'http://www.qq.com',
                'pagepath' => 'pages/index'
            ],
            [
                'key' => '帶批閱作業',
                'data' => '4',
                'jump_url' => 'http://www.qq.com',
                'pagepath' => 'pages/index'
            ],
            [
                'key' => '成績錄入',
                'data' => '45',
                'jump_url' => 'http://www.qq.com',
                'pagepath' => 'pages/index'
            ],
            [
                'key' => '綜合評價',
                'data' => '98',
                'jump_url' => 'http://www.qq.com',
                'pagepath' => 'pages/index'
            ]
        ]
    ]
];

$agents->agent_workbench->setWorkbenchData(array $params);
```