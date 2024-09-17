[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Falipay%2Falipay-easysdk.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Falipay%2Falipay-easysdk?ref=badge_shield)
[![Maven Central](https://img.shields.io/maven-central/v/com.alipay.sdk/alipay-easysdk.svg)](https://mvnrepository.com/artifact/com.alipay.sdk/alipay-easysdk)
[![NuGet](https://badge.fury.io/nu/AlipayEasySDK.svg)](https://badge.fury.io/nu/AlipayEasySDK)
[![Packagist](https://poser.pugx.org/alipaysdk/easysdk/v/stable)](https://packagist.org/packages/alipaysdk/easysdk)

歡迎使用 Alipay **Easy** SDK。

打造**最好用**的支付寶開放平臺**服務端SDK**，Alipay Easy SDK讓您享受**極簡程式設計**體驗，快速訪問支付寶開放平臺開放的各項**核心能力**。

## 設計理念
不同於原有的Alipay SDK通用而全面的設計理念，Alipay Easy SDK對開放能力的API進行了更加貼近高頻場景的精心設計與裁剪，簡化了服務端呼叫方式，讓呼叫API像使用語言內建的函式一樣簡便。

同時，您也不必擔心面向高頻場景提煉的API可能無法完全契合自己的個性化場景，Alipay Easy SDK支援靈活的[動態擴充套件](#extension)方式，同樣可以滿足低頻引數、低頻API的使用需求。

Alipay Easy SDK提供了與[能力地圖](https://opendocs.alipay.com/mini/00am3f)相對應的程式碼組織結構，讓開發者可以快速找到不同能力對應的API。

Alipay Easy SDK主要目標是提升開發者在**服務端**整合支付寶開放平臺開放的各類核心能力的效率。

### 化繁為簡

| Alipay Easy SDK  | Alipay SDK                                                     |
|------------------|----------------------------------------------------------------|
| 極簡程式碼風格，更貼近自然語言閱讀習慣  | 傳統程式碼風格，需要多行程式碼完成一個介面的呼叫 |
| Factory單例全域性任何地方都可直接引用 | AlipayClient例項需自行建立並在上下文中傳遞 |
| API中只保留高頻場景下的必備引數，同時提供低頻可選引數的裝配能力    | 沒有區分高低頻引數，單API最多可達數十個入參，對普通開發者的干擾較大 |


* Alipay Easy SDK :smiley:

```java
Factory.Payment.Common().create("Iphone6 16G", "202003019443", "0.10", "2088002656718920");
```

* Alipay SDK :confused:

```java
AlipayTradeCreateRequest request = new AlipayTradeCreateRequest();

AlipayTradeCreateModel model = new AlipayTradeCreateModel();
model.setSubject("Iphone6 16G");
model.setOutTradeNo("202003019443");
model.setTotalAmount("0.10");
model.setBuyerId("2088002656718920");
...

request.setBizModel(model);
...

alipayClient.execute(request);
```

### 如何切換
* 無論是Alipay Easy SDK還是Alipay SDK，本質都是傳送HTTP請求訪問Open API閘道器，所以只需將原來透過Alipay SDK呼叫Open API的程式碼，替換為Alipay Easy SDK中對應API的呼叫即可。Alipay Easy SDK和Alipay SDK並無衝突，可以共存。

* 如果您所需對接的開放平臺能力，Alipay Easy SDK尚未提煉出API支援（[已支援的API列表](#apiList)），您可以透過[通用介面](./APIDoc.md#generic)完成呼叫。

* 我們會持續挖掘高頻場景，不斷豐富Alipay Easy SDK支援的API，讓您在絕大多數常見場景下，都能享受Alipay Easy SDK帶來的便捷。

## 技術特點
### 純語言開發
所有Alipay Easy SDK的具體程式語言的實現，均只採用純程式語言進行開發，不引入任何重量級框架，減少潛在的框架衝突，讓SDK可以自由整合進任何程式碼環境中。

### 結構清晰
我們按照能力類別和場景類別對API進行了歸類，結構更加清晰，一目瞭然。
> 更多資訊請參見[API組織規範](#spec)。

### 引數精簡
Alipay Easy SDK對每個API都精心打磨，剔除了`Open API`中不常用的可選引數，減少普通使用者的無效選擇，提升開發效率。

<a name="extension"/>

### 靈活擴充套件
開發者可以透過Fluent風格的API鏈式呼叫，在為高頻場景打造的API基礎上，不斷擴充套件自己的個性化場景需求。

```java
// 透過呼叫agent方法，擴充套件支援ISV代呼叫場景
Factory.Payment.FaceToFace().agent("ca34ea491e7146cc87d25fca24c4cD11").preCreate(...)

// 透過呼叫optional方法，擴充套件支援個性化可選引數
Factory.Payment.FaceToFace().optional("extend_params", extendParams).preCreate(...)

// 多種擴充套件可靈活搭配，不同擴充套件方法功能詳細說明請前往各語言主頁中的“快速開始-擴充套件呼叫”欄目中檢視
Factory.Payment.FaceToFace()
	.agent(...)
	.optionalArgs(...)
	.auth(...)
	.asyncNotify(...)
	.preCreate(...)
```

### 測試/示例完備
每個API都有對應的單元測試進行覆蓋，良好的單元測試天生就是最好的示例。

同時您也可以前往[API Doc](./APIDoc.md)檢視每個API的詳細使用說明。

> 注：單元測試中使用到的私鑰均進行了脫敏處理，會導致單元測試無法直接執行。您可以自行更改單元測試專案中的`TestAccout類`和`privateKey.json`檔案中的相關賬號與私鑰配置後再執行單元測試。

### 多語言
Alipay Easy SDK基於阿里集團研發的[`Darabonba`](https://github.com/aliyun/darabonba)進行架構，透過DSL中間語言定義API模型，再基於DSL語言自動生成不同程式語言（Java、C#、PHP、TS等）實現的SDK，極大地提升了SDK能力的擴充套件效率和適用範圍，同時也保證了相同的`Easy API`在不同語言生態中體驗的一致性。

API模型的DSL描述可以進入[tea](./tea)目錄檢視。

### 快速整合
各語言SDK均會在各自的中央倉庫（Maven、NuGet、Composer、NPM etc.）中同步釋出，讓您使用各語言主流依賴管理工具即可一鍵安裝整合SDK。

## 語言支援情況
Alipay Easy SDK首發暫只支援`Java`、`C#`、`PHP`程式語言，更多程式語言支援正在積極新增中，敬請期待。

各語言具體的**使用說明**和**詳細介紹**請點選如下連結進入各語言主目錄檢視。

[Java](./java)

[C#](./csharp)

[PHP](./php)

<a name="spec"/>

## API組織規範

在Alipay Easy SDK中，API的引用路徑與能力地圖的組織層次一致，遵循如下規範

> Factory.能力類別.場景類別.介面方法名稱( ... )

比如，如果您想要使用[能力地圖](https://opendocs.alipay.com/mini/00am3f)中`營銷能力`下的`模板訊息`場景中的`小程式傳送模板訊息`，只需按如下形式編寫呼叫程式碼即可（不同程式語言的連線符號可能不同）。

`Factory.Marketing.TemplateMessage().send( ... )`

其中，介面方法名稱通常是對其依賴的OpenAPI功能的一個最簡概況，介面方法的出入參與OpenAPI中同名引數含義一致，可參照OpenAPI相關引數的使用說明。

Alipay Easy SDK將致力於保持良好的API命名，以符合開發者的程式設計直覺。

<a name="apiList"/>

## 已支援的API列表

| 能力類別      | 場景類別            | 介面方法名稱                 | 呼叫的OpenAPI名稱                                              |
|-----------|-----------------|------------------------|-----------------------------------------------------------|
| Base<br/>基礎能力      | OAuth<br/>使用者授權           | getToken<br/>獲取授權訪問令牌和使用者user_id               | alipay\.system\.oauth\.token                              |
| Base<br/>基礎能力     | OAuth<br/>使用者授權           | refreshToken<br/>重新整理授權訪問令牌           | alipay\.system\.oauth\.token                              |
| Base<br/>基礎能力      | Qrcode<br/>小程式二維碼          | create<br/>建立小程式二維碼                 | alipay\.open\.app\.qrcode\.create                         |
| Base<br/>基礎能力      | Image<br/>圖片           | upload<br/>上傳門店照片                 | alipay\.offline\.material\.image\.upload                  |
| Base<br/>基礎能力      | Video<br/>影片           | upload<br/>上傳門店影片                 | alipay\.offline\.material\.image\.upload                  |
| Member<br/>會員能力    | Identification<br/>支付寶身份認證  | init<br/>身份認證初始化                   | alipay\.user\.certify\.open\.initialize                   |
| Member<br/>會員能力    | Identification<br/>支付寶身份認證  | certify<br/>生成認證連結                | alipay\.user\.certify\.open\.certify                      |
| Member<br/>會員能力    | Identification<br/>支付寶身份認證  | query<br/>身份認證記錄查詢                  | alipay\.user\.certify\.open\.query                        |
| Payment<br/>支付能力   | Common<br/>通用          | create<br/>建立交易                 | alipay\.trade\.create                                     |
| Payment<br/>支付能力   | Common<br/>通用          | query<br/>查詢交易                  | alipay\.trade\.query                                      |
| Payment<br/>支付能力   | Common<br/>通用          | refund<br/>交易退款                 | alipay\.trade\.refund                                     |
| Payment<br/>支付能力   | Common<br/>通用          | close<br/>關閉交易                  | alipay\.trade\.close                                      |
| Payment<br/>支付能力   | Common<br/>通用          | cancel<br/>撤銷交易                 | alipay\.trade\.cancel                                     |
| Payment<br/>支付能力   | Common<br/>通用          | queryRefund<br/>交易退款查詢            | alipay\.trade\.fastpay\.refund\.query                     |
| Payment<br/>支付能力   | Common<br/>通用          | downloadBill<br/>查詢對賬單下載地址           | alipay\.data\.dataservice\.bill\.downloadurl\.query       |
| Payment<br/>支付能力   | Common<br/>通用          | verifyNotify<br/>非同步通知驗籤           | -                                                         |
| Payment<br/>支付能力   | Huabei<br/>花唄分期          | create<br/>建立花唄分期交易                 | alipay\.trade\.create                                     |
| Payment<br/>支付能力   | FaceToFace<br/>當面付      | pay<br/>掃使用者出示的付款碼，完成付款                    | alipay\.trade\.pay                                        |
| Payment<br/>支付能力   | FaceToFace<br/>當面付      | precreate<br/>生成交易付款碼，待使用者掃碼付款              | alipay\.trade\.precreate                                  |
| Payment<br/>支付能力   | App<br/>手機APP             | pay<br/>生成訂單串，再使用客戶端 SDK 憑此串喚起支付寶收銀臺                    | alipay\.trade\.app\.pay                                   |
| Payment<br/>支付能力   | Page<br/>電腦網站            | pay<br/>生成交易表單，渲染後自動跳轉支付寶網站引導使用者完成支付                    | alipay\.trade\.page\.pay                                  |
| Payment<br/>支付能力   | Wap<br/>手機網站             | pay<br/>生成交易表單，渲染後自動跳轉支付寶網站引導使用者完成支付                    | alipay\.trade\.wap\.pay                                   |
| Security<br/>安全能力  | TextRisk<br/>文字內容安全        | detect<br/>檢測內容風險                 | alipay\.security\.risk\.content\.detect                   |
| Marketing<br/>營銷能力 | Pass<br/>支付寶卡包            | createTemplate<br/>卡券模板建立         | alipay\.pass\.template\.add                               |
| Marketing<br/>營銷能力 | Pass<br/>支付寶卡包            | updateTemplate<br/>卡券模板更新         | alipay\.pass\.template\.update                            |
| Marketing<br/>營銷能力 | Pass<br/>支付寶卡包            | addInstance<br/>卡券例項發放            | alipay\.pass\.instance\.add                               |
| Marketing<br/>營銷能力 | Pass<br/>支付寶卡包            | updateInstance<br/>卡券例項更新         | alipay\.pass\.instance\.update                            |
| Marketing<br/>營銷能力 | TemplateMessage<br/>小程式模板訊息 | send <br/>傳送模板訊息| alipay\.open\.app\.mini\.templatemessage\.send            |
| Marketing<br/>營銷能力 | OpenLife<br/>生活號        | createImageTextContent<br/>建立圖文訊息內容 | alipay\.open\.public\.message\.content\.create            |
| Marketing<br/>營銷能力 | OpenLife<br/>生活號        | modifyImageTextContent<br/>更新圖文訊息內容 | alipay\.open\.public\.message\.content\.modify            |
| Marketing<br/>營銷能力 | OpenLife<br/>生活號        | sendText<br/>群發本文訊息               | alipay\.open\.public\.message\.total\.send                |
| Marketing<br/>營銷能力 | OpenLife<br/>生活號        | sendImageText<br/>群發圖文訊息          | alipay\.open\.public\.message\.total\.send                |
| Marketing<br/>營銷能力 | OpenLife<br/>生活號        | sendSingleMessage<br/>單發模板訊息      | alipay\.open\.public\.message\.single\.send               |
| Marketing<br/>營銷能力 | OpenLife<br/>生活號        | recallMessage<br/>生活號訊息撤回          | alipay\.open\.public\.life\.msg\.recall                   |
| Marketing<br/>營銷能力 | OpenLife<br/>生活號        | setIndustry<br/>模板訊息行業設定            | alipay\.open\.public\.template\.message\.industry\.modify |
| Marketing<br/>營銷能力 | OpenLife<br/>生活號        | getIndustry<br/>生活號查詢行業設定            | alipay\.open\.public\.setting\.category\.query            |
| Util<br/>輔助工具      | AES<br/>加解密             | decrypt<br/>解密，常用於會員手機號解密                | -                                                         |
| Util<br/>輔助工具      | AES<br/>加解密             | encrypt<br/>加密                | -                                                         |
| Util<br/>輔助工具      | Generic<br/>通用介面         | execute<br/>自行拼接引數，執行OpenAPI呼叫                | -                                                         |

> 注：更多高頻場景的API持續更新中，敬請期待。

您還可以前往[API Doc](./APIDoc.md)檢視每個API的詳細使用說明。

# 變更日誌
每個版本的詳細更改記錄在[變更日誌](./CHANGELOG)中。

> 版本號最末一位修訂號的增加（比如從`1.0.0`升級為`1.0.1`），意味著SDK的功能沒有發生任何變化，僅僅是修復了部分Bug。該類升級可能不會記錄在變更日誌中。

> 版本號中間一位次版本號的增加（比如從`1.0.0`升級為`1.1.0`），意味著SDK的功能發生了可向下相容的新增或修改。

> 版本號首位主版本號的增加（比如從`1.0.0`升級為`2.0.0`），意味著SDK的功能可能發生了不向下相容的較大調整，升級主版本號後請注意做好相關的迴歸測試工作。

# 相關
* [支付寶開放平臺](https://open.alipay.com/platform/home.htm)
* [支付寶開放平臺文件中心](https://docs.open.alipay.com/catalog)
* [最新原始碼](https://github.com/alipay/alipay-easysdk)

# 許可證
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Falipay%2Falipay-easysdk.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Falipay%2Falipay-easysdk?ref=badge_large)

# 交流與技術支援
不管您在使用Alipay Easy SDK的過程中遇到任何問題，歡迎前往 [支付寶開放社群](https://forum.alipay.com/mini-app/channel/1100001)  發帖與支付寶工作人員和其他開發者一起交流。

注：為了提高開發者問題的響應時效，github本身的issue功能已關閉，支付寶開放社群中發帖的問題，通常會在2小時內響應。
