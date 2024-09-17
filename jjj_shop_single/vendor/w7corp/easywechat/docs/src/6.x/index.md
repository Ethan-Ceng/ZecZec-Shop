> 👋🏼 您當前瀏覽的文件為 6.x，其它版本的文件請參考：[5.x](/5.x/)、[4.x](/4.x/)、[3.x](/3.x/)

# EasyWeChat

EasyWeChat 是一個開源的 [微信](http://www.wechat.com) 非官方 SDK。安裝非常簡單，因為它是一個標準的 [Composer](https://getcomposer.org/) 包，這意味著任何滿足下列安裝條件的 PHP 專案支援 Composer 都可以使用它。

## 環境需求

- PHP >= 8.0
- [PHP cURL 擴充套件](http://php.net/manual/en/book.curl.php)
- [PHP OpenSSL 擴充套件](http://php.net/manual/en/book.openssl.php)
- [PHP SimpleXML 擴充套件](http://php.net/manual/en/book.simplexml.php)
- [PHP fileinfo 拓展](http://php.net/manual/en/book.fileinfo.php)

## 安裝

```shell
composer require w7corp/easywechat
```

## 使用

從 6.x 起，EasyWeChat 依然保持了它開箱即用的特性，同樣只需要傳入一個配置，初始化一個模組例項即可：

```php
use EasyWeChat\OfficialAccount\Application;

$config = [
    'app_id' => 'wx3cf0f39249eb0exx',
    'secret' => 'f1c242f4f28f735d4687abb469072axx',
    'token' => 'easywechat',
    'aes_key' => '' // 明文模式請勿填寫 EncodingAESKey
    //...
];

$app = new Application($config);
```

在建立例項後，所有的方法都幾乎可以有 IDE 自動補全，當然，建議先閱讀各模組的文件瞭解一下它們的區別，這裡我們以呼叫公眾號獲取使用者資料為例：

```php
$response = $app->getClient()->get("/cgi-bin/user/info?openid={$openid}&lang=zh_CN");

# 檢視返回結果
var_dump($response->toArray());
```

## 開始之前

在你動手寫程式碼之前，建議您首先閱讀以下內容：

- [關於 6.x](./introduction.md)
- [API 呼叫](./client.md)

## 參與貢獻

我們歡迎廣大開發者貢獻大家的智慧，讓我們共同讓它變得更完美。您可以在 GitHub 上提交 Pull Request，我們會盡快稽核並公佈。更多資訊請參考 [貢獻指南](contributing.md)。

## 開發者交流群

[EasyWeChat SDK 交流群](http://shang.qq.com/wpa/qunwpa?idkey=b4dcf3ec51a7e8c3c3a746cf450ce59895e5c4ec4fbcb0f80c2cd97c3c6e63e9) ID: 319502940
