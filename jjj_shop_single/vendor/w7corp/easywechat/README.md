# [EasyWeChat](https://www.easywechat.com)

📦 一個 PHP 微信開發 SDK，開源 SaaS 平臺提供商 [微擎](https://www.w7.cc/) 旗下開源產品。

[![Test Status](https://github.com/w7corp/easywechat/workflows/Test/badge.svg)](https://github.com/w7corp/easywechat/actions)
[![Lint Status](https://github.com/w7corp/easywechat/workflows/Lint/badge.svg)](https://github.com/w7corp/easywechat/actions)
[![Latest Stable Version](https://poser.pugx.org/w7corp/easywechat/v/stable.svg)](https://packagist.org/packages/w7corp/easywechat)
[![Latest Unstable Version](https://poser.pugx.org/w7corp/easywechat/v/unstable.svg)](https://packagist.org/packages/w7corp/easywechat)
[![Total Downloads](https://poser.pugx.org/w7corp/easywechat/downloads)](https://packagist.org/packages/w7corp/easywechat)
[![License](https://poser.pugx.org/w7corp/easywechat/license)](https://packagist.org/packages/w7corp/easywechat)

## 環境需求

- PHP >= 8.0.2
- [Composer](https://getcomposer.org/) >= 2.0

## 安裝

```bash
composer require w7corp/easywechat
```

## 使用示例

基本使用（以公眾號服務端為例）:

```php
<?php

use EasyWeChat\OfficialAccount\Application;

$config = [
    'app_id' => 'wx3cf0f39249eb0exxx',
    'secret' => 'f1c242f4f28f735d4687abb469072xxx',
    'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
    'token' => 'easywechat',
];

$app = new Application($config);

$app->getServer()->with(fn() => "您好！EasyWeChat！");

$response = $server->serve();
```

## 文件和連結

[官網](https://www.easywechat.com) · [討論](https://github.com/w7corp/easywechat/discussions) · [更新策略](https://github.com/w7corp/easywechat/security/policy)

## :heart: 支援我

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me.svg?raw=true)](https://github.com/sponsors/overtrue)

如果你喜歡我的專案並想支援它，[點選這裡 :heart:](https://github.com/sponsors/overtrue)

## 由 JetBrains 贊助

非常感謝 Jetbrains 為我提供的 IDE 開源許可，讓我完成此專案和其他開源專案上的開發工作。

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/overtrue)

## 可愛的貢獻者們

<a href="https://github.com/w7corp/easywechat/graphs/contributors"><img src="https://opencollective.com/wechat/contributors.svg?width=890" /></a>

## License

MIT
