using System.Collections.Generic;
using System.IO;
using System;
using Alipay.EasySDK.Kernel;
using Newtonsoft.Json;

namespace UnitTest
{
    public static class TestAccount
    {
        /// <summary>
        /// 從檔案中讀取私鑰
        /// 
        /// 注意：實際開發過程中，請務必注意不要將私鑰資訊配置在原始碼中（比如配置為常量或儲存在配置檔案的某個欄位中等），因為私鑰的保密等級往往比原始碼高得多，將會增加私鑰洩露的風險。
        /// 推薦將私鑰資訊儲存在專用的私鑰檔案中，將私鑰檔案透過安全的流程分發到伺服器的安全儲存區域上，僅供自己的應用執行時讀取。
        /// </summary>
        /// <param name="appId">私鑰對應的APP_ID</param>
        /// <returns>私鑰字串</returns>
        private static string GetPrivateKey(string appId)
        {
            IDictionary<string, string> json = JsonConvert.DeserializeObject<IDictionary<string, string>>(
                File.ReadAllText(GetSolutionBasePath() + "/UnitTest/Fixture/privateKey.json"));
            return json[appId];
        }

        /// <summary>
        /// 獲取解決方案所在路徑
        /// </summary>
        /// <returns>解決方案所在絕對路徑</returns>
        public static string GetSolutionBasePath()
        {
            string current = Directory.GetCurrentDirectory();
            do
            {
                current = Directory.GetParent(current).ToString();
            }
            while (!current.EndsWith("bin", StringComparison.Ordinal));
            return current + "/../..";
        }

        /// <summary>
        /// 線上小程式測試賬號
        /// </summary>
        public static class Mini
        {
            public static Config CONFIG = GetConfig();

            public static Config GetConfig()
            {
                return new Config
                {
                    Protocol = "https",
                    GatewayHost = "openapi.alipay.com",
                    AppId = "<-- 請填寫您的AppId，例如：2019022663440152 -->",
                    SignType = "RSA2",

                    AlipayPublicKey = "<-- 請填寫您的支付寶公鑰，例如：MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAumX1EaLM4ddn1Pia4SxTRb62aVYxU8I2mHMqrc"
                       + "pQU6F01mIO/DjY7R4xUWcLi0I2oH/BK/WhckEDCFsGrT7mO+JX8K4sfaWZx1aDGs0m25wOCNjp+DCVBXotXSCurqgGI/9UrY+"
                       + "QydYDnsl4jB65M3p8VilF93MfS01omEDjUW+1MM4o3FP0khmcKsoHnYGs21btEeh0LK1gnnTDlou6Jwv3Ew36CbCNY2cYkuyP"
                       + "AW0j47XqzhWJ7awAx60fwgNBq6ZOEPJnODqH20TAdTLNxPSl4qGxamjBO+RuInBy+Bc2hFHq3pNv6hTAfktggRKkKzDlDEUwg"
                       + "SLE7d2eL7P6rwIDAQAB -->",
                    MerchantPrivateKey = GetPrivateKey("<-- 請填寫您的AppId，例如：2019022663440152 -->"),
                    NotifyUrl = "<-- 請填寫您的非同步通知接收伺服器地址，例如：https://www.test.com/callback"
                };
            }
        }

        /// <summary>
        /// 線上生活號測試賬號
        /// </summary>
        public static class OpenLife
        {
            public static Config CONFIG = new Config
            {
                Protocol = "https",
                GatewayHost = "openapi.alipay.com",
                AppId = "<-- 請填寫您的AppId，例如：2019051064521003 -->",
                SignType = "RSA2",

                AlipayCertPath = "<-- 請填寫您的支付寶公鑰證書檔案路徑，例如：GetSolutionBasePath() + \"/UnitTest/Fixture/alipayCertPublicKey_RSA2.crt\" -->",
                AlipayRootCertPath = "<-- 請填寫您的支付寶根證書檔案路徑，例如：GetSolutionBasePath() + \"/UnitTest/Fixture/alipayRootCert.crt\" -->",
                MerchantCertPath = "<-- 請填寫您的應用公鑰證書檔案路徑，例如：GetSolutionBasePath() + \"/UnitTest/Fixture/appCertPublicKey_2019051064521003.crt\" -->",
                MerchantPrivateKey = GetPrivateKey("<-- 請填寫您的AppId，例如：2019051064521003 -->")
            };
        }
    }
}
