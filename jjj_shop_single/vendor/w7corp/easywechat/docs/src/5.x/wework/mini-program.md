# 小程式

## 登入獲取使用者資訊

> 注意：需要關聯小程式，並且使用關聯後的小程式AgentId與Secret。

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx', //企業id

    'agent_id' => 100020, // 企業微信關聯後的AgentId
    'secret'   => 'xxxxxxxxxx', //企業微信關聯後的Secret
];

$app = Factory::work($config);

$miniProgram = $app->miniProgram();

$res = $miniProgram->auth->session("js-code");
```

