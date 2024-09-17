[![NuGet](https://badge.fury.io/nu/AlipayEasySDK.svg)](https://badge.fury.io/nu/AlipayEasySDK)
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Falipay%2Falipay-easysdk.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Falipay%2Falipay-easysdk?ref=badge_shield)

歡迎使用 Alipay **Easy** SDK for .NET 。

Alipay Esay SDK for .NET讓您不用複雜程式設計即可訪支付寶開放平臺開放的各項常用能力，SDK可以自動幫您滿足能力呼叫過程中所需的證書校驗、加簽、驗籤、傳送HTTP請求等非功能性要求。

下面向您介紹Alipay Easy SDK for .NET 的基本設計理念和使用方法。

## 設計理念
不同於原有的[Alipay SDK](https://github.com/alipay/alipay-sdk-net-all)通用而全面的設計理念，Alipay Easy SDK對開放能力的API進行了更加貼近高頻場景的精心設計與裁剪，簡化了服務端呼叫方式，讓呼叫API像使用語言內建的函式一樣簡便。

同時，您也不必擔心面向高頻場景提煉的API可能無法完全契合自己的個性化場景，Alipay Easy SDK支援靈活的動態擴充套件方式，同樣可以滿足低頻引數、低頻API的使用需求。

Alipay Easy SDK提供了與[能力地圖](https://opendocs.alipay.com/mini/00am3f)相對應的程式碼組織結構，讓開發者可以快速找到不同能力對應的API。

Alipay Easy SDK主要目標是提升開發者在**服務端**整合支付寶開放平臺開放的各類核心能力的效率。

## 環境要求
1. Alipay Easy SDK for .NET基於`.Net Standard 2.0`開發，支援`.Net Framework 4.6.1`、.`Net Core 2.0`及其以上版本

2. 使用 Alipay Easy SDK for .NET 之前 ，您需要先前往[支付寶開發平臺-開發者中心](https://openhome.alipay.com/platform/developerIndex.htm)完成開發者接入的一些準備工作，包括建立應用、為應用新增功能包、設定應用的介面加簽方式等。

3. 準備工作完成後，注意儲存如下資訊，後續將作為使用SDK的輸入。

* 加簽模式為公鑰證書模式時（推薦）

`AppId`、`應用的私鑰`、`應用公鑰證書檔案`、`支付寶公鑰證書檔案`、`支付寶根證書檔案`

* 加簽模式為公鑰模式時

`AppId`、`應用的私鑰`、`支付寶公鑰`

## 安裝依賴
### 透過[NuGet](https://www.nuget.org/packages/AlipayEasySDK/)程式包管理器線上安裝依賴（推薦）
* 在 `解決方案資源管理器面板` 中右擊您的專案選擇 `管理 NuGet 程式包` 選單，在開啟的 `NuGet 管理面板` 中點選 `瀏覽` 選項卡輸入 `AlipayEasySDK`，在下方列表中選擇 `Authors` 為 `antopen` 由官方釋出的**最新穩定版**NuGet包，點選 **安裝** 即可。

* 或者透過 .NET CLI 工具來安裝
	> dotnet add package AlipayEasySDK

### 離線安裝NuGet包（適用於自己修改原始碼後的本地重新打包安裝）
1. 使用`Visual Studio`開啟本`README.md`所在資料夾下的`AlipayEasySDK.sln`解決方案，在`生成`選單欄下，執行`全部重新生成`。
2. 在`AlipayEasySDK/bin/Debug`或`AlipayEasySDK/bin/Release`目錄下，找到`AlipayEasySDK.[version].nupkg`檔案，該檔案即為本SDK的NuGet離線包。
3. 參照[NuGet離線安裝程式包使用指南](https://yq.aliyun.com/articles/689227)，在您的.NET應用專案工程中引入本SDK的NuGet離線包，即可完成SDK的依賴安裝。

## 快速開始
### 普通呼叫
以下這段程式碼示例向您展示了使用Alipay Easy SDK for .NET呼叫一個API的3個主要步驟：

1. 設定引數（全域性只需設定一次）。
2. 發起API呼叫。
3. 處理響應或異常。

```charp
using System;
using Alipay.EasySDK.Factory;
using Alipay.EasySDK.Kernel;
using Alipay.EasySDK.Kernel.Util;
using Alipay.EasySDK.Payment.FaceToFace.Models;

namespace SDKDemo
{
    class Program
    {
        static void Main(string[] args)
        {
            // 1. 設定引數（全域性只需設定一次）
            Factory.SetOptions(GetConfig());
            try
            {
                // 2. 發起API呼叫（以建立當面付收款二維碼為例）
                AlipayTradePrecreateResponse response = Factory.Payment.FaceToFace()
                    .PreCreate("Apple iPhone11 128G", "2234567234890", "5799.00");
                // 3. 處理響應或異常
                if (ResponseChecker.Success(response))
                {
                    Console.WriteLine("呼叫成功");
                }
                else
                {
                    Console.WriteLine("呼叫失敗，原因：" + response.Msg + "，" + response.SubMsg);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine("呼叫遭遇異常，原因：" + ex.Message);
                throw ex;
            }
        }

        static private Config GetConfig()
        {
            return new Config()
            {
                Protocol = "https",
                GatewayHost = "openapi.alipay.com",
                SignType = "RSA2",

                AppId = "<-- 請填寫您的AppId，例如：2019091767145019 -->",

                // 為避免私鑰隨原始碼洩露，推薦從檔案中讀取私鑰字串而不是寫入原始碼中
                MerchantPrivateKey = "<-- 請填寫您的應用私鑰，例如：MIIEvQIBADANB ... ... -->",

                MerchantCertPath = "<-- 請填寫您的應用公鑰證書檔案路徑，例如：/foo/appCertPublicKey_2019051064521003.crt -->",
                AlipayCertPath = "<-- 請填寫您的支付寶公鑰證書檔案路徑，例如：/foo/alipayCertPublicKey_RSA2.crt -->",
                AlipayRootCertPath = "<-- 請填寫您的支付寶根證書檔案路徑，例如：/foo/alipayRootCert.crt -->",

                // 如果採用非證書模式，則無需賦值上面的三個證書路徑，改為賦值如下的支付寶公鑰字串即可
                // AlipayPublicKey = "<-- 請填寫您的支付寶公鑰，例如：MIIBIjANBg... -->"

                //可設定非同步通知接收服務地址（可選）
                NotifyUrl = "<-- 請填寫您的支付類介面非同步通知接收服務地址，例如：https://www.test.com/callback -->",

                //可設定AES金鑰，呼叫AES加解密相關介面時需要（可選）
                EncryptKey = "<-- 請填寫您的AES金鑰，例如：aa4BtZ4tspm2wnXLb1ThQA== -->"
            };
        }
    }
}
```

### 擴充套件呼叫
#### ISV代呼叫

```csharp
Factory.Payment.FaceToFace()
    //呼叫Agent擴充套件方法，設定app_auth_token，完成ISV代呼叫
    .Agent("ca34ea491e7146cc87d25fca24c4cD11")
    .PreCreate("Apple iPhone11 128G", "2234567890", "5799.00");
```

#### 設定獨立的非同步通知地址

```csharp
Factory.Payment.FaceToFace()
    // 呼叫AsyncNotify擴充套件方法，可以為每此API呼叫，設定獨立的非同步通知地址
    // 此處設定的非同步通知地址的優先順序高於全域性Config中配置的非同步通知地址
    .AsyncNotify("https://www.test.com/callback")
    .PreCreate("Apple iPhone11 128G", "2234567890", "5799.00");
```

#### 設定可選業務引數

```csharp
List<object> goodsDetailList = new List<object>();
Dictionary<string, object> goodsDetail = new Dictionary<string, object>
{
    { "goods_id", "apple-01" },
    { "goods_name", "Apple iPhone11 128G" },
    { "quantity", 1 },
    { "price", "5799.00" }
};
goodsDetailList.Add(goodsDetail);

Factory.Payment.FaceToFace()
    // 呼叫Optional擴充套件方法，完成可選業務引數（biz_content下的可選欄位）的設定
    .Optional("seller_id", "2088102146225135")
    .Optional("discountable_amount", "8.88")
    .Optional("goods_detail", goodsDetailList)
    .PreCreate("Apple iPhone11 128G", "2234567890", "5799.00");

Dictionary<string, object> optionalArgs = new Dictionary<string, object>
{
    { "seller_id", "2088102146225135" },
    { "discountable_amount", "8.88" },
    { "goods_detail", goodsDetailList }
};

Factory.Payment.FaceToFace()
    // 也可以呼叫BatchOptional擴充套件方法，批次設定可選業務引數（biz_content下的可選欄位）
    .BatchOptional(optionalArgs)
    .PreCreate("Apple iPhone11 128G", "2234567890", "5799.00");
```

#### 多種擴充套件靈活組合

```csharp
// 多種擴充套件方式可靈活組裝（對擴充套件方法的呼叫順序沒有要求）
Factory.Payment.FaceToFace()
    .Agent("ca34ea491e7146cc87d25fca24c4cD11")
    .AsyncNotify("https://www.test.com/callback")
    .Optional("seller_id", "2088102146225135")
    .PreCreate("Apple iPhone11 128G", "2234567890", "5799.00");
```

## API組織規範
在Alipay Easy SDK中，API的引用路徑與能力地圖的組織層次一致，遵循如下規則

> Factory.能力名稱.場景名稱().介面方法名稱( ... )

比如，如果您想要使用[能力地圖](https://opendocs.alipay.com/mini/00am3f)中`營銷能力`下的`模板訊息`場景中的`小程式傳送模板訊息`，只需按如下形式編寫呼叫程式碼即可（不同程式語言的連線符號可能不同）。

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
