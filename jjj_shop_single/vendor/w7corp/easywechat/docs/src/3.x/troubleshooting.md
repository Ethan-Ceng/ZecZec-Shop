# 疑難解答


在微信公眾平臺開發的道路上，遍佈著各種大大小小的坑，有的人掉坑裡，幾經折騰又爬出來了，然後拍拍屁股走人。然而坑還在那裡，還會繼續有後來人掉進去……

這，是我們不願看到的。

所以在這裡，我們將陸續將微信開發中可能遇到的各種疑難問題進行彙總，並給出對應的解決辦法。一般情況下，這些問題都可以對號入座，輕鬆地解決。但也不排除特殊情況，這時候你遇到的問題與文中某一個症狀一致，但文中所給的解決方案並不湊效，這種情況下就需要發揮你自己的智慧，去……折騰了……

我們期待這一版塊為各位的開發帶來便利，同時也希望各位本著開源、分享的精神對其進行補充和完善，將各種坑一一填小、填平，讓微信開發變得不那麼痛苦，甚至，變成一件快樂的事……

# 一些伺服器基本設施問題：

- 時區不對， 使用命令 `date` 可以在伺服器上檢視當前時間，如果發現時區不對則需要修改時區：[Setting The Correct Timezone In CentOS And Ubuntu Servers With NTP](https://www.liberiangeek.net/2013/02/setting-the-correct-timezone-in-centos-and-ubuntu-servers-with-ntp/)
    - ...


## curl: (60) SSL certificate problem: unable to get local issuer certificate

這是 SSL 證書問題所致，在使用 SDK 呼叫微信支付等相關的操作時可能會遇到報 “SSL certificate problem: unable to get local issuer certificate” 的錯誤。

微信公眾平臺提供的文件中建議對部分較敏感的操作介面使用 https 協議進行訪問，例如微信支付和紅包等介面中涉及到操作商戶資金的一些操作。
wechat SDK 遵循了官方建議，所以在呼叫這些介面時，除了按照官方文件設定操作證書檔案外，還需要保證伺服器正確安裝了 CA 證書。

1. 下載 CA 證書

  你可以從 http://curl.haxx.se/ca/cacert.pem 下載 或者 使用[微信官方提供的證書](https://pay.weixin.qq.com/wiki/doc/api/app.php?chapter=4_3)中的 CA 證書 `rootca.pem` 也是同樣的效果。

2. 在 `php.ini` 中配置 CA 證書

  只需要將上面下載好的 CA 證書放置到您的伺服器上某個位置，然後修改 `php.ini` 的 `curl.cainfo` 為該路徑（**絕對路徑！**），重啟 `php-fpm` 服務即可。

  ```
  curl.cainfo = /path/to/downloaded/cacert.pem
  ```
  > 注意證書檔案**路徑為絕對路徑**！以自己實際情況為準。

  其它修改 HTTP 類原始檔的方式是不允許的。

## cURL error 56: SSLRead() return error -9806

目前在 OSX 下，發現使用 HomeBrew 裝的 PHP 7.0 有這個問題，解決方案是重新 brew 安裝 PHP：

```shell
$ brew install homebrew/php/php70 --with-homebrew-openssl --with-homebrew-curl --without-snmp -vvv
```

驗證：

```shell
$ php -i | grep 'OpenSSL support'

OpenSSL support => enabled
OpenSSL support => enabled
```


## 支付失敗！當前頁面的 URL 未註冊

這是由於微信支付授權目錄未正確配置引起的。此時開發者應該登入微信公眾平臺，進入**【微信支付】->【開發設定】**進行設定。

1. 公眾號可新增3個支付授權目錄，滿足不同應用使用同一個公眾號進行支付的業務需求。

2. 正確的**【支付授權目錄】**應以 `http://` 或 `https://` 開頭，並以正斜槓 `/` 結尾，授權目錄所包含的域名**必須經過 ICP 備案**。

3. 支付授權目錄需**細化至二級或三級目錄**。

4. 所有**實際調起微信支付請求的頁面都必須要所配置的支付授權目錄之下**。

5. 在開發過程中，也可以使用測試授權目錄進行開發測試，此時還**應該將參與測試的個人微訊號新增到測試白名單中**，否則將出現對應的錯誤提示……

> 配置前請先理解**頁面**、**目錄**、**URL **以及**域名**等幾個基本概念，並對自己所使用的框架的路由機制有一個大致瞭解。這樣你才會知道自己正在配置的引數是個啥玩意兒，有什麼卵用…… :smile:


## redirect_url 引數錯誤

這是由於程式使用了**網頁授權**而公眾號沒有正確配置**【網頁授權域名】**所致。此時你需要登入[微信公眾平臺](https://mp.weixin.qq.com/)，在【開發】->【介面許可權】頁面找到**網頁授權獲取使用者基本資訊**進行配置並儲存。

1. 網頁授權域名應該為透過 ICP 備案的有效域名，否則儲存時無法透過安全監測。

2. 網頁授權域名即程式完成授權獲得授權  code 後跳轉到的頁面的域名，一般情況下為你的業務域名。

3. 網頁授權域名配置成功後會立即生效。

4. 公眾號的網頁授權域名只可配置一個，請合理規劃你的業務，否則你會發現……授權域名不夠用哈。


## [JSAPI] config: invalid url domain
在使用 JS-SDK 進行開發時，每個頁面都需要呼叫 wx.config() 方法配置 JSPAI 引數。如果沒有正確配置 **JSAPI 安全域名**並且開啟了除錯模式，此時就報此錯誤。遇到這個問題時，開發者需要登入微信公眾平臺，進入【公眾號設定】->【功能設定】頁面，將專案所使用的域名新增至 **【JSAPI 安全域名】**列表中。

1. 一個公眾號同時最多可繫結**三個**安全域名，並且這些域名必須為透過 **ICP 備案**的**一級或一級以上**的有效域名。

2. JSAPI 安全域名每個月**限修改三次**，修改任何一個都算，所以，請謹慎操作。

3. 如果需要使用 JSAPI 調起支付功能，則支付目錄必須也在所配置的**安全域名之下**，並且需要將支付目錄新增至**支付授權目錄**。

## token驗證失敗、向公眾號傳送訊息無任何反應

相信對接公眾號一般是微信開發者進行開發過程中最先進行的工作，而在這看似簡單的配置操作中，也可能會掉坑裡。
最常見的兩種情況就如下：

1. 確認你 “**啟用**” 了開發模式， token 驗證透過不代表啟用，儲存後也不代表啟用。看到紅色 “**停用**” 才真正的是啟用了。

2. 配置好URL(伺服器地址)以及Token(令牌)後，點選儲存時提示**token驗證失敗**，出現這種情況的原因有多種，其中之一便是網路不穩定，所以**可嘗試多次儲存**，若始終無法透過再排查其它可能因素。

3. 配置儲存成功之後，向公眾號傳送訊息無任何反應，自己的訊息處理程式也沒有被呼叫的記錄（無對應日誌）。這種情況下如果你嘗試**反覆停用和啟用伺服器配置**，可能突然間驚奇地了現，問題莫名其妙的解決了。

4. 使用線上除錯工具的訊息介面，http://mp.weixin.qq.com/debug/， 只要返回綠色的“**請求成功**”，就代表你的程式碼沒有問題，請**重複上面第3項**再測試。

5. **如果你在用什麼本地開發工具，或者什麼 ngrok 代理到本機這樣的開發方式，那麼失敗就很正常了，微信伺服器到你機器的網路延遲太大（還是用伺服器開發吧）。**

> 請開發者理解伺服器 TOKEN 驗證原理（官方文件有說明）並謹記伺服器驗證時使用 GET 方式訪問，而公眾平臺向你的伺服器傳送訊息/資料則使用 POST 方式，所以伺服器驗證成功之後，在某些啟用了 CSRF 驗證的框架裡，接收訊息時可能還會遇到 CSRF 相關的問題，請根據自己專案實際情況進行排查。
> 另外有的朋友的 Laravel 裡使用了 laravel-debugbar，這個元件的原理是在頁面輸出時在後面新增 HTML 來實現的，所以它會改變我們返回給微信的內容，此時要麼解除安裝，要麼停用掉它。


## Maximum function nesting level of '100' reached, aborting!

在使用了 Xdebug 的環境下可能出現這個問題。這是由於 Xdebug 限制函式巢狀的最大層級數（預設為100），當巢狀次數達到該值便會觸發 Xdebug 跳出巢狀並報此錯誤。

為避免這個問題，**可以將 Xdebug 的 max_nesting_level 引數適當設定大一些**，通常設定為200就可以了（當然可根據自己實際情況設定為更大的值）。

如下，修改 php.ini 配置檔案後，重啟 Apache 或 php-fpm 服務即可。

```
xdebug.max_nesting_level=200
```
