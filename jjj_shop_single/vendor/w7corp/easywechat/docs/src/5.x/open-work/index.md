# 企業微信第三方服務商

## 例項化

```php
<?php
use EasyWeChat\Factory;

$config = [
     'corp_id'              => '服務商的corpid',
     'secret'               => '服務商的secret，在服務商管理後臺可見',
     'suite_id'             => '以ww或wx開頭應用id',
     'suite_secret'         => '應用secret',
     'token'                => '應用的Token',
     'aes_key'              => '應用的EncodingAESKey',
     'reg_template_id'      => '註冊定製化模板ID',
     'redirect_uri_install' => '安裝應用的回撥url（可選）', 
     'redirect_uri_single'  => '單點登入回撥url （可選）', 
     'redirect_uri_oauth'   => '網頁授權第三方回撥url （可選）', 
     
];

$app = Factory::openWork($config);
```

