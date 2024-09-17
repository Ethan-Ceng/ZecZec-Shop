using Tea;

namespace Alipay.EasySDK.Kernel
{
    /// <summary>
    /// 客戶端配置引數模型
    /// </summary>
    public class Config : TeaModel
    {
        /// <summary>
        /// 通訊協議，通常填寫https
        /// </summary>
        [NameInMap("protocol")]
        [Validation(Required = true)]
        public string Protocol { get; set; } = "https";

        /// <summary>
        /// 閘道器域名
        /// 線上為：openapi.alipay.com
        /// 沙箱為：openapi.alipaydev.com
        /// </summary>
        [NameInMap("gatewayHost")]
        [Validation(Required = true)]
        public string GatewayHost { get; set; } = "openapi.alipay.com";

        /// <summary>
        /// AppId
        /// </summary>
        [NameInMap("appId")]
        [Validation(Required = true)]
        public string AppId { get; set; }

        /// <summary>
        /// 簽名型別，Alipay Easy SDK只推薦使用RSA2，估此處固定填寫RSA2
        /// </summary>
        [NameInMap("signType")]
        [Validation(Required = true)]
        public string SignType { get; set; } = "RSA2";

        /// <summary>
        /// 支付寶公鑰
        /// </summary>
        [NameInMap("alipayPublicKey")]
        [Validation(Required = true)]
        public string AlipayPublicKey { get; set; }

        /// <summary>
        /// 應用私鑰
        /// </summary>
        [NameInMap("merchantPrivateKey")]
        [Validation(Required = true)]
        public string MerchantPrivateKey { get; set; }

        /// <summary>
        /// 應用公鑰證書檔案路徑
        /// </summary>
        [NameInMap("merchantCertPath")]
        [Validation(Required = true)]
        public string MerchantCertPath { get; set; }

        /// <summary>
        /// 支付寶公鑰證書檔案路徑
        /// </summary>
        [NameInMap("alipayCertPath")]
        [Validation(Required = true)]
        public string AlipayCertPath { get; set; }

        /// <summary>
        /// 支付寶根證書檔案路徑
        /// </summary>
        [NameInMap("alipayRootCertPath")]
        [Validation(Required = true)]
        public string AlipayRootCertPath { get; set; }

        /// <summary>
        /// 非同步通知回撥地址（可選）
        /// </summary>
        [NameInMap("notifyUrl")]
        [Validation(Required = true)]
        public string NotifyUrl { get; set; }

        /// <summary>
        /// AES金鑰（可選）
        /// </summary>
        [NameInMap("encryptKey")]
        [Validation(Required = true)]
        public string EncryptKey { get; set; }


        /// <summary>
        /// 代理地址（可選）例如：http://127.0.0.1:8080
        /// </summary>
        [NameInMap("httpProxy")]
        [Validation(Required = true)]
        public string HttpProxy { get; set; }

        /// <summary>
        /// 忽略證書校驗（可選）
        /// </summary>
        [NameInMap("ignoreSSL")]
        [Validation(Required = true)]
        public string IgnoreSSL { get; set; }

        }

}
