using System;
using System.Text;

namespace Alipay.EasySDK.Kernel
{
    /// <summary>
    /// 支付寶開放平臺閘道器互動常用常量
    /// </summary>
    public static class AlipayConstants
    {
        /// <summary>
        /// Config配置引數Key值
        /// </summary>
        public const string PROTOCOL_CONFIG_KEY = "protocol";
        public const string HOST_CONFIG_KEY = "gatewayHost";
        public const string ALIPAY_CERT_PATH_CONFIG_KEY = "alipayCertPath";
        public const string MERCHANT_CERT_PATH_CONFIG_KEY = "merchantCertPath";
        public const string ALIPAY_ROOT_CERT_PATH_CONFIG_KEY = "alipayRootCertPath";
        public const string SIGN_TYPE_CONFIG_KEY = "signType";
        public const string NOTIFY_URL_CONFIG_KEY = "notifyUrl";

        /// <summary>
        /// 與閘道器HTTP互動中涉及到的欄位值
        /// </summary>
        public const string BIZ_CONTENT_FIELD = "biz_content";
        public const string ALIPAY_CERT_SN_FIELD = "alipay_cert_sn";
        public const string SIGN_FIELD = "sign";
        public const string SIGN_TYPE_FIELD = "sign_type";
        public const string BODY_FIELD = "http_body";
        public const string NOTIFY_URL_FIELD = "notify_url";
        public const string METHOD_FIELD = "method";
        public const string RESPONSE_SUFFIX = "_response";
        public const string ERROR_RESPONSE = "error_response";

        /// <summary>
        /// 預設字元集編碼，EasySDK統一固定使用UTF-8編碼，無需使用者感知編碼，使用者面對的總是String而不是bytes
        /// </summary>
        public readonly static Encoding DEFAULT_CHARSET = Encoding.UTF8;

        /// <summary>
        /// 預設的簽名演算法，EasySDK統一固定使用RSA2簽名演算法（即SHA_256_WITH_RSA），但此引數依然需要使用者指定以便使用者感知，因為在開放平臺介面簽名配置介面中需要選擇同樣的演算法
        /// </summary>
        public const string RSA2 = "RSA2";

        /// <summary>
        /// RSA2對應的真實簽名演算法名稱
        /// </summary>
        public const string SHA_256_WITH_RSA = "SHA256WithRSA";

        /// <summary>
        /// RSA2對應的真實非對稱加密演算法名稱
        /// </summary>
        public const string RSA = "RSA";

        /// <summary>
        /// 申請生成的重定向網頁的請求型別，GET表示生成URL
        /// </summary>
        public const string GET = "GET";

        /// <summary>
        /// 申請生成的重定向網頁的請求型別，POST表示生成form表單
        /// </summary>
        public const string POST = "POST";
    }
}
