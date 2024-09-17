using System;
using System.Collections.Generic;
using Tea;
using Alipay.EasySDK.Kernel.Util;

namespace Alipay.EasySDK.Kernel
{
    public class Context
    {
        /// <summary>
        /// 客戶端配置引數
        /// </summary>
        private readonly Dictionary<string, object> config;

        /// <summary>
        /// 證書模式執行時環境
        /// </summary>
        public CertEnvironment CertEnvironment { get; }

        /// <summary>
        /// SDK版本號
        /// </summary>
        public string SdkVersion { get; set; }

        public Context(Config config, string sdkVersion)
        {
            this.config = config.ToMap();
            SdkVersion = sdkVersion;
            ArgumentValidator.CheckArgument(AlipayConstants.RSA2.Equals(GetConfig(AlipayConstants.SIGN_TYPE_CONFIG_KEY)),
               "Alipay Easy SDK只允許使用RSA2簽名方式，RSA簽名方式由於安全性相比RSA2弱已不再推薦。");

            if (!string.IsNullOrEmpty(GetConfig(AlipayConstants.ALIPAY_CERT_PATH_CONFIG_KEY)))
            {
                CertEnvironment = new CertEnvironment(
                        GetConfig(AlipayConstants.MERCHANT_CERT_PATH_CONFIG_KEY),
                        GetConfig(AlipayConstants.ALIPAY_CERT_PATH_CONFIG_KEY),
                        GetConfig(AlipayConstants.ALIPAY_ROOT_CERT_PATH_CONFIG_KEY));
            }
        }

        public string GetConfig(string key)
        {
            return (string)config[key];
        }
    }
}