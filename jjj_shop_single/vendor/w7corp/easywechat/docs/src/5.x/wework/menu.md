# 自定義選單

自定義選單是指為單個應用設定自定義選單功能，所以在使用時請注意呼叫正確的應用例項。

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx', // 應用的 secret
    //...
];
$app = Factory::work($config);
```

## 建立選單

```php
$menus = [
    'button' => [
        [
            'name' => '首頁',
            'type' => 'view',
            'url' => 'https://easywechat.com'
        ],
        [
            'name' => '關於我們',
            'type' => 'view',
            'url' => 'https://easywechat.com/about'
        ],
        //...
    ],
];

$app->menu->create($menus);
```

## 獲取選單

```php
$app->menu->get();
```

## 刪除選單

```php
$app->menu->delete();
```
