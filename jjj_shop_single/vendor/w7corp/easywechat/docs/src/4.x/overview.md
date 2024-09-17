# EasyWeChat

EasyWeChat 是一個開源的 [微信](http://www.wechat.com) 非官方 SDK。

EasyWeChat 的安裝非常簡單，因為它是一個標準的 [Composer](https://getcomposer.org/) 包，這意味著任何滿足下列安裝條件的 PHP 專案支援 Composer 都可以使用它。

### 環境需求

> - PHP >= 7.0
> - [PHP cURL 擴充套件](http://php.net/manual/en/book.curl.php)
> - [PHP OpenSSL 擴充套件](http://php.net/manual/en/book.openssl.php)
> - [PHP SimpleXML 擴充套件](http://php.net/manual/en/book.simplexml.php)
> - [PHP fileinfo 拓展](http://php.net/manual/en/book.fileinfo.php)

### 加入我們

[EasyWeChat SDK 交流群](http://shang.qq.com/wpa/qunwpa?idkey=b4dcf3ec51a7e8c3c3a746cf450ce59895e5c4ec4fbcb0f80c2cd97c3c6e63e9) ID: 319502940

> 為了避免廣告及不看文件使用者，加群需要付費，所以請使用 能支援群費的客戶端。
> 另外：付費加群不代表我們有責任在群裡回答你的問題，所以請認真閱讀微信官方文件與 SDK 使用文件再使用，否則提的低階問題不會有人理你
> 不喜勿加，謝謝！
> 除非你發現了明確的 Bug，否則不要在群裡 @ 我 :pray:

你有以下兩種方式加入到我們中來，為廣大開發者提供更優質的免費開源的服務：

> - **貢獻程式碼**：我們的程式碼都在 [overtrue/wechat](https://github.com/overtrue/wechat) ，你可以提交 PR 到任何一個專案，當然，前提是程式碼質量必須是 OK 的。
> - **翻譯或補充文件**：我們的文件在：[w7corp/EasyWeChat/docs](https://github.com/w7corp/easywechat/tree/master/docs)，你可以選擇補充文件或者參與英文文件的翻譯，目前有 `zh-cn` 與 `en` 兩個分支，你可以提交對應的 PR 到目標分支參與翻譯工作。

### 開始之前

我們提供了影片教程：https://www.easywechat.com/tutorials 當然，我還是建議你具備以下基礎知識，否則可能沒有那麼快上手。

本 SDK 不是一個全新再造的東西，所以我不會從 0 開始教會你開發微信，你完全有必要在使用本 SDK 前做好以下工作：

> - 具備 PHP 基礎知識，不要連閉包是啥都不明白，可以參考我在知乎的回答: [想要開發自己的 PHP 框架需要那些知識儲備？](http://www.zhihu.com/question/26635323/answer/33812516)
> - 熟悉 PHP 常見的知識：自動載入、composer 的使用、JSON 處理、Curl 的使用等；
> - **仔細閱讀並看懂**[微信官方文件](http://mp.weixin.qq.com/wiki/13/80a1a25adbc46faf2716774c423b3151.html) [微信開放平臺文件](https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419318292&token=&lang=zh_CN)；
> - 明白微信介面的組成，自有伺服器、微信伺服器、公眾號（還有其它各種號）、測試號、以及通訊原理（互動過程）；
> - 瞭解基本的 HTTP 協議，Header 頭、請求方式（GET\POST\PUT\PATCH\DELETE）等；
> - 基本的 Debug 技能，檢視 php 日誌，nginx 日誌等。

如果你不具備這些知識，請不要使用，因為用起來會比較痛苦。

另外你有必要看一下以下的連結：

> - https://learnku.com/laravel/t/535/assertion-people-who-do-not-understand-the-wisdom-of-asking-questions-will-not-graduate-from-junior-programmers
> - http://laravel-china.github.io/php-the-right-way/

如果你在群裡問以下類似的問題，這真的是你沒有做好上面的工作：

> - "為啥我的不行啊，請問伺服器日誌怎麼看啊？"
> - "請問這是什麼原因啊？[結果/報錯截圖]"
> - "請問這個 SDK 怎麼用啊？"
> - "誰能告訴我這個 SDK 是怎麼安裝的啊？"
> - "怎麼接收使用者發的訊息啊？"
> - "為啥我的報這個錯啊：Class XXXX not found..."
> - ...

我們專門針對一些容易出現的通用問題已經做了彙總： [疑難解答](troubleshooting.md) ，如果你在問題疑難解答沒找到你出現的問題，那麼可以在這裡提問 [GitHub](https://github.com/overtrue/wechat/issues)，提問請描述清楚你用的版本，你的做法是什麼，不然別人沒法幫你。

> 要在 QQ 單獨找我提問\*\*，除非你是發現了明顯的 bug。有問題先審查程式碼，看文件, 再 google，然後 去群裡發個問題，帶上你的程式碼，重現流程，大家有空的會幫忙你解答。謝謝合作！:pray:

### 打賞支援

這是一個開源的專案，我們沒有收費服務，你如果覺得你從中獲益，簡化了你的開發工作，你可以 [打賞](https://github.com/sponsors/overtrue) 來支援我們。
