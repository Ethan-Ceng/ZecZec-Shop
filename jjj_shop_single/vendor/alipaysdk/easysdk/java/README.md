[![Maven Central](https://img.shields.io/maven-central/v/com.alipay.sdk/alipay-easysdk.svg)](https://mvnrepository.com/artifact/com.alipay.sdk/alipay-easysdk)
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Falipay%2Falipay-easysdk.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Falipay%2Falipay-easysdk?ref=badge_shield)

歡迎使用 Alipay **Easy** SDK for Java 。

Alipay Esay SDK for Java讓您不用複雜程式設計即可訪支付寶開放平臺開放的各項常用能力，SDK可以自動幫您滿足能力呼叫過程中所需的證書校驗、加簽、驗籤、傳送HTTP請求等非功能性要求。

下面向您介紹Alipay Easy SDK for Java 的基本設計理念和使用方法。

## 設計理念
不同於原有的[Alipay SDK](https://github.com/alipay/alipay-sdk-java-all)通用而全面的設計理念，Alipay Easy SDK對開放能力的API進行了更加貼近高頻場景的精心設計與裁剪，簡化了服務端呼叫方式，讓呼叫API像使用語言內建的函式一樣簡便。

同時，您也不必擔心面向高頻場景提煉的API可能無法完全契合自己的個性化場景，Alipay Easy SDK支援靈活的動態擴充套件方式，同樣可以滿足低頻引數、低頻API的使用需求。

Alipay Easy SDK提供了與[能力地圖](https://opendocs.alipay.com/mini/00am3f)相對應的程式碼組織結構，讓開發者可以快速找到不同能力對應的API。

Alipay Easy SDK主要目標是提升開發者在**服務端**整合支付寶開放平臺開放的各類核心能力的效率。

## 環境要求
1. Alipay Easy SDK for Java 需要配合`JDK 1.8`或其以上版本。

2. 使用 Alipay Easy SDK for Java 之前 ，您需要先前往[支付寶開發平臺-開發者中心](https://openhome.alipay.com/platform/developerIndex.htm)完成開發者接入的一些準備工作，包括建立應用、為應用新增功能包、設定應用的介面加簽方式等。

3. 準備工作完成後，注意儲存如下資訊，後續將作為使用SDK的輸入。

* 加簽模式為公鑰證書模式時（推薦）

`AppId`、`應用的私鑰`、`應用公鑰證書檔案`、`支付寶公鑰證書檔案`、`支付寶根證書檔案`

* 加簽模式為公鑰模式時

`AppId`、`應用的私鑰`、`支付寶公鑰`

## 安裝依賴
### 透過[Maven](https://mvnrepository.com/artifact/com.alipay.sdk/alipay-easysdk)來管理專案依賴
推薦透過Maven來管理專案依賴，您只需在專案的`pom.xml`檔案中宣告如下依賴

```xml
<dependency>
    <groupId>com.alipay.sdk</groupId>
    <artifactId>alipay-easysdk</artifactId>
    <version>Use the version shown in the maven badge</version>
</dependency>
```

## 快速開始
### 普通呼叫
以下這段程式碼示例向您展示了使用Alipay Easy SDK for Java呼叫一個API的3個主要步驟：

1. 設定引數（全域性只需設定一次）。
2. 發起API呼叫。
3. 處理響應或異常。

```java
import com.alipay.easysdk.factory.Factory;
import com.alipay.easysdk.factory.Factory.Payment;
import com.alipay.easysdk.kernel.Config;
import com.alipay.easysdk.kernel.util.ResponseChecker;
import com.alipay.easysdk.payment.facetoface.models.AlipayTradePrecreateResponse;

public class Main {
    public static void main(String[] args) throws Exception {
        // 1. 設定引數（全域性只需設定一次）
        Factory.setOptions(getOptions());
        try {
            // 2. 發起API呼叫（以建立當面付收款二維碼為例）
            AlipayTradePrecreateResponse response = Payment.FaceToFace()
                    .preCreate("Apple iPhone11 128G", "2234567890", "5799.00");
            // 3. 處理響應或異常
            if (ResponseChecker.success(response)) {
                System.out.println("呼叫成功");
            } else {
                System.err.println("呼叫失敗，原因：" + response.msg + "，" + response.subMsg);
            }
        } catch (Exception e) {
            System.err.println("呼叫遭遇異常，原因：" + e.getMessage());
            throw new RuntimeException(e.getMessage(), e);
        }
    }

    private static Config getOptions() {
        Config config = new Config();
        config.protocol = "https";
        config.gatewayHost = "openapi.alipay.com";
        config.signType = "RSA2";

        config.appId = "<-- 請填寫您的AppId，例如：2019091767145019 -->";

        // 為避免私鑰隨原始碼洩露，推薦從檔案中讀取私鑰字串而不是寫入原始碼中
        config.merchantPrivateKey = "<-- 請填寫您的應用私鑰，例如：MIIEvQIBADANB ... ... -->";

        //注：證書檔案路徑支援設定為檔案系統中的路徑或CLASS_PATH中的路徑，優先從檔案系統中載入，載入失敗後會繼續嘗試從CLASS_PATH中載入
        config.merchantCertPath = "<-- 請填寫您的應用公鑰證書檔案路徑，例如：/foo/appCertPublicKey_2019051064521003.crt -->";
        config.alipayCertPath = "<-- 請填寫您的支付寶公鑰證書檔案路徑，例如：/foo/alipayCertPublicKey_RSA2.crt -->";
        config.alipayRootCertPath = "<-- 請填寫您的支付寶根證書檔案路徑，例如：/foo/alipayRootCert.crt -->";

        //注：如果採用非證書模式，則無需賦值上面的三個證書路徑，改為賦值如下的支付寶公鑰字串即可
        // config.alipayPublicKey = "<-- 請填寫您的支付寶公鑰，例如：MIIBIjANBg... -->";

        //可設定非同步通知接收服務地址（可選）
        config.notifyUrl = "<-- 請填寫您的支付類介面非同步通知接收服務地址，例如：https://www.test.com/callback -->";

        //可設定AES金鑰，呼叫AES加解密相關介面時需要（可選）
        config.encryptKey = "<-- 請填寫您的AES金鑰，例如：aa4BtZ4tspm2wnXLb1ThQA== -->";

        return config;
    }
}
```

### 擴充套件呼叫
#### ISV代呼叫

```java
Factory.Payment.FaceToFace()
    // 呼叫agent擴充套件方法，設定app_auth_token，完成ISV代呼叫
    .agent("ca34ea491e7146cc87d25fca24c4cD11")
    .preCreate("Apple iPhone11 128G", "2234567890", "5799.00");
```

#### 設定獨立的非同步通知地址

```java
Factory.Payment.FaceToFace()
    // 呼叫asyncNotify擴充套件方法，可以為每此API呼叫，設定獨立的非同步通知地址
    // 此處設定的非同步通知地址的優先順序高於全域性Config中配置的非同步通知地址
    .asyncNotify("https://www.test.com/callback")
    .preCreate("Apple iPhone11 128G", "2234567890", "5799.00");
```

#### 設定可選業務引數

```java
List<Object> goodsDetailList = new ArrayList<>();
Map<String, Object> goodsDetail = new HashMap<>();
goodsDetail.put("goods_id", "apple-01");
goodsDetail.put("goods_name", "Apple iPhone11 128G");
goodsDetail.put("quantity", 1);
goodsDetail.put("price", "5799.00");
goodsDetailList.add(goodsDetail);

Factory.Payment.FaceToFace()
    // 呼叫optional擴充套件方法，完成可選業務引數（biz_content下的可選欄位）的設定
    .optional("seller_id", "2088102146225135")
    .optional("discountable_amount", "8.88")
    .optional("goods_detail", goodsDetailList)
    .preCreate("Apple iPhone11 128G", "2234567890", "5799.00");


Map<String, Object> optionalArgs = new HashMap<>();
optionalArgs.put("seller_id", "2088102146225135");
optionalArgs.put("discountable_amount", "8.88");
optionalArgs.put("goods_detail", goodsDetailList);

Factory.Payment.FaceToFace()
    // 也可以呼叫batchOptional擴充套件方法，批次設定可選業務引數（biz_content下的可選欄位）
    .batchOptional(optionalArgs)
    .preCreate("Apple iPhone11 128G", "2234567890", "5799.00");
```
#### 多種擴充套件靈活組合

```java
// 多種擴充套件方式可靈活組裝（對擴充套件方法的呼叫順序沒有要求）
Factory.Payment.FaceToFace()
    .agent("ca34ea491e7146cc87d25fca24c4cD11")
    .asyncNotify("https://www.test.com/callback")
    .optional("seller_id", "2088102146225135")
    .preCreate("Apple iPhone11 128G", "2234567890", "5799.00");
```

## API組織規範
在Alipay Easy SDK中，API的引用路徑與能力地圖的組織層次一致，遵循如下規範

> Factory.能力名稱.場景名稱().介面方法名稱( ... )

比如，如果您想要使用[能力地圖](https://opendocs.alipay.com/mini/00am3f)中`營銷能力`下的`模板訊息`場景中的`小程式傳送模板訊息`，只需按如下形式編寫呼叫程式碼即可。

`Factory.Marketing.TemplateMessage().send( ... )`

其中，介面方法名稱通常是對其依賴的OpenAPI功能的一個最簡概況，介面方法的出入參與OpenAPI中同名引數含義一致，可參照OpenAPI相關引數的使用說明。

Alipay Easy SDK將致力於保持良好的API命名，以符合開發者的程式設計直覺。

## 已支援的API列表

| 能力類別      | 場景類別            | 介面方法名稱                 | 呼叫的OpenAPI名稱                                              |
|-----------|-----------------|------------------------|-----------------------------------------------------------|
| Base      | OAuth           | getToken               | alipay\.system\.oauth\.token                              |
| Base      | OAuth           | refreshToken           | alipay\.system\.oauth\.token                              |
| Base      | Qrcode          | create                 | alipay\.open\.app\.qrcode\.create                         |
| Base      | Image           | upload                 | alipay\.offline\.material\.image\.upload                  |
| Base      | Video           | upload                 | alipay\.offline\.material\.image\.upload                  |
| Member    | Identification  | init                   | alipay\.user\.certify\.open\.initialize                   |
| Member    | Identification  | certify                | alipay\.user\.certify\.open\.certify                      |
| Member    | Identification  | query                  | alipay\.user\.certify\.open\.query                        |
| Payment   | Common          | create                 | alipay\.trade\.create                                     |
| Payment   | Common          | query                  | alipay\.trade\.query                                      |
| Payment   | Common          | refund                 | alipay\.trade\.refund                                     |
| Payment   | Common          | close                  | alipay\.trade\.close                                      |
| Payment   | Common          | cancel                 | alipay\.trade\.cancel                                     |
| Payment   | Common          | queryRefund            | alipay\.trade\.fastpay\.refund\.query                     |
| Payment   | Common          | downloadBill           | alipay\.data\.dataservice\.bill\.downloadurl\.query       |
| Payment   | Common          | verifyNotify           | -                                                         |
| Payment   | Huabei          | create                 | alipay\.trade\.create                                     |
| Payment   | FaceToFace      | pay                    | alipay\.trade\.pay                                        |
| Payment   | FaceToFace      | precreate              | alipay\.trade\.precreate                                  |
| Payment   | App             | pay                    | alipay\.trade\.app\.pay                                   |
| Payment   | Page            | pay                    | alipay\.trade\.page\.pay                                  |
| Payment   | Wap             | pay                    | alipay\.trade\.wap\.pay                                   |
| Security  | TextRisk        | detect                 | alipay\.security\.risk\.content\.detect                   |
| Marketing | Pass            | createTemplate         | alipay\.pass\.template\.add                               |
| Marketing | Pass            | updateTemplate         | alipay\.pass\.template\.update                            |
| Marketing | Pass            | addInstance            | alipay\.pass\.instance\.add                               |
| Marketing | Pass            | updateInstance         | alipay\.pass\.instance\.update                            |
| Marketing | TemplateMessage | send                   | alipay\.open\.app\.mini\.templatemessage\.send            |
| Marketing | OpenLife        | createImageTextContent | alipay\.open\.public\.message\.content\.create            |
| Marketing | OpenLife        | modifyImageTextContent | alipay\.open\.public\.message\.content\.modify            |
| Marketing | OpenLife        | sendText               | alipay\.open\.public\.message\.total\.send                |
| Marketing | OpenLife        | sendImageText          | alipay\.open\.public\.message\.total\.send                |
| Marketing | OpenLife        | sendSingleMessage      | alipay\.open\.public\.message\.single\.send               |
| Marketing | OpenLife        | recallMessage          | alipay\.open\.public\.life\.msg\.recall                   |
| Marketing | OpenLife        | setIndustry            | alipay\.open\.public\.template\.message\.industry\.modify |
| Marketing | OpenLife        | getIndustry            | alipay\.open\.public\.setting\.category\.query            |
| Util      | AES             | decrypt                | -                                                         |
| Util      | AES             | encrypt                | -                                                         |
| Util      | Generic         | execute                | -                                                         |

> 注：更多高頻場景的API持續更新中，敬請期待。

## 文件
[API Doc](./../APIDoc.md)

[Alipay Easy SDK](./../README.md)
