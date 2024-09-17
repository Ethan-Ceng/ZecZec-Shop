/**
 * Alipay.com Inc. Copyright (c) 2004-2020 All Rights Reserved.
 */
package com.alipay.easysdk;

import com.alipay.easysdk.kernel.Config;
import com.alipay.easysdk.kms.aliyun.AliyunKMSConfig;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;

import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.Map;

/**
 * @author zhongyu
 * @version $Id: TestAccount.java, v 0.1 2020年01月19日 4:42 PM zhongyu Exp $
 */
public class TestAccount {
    /**
     * 從檔案中讀取私鑰
     * <p>
     * 注意：實際開發過程中，請務必注意不要將私鑰資訊配置在原始碼中（比如配置為常量或儲存在配置檔案的某個欄位中等），因為私鑰的保密等級往往比原始碼高得多，將會增加私鑰洩露的風險。推薦將私鑰資訊儲存在專用的私鑰檔案中，
     * 將私鑰檔案透過安全的流程分發到伺服器的安全儲存區域上，僅供自己的應用執行時讀取。
     * <p>
     * 此處為了單元測試執行的環境普適性，私鑰檔案配置在resources資源下，實際過程中請不要這樣做。
     *
     * @param appId 私鑰對應的APP_ID
     * @return 私鑰字串
     */
    private static String getPrivateKey(String appId) {
        InputStream stream = TestAccount.class.getResourceAsStream("/fixture/privateKey.json");
        Map<String, String> result = new Gson().fromJson(new InputStreamReader(stream), new TypeToken<Map<String, String>>() {}.getType());
        return result.get(appId);
    }

    /**
     * 從檔案中讀取阿里雲AccessKey配置資訊
     * 此處為了單元測試執行的環境普適性，AccessKey資訊配置在resources資源下，實際過程中請不要這樣做。
     * @param key AccessKey配置對應的key
     * @return AccessKey配置字串
     */
    private static String getAliyunAccessKey(String key){
            InputStream stream = TestAccount.class.getResourceAsStream("/fixture/aliyunAccessKey.json");
            Map<String, String> result = new Gson().fromJson(new InputStreamReader(stream), new TypeToken<Map<String, String>>() {}.getType());
            return result.get(key);
    }

    /**
     * 線上小程式測試賬號
     */
    public static class Mini {
        public static final Config CONFIG = getConfig();

        public static Config getConfig() {
            Config config = new Config();
            config.protocol = "https";
            config.gatewayHost = "openapi.alipay.com";
            config.appId = "2019022663440152";
            config.signType = "RSA2";

            config.alipayPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAumX1EaLM4ddn1Pia4SxTRb62aVYxU8I2mHMqrc"
                    + "pQU6F01mIO/DjY7R4xUWcLi0I2oH/BK/WhckEDCFsGrT7mO+JX8K4sfaWZx1aDGs0m25wOCNjp+DCVBXotXSCurqgGI/9UrY+"
                    + "QydYDnsl4jB65M3p8VilF93MfS01omEDjUW+1MM4o3FP0khmcKsoHnYGs21btEeh0LK1gnnTDlou6Jwv3Ew36CbCNY2cYkuyP"
                    + "AW0j47XqzhWJ7awAx60fwgNBq6ZOEPJnODqH20TAdTLNxPSl4qGxamjBO+RuInBy+Bc2hFHq3pNv6hTAfktggRKkKzDlDEUwg"
                    + "SLE7d2eL7P6rwIDAQAB";
            config.merchantPrivateKey = getPrivateKey(config.appId);
            config.notifyUrl = "https://www.test.com/callback";
            return config;
        }
    }

    /**
     * 線上生活號測試賬號
     */
    public static class OpenLife {
        public static final Config CONFIG = getConfig();

        private static Config getConfig() {
            Config config = new Config();
            config.protocol = "https";
            config.gatewayHost = "openapi.alipay.com";
            config.appId = "2021002177673029";
            config.signType = "RSA2";

            config.alipayCertPath = "src/test/resources/fixture/alipayCertPublicKey_RSA2.crt";
            config.alipayRootCertPath = "src/test/resources/fixture/alipayRootCert.crt";
            config.merchantCertPath = "src/test/resources/fixture/appCertPublicKey_2021002177673029.crt";
            config.merchantPrivateKey = getPrivateKey(config.appId);
            return config;
        }
    }

    /**
     * Aliyun KMS簽名測試賬號
     */
    public static class AliyunKMS {
        public static final AliyunKMSConfig CONFIG = getConfig();

        private static AliyunKMSConfig getConfig() {
            AliyunKMSConfig config = new AliyunKMSConfig();
            config.protocol = "https";
            config.gatewayHost = "openapi.alipay.com";
            config.appId = "2021001163614348";
            config.signType = "RSA2";
            config.notifyUrl = "https://www.test.com/callback";

            config.alipayPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiOgupSXhUE3GkMDeCpeDwoEM2z+krBpaKPFbfS" +
                    "JgFVoN/M1s62VC6LhFI9aL4F76bqMGilQPpe2ukW5UmLR+C3OmliuqE/v5/UEpasnndcZMEKadQbWOpQ4eBHGkKTASQhtbgYb3U" +
                    "WS+viD5MfHS0+3h+sko8cW06jONmjG2tvFpnmooIjMawXByK8/f4vBMBk4ZQQodo4TT18mhyyyIoilhLH2EatQp/lov54ZhwHi9" +
                    "8LXeLw7Yt4QK8q7u+lB34V8lsu9zVMEMZExhoblsdjgzFAY6KzCn/QGnQE5e54i59+wONAyf2npUkz4cpPDJPLQ7KBu1febsZjk" +
                    "g9vZrXwIDAQAB";

            //如果使用阿里雲KMS簽名，則不需要配置私鑰
            //config.merchantPrivateKey = getPrivateKey(config.appId);

            //如果使用第三方簽名服務，則需要指定簽名提供方名稱，阿里雲KMS的名稱為"AliyunKMS"
            config.signProvider = "AliyunKMS";

            //如果使用阿里雲KMS簽名，需要更換為您的阿里雲賬號資訊
            config.aliyunAccessKeyId = getAliyunAccessKey("AccessKeyId");
            config.aliyunAccessKeySecret = getAliyunAccessKey("AccessKeySecret");
            config.kmsKeyId = "4358f298-8e30-4849-9791-52e68dbd9d1e";
            config.kmsKeyVersionId = "e71daa69-c321-4014-b0c4-ba070c7839ee";

            //如果使用阿里雲KMS簽名，需要更換為您的KMS服務地址
            // KMS服務地址列表詳情，請參考：
            // https://help.aliyun.com/document_detail/69006.html?spm=a2c4g.11186623.2.9.783f77cfAoNhY6#concept-69006-zh
            config.kmsEndpoint = "kms.cn-hangzhou.aliyuncs.com";

            return config;
        }
    }
}