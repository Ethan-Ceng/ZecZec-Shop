# 聊天敏感詞


### 新建敏感詞規則

```php
$params = [
    'rule_name' => 'rulename',
    'word_list' => [
        '敏感詞1', '敏感詞2'
    ],
    'semantics_list' => [1, 2, 3],
    'intercept_type' => 1,
    'applicable_range' => [
        'user_list' => ['zhangshan'],
        'department_list' => [2, 3]
    ]
];

$app->product->createInterceptRule($params);
```

### 獲取敏感詞規則詳情

```php
$ruleId = 'ruleId';

$app->product->getInterceptRuleDetails($ruleId);
```

### 刪除敏感詞規則

```php
$ruleId = 'ruleId';

$app->product->deleteInterceptRule($ruleId);
```


### 編輯敏感詞規則

```php
$params = [
    'rule_id' => 'xxxx',
    'rule_name' => 'rulename',
    'word_list' => [
        '敏感詞1', '敏感詞2'
    ],
    'semantics_list' => [1, 2, 3],
    'intercept_type' => 1,
    'applicable_range' => [
        'user_list' => ['zhangshan'],
        'department_list' => [2, 3]
    ]
];

$app->product->updateInterceptRule($params);
```
