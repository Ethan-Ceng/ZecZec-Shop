using System;
using System.Collections.Generic;
using System.IO;
using Org.BouncyCastle.X509;
using Alipay.EasySDK.Kernel.Util;
using System.Linq;

namespace Alipay.EasySDK.Kernel
{
    /// <summary>
    /// 證書模式執行時環境
    /// </summary>
    public class CertEnvironment
    {

        /// <summary>
        /// 支付寶根證書內容
        /// </summary>
        public string RootCertContent { get; set; }

        /// <summary>
        /// 支付寶根證書序列號
        /// </summary>
        public string RootCertSN { get; set; }

        /// <summary>
        /// 商戶應用公鑰證書序列號
        /// </summary>
        public string MerchantCertSN { get; set; }

        /// <summary>
        /// 快取的不同支付寶公鑰證書序列號對應的支付寶公鑰
        /// </summary>
        private readonly Dictionary<string, string> CachedAlipayPublicKey = new Dictionary<string, string>();

        /// <summary>
        /// 構造證書執行環境
        /// </summary>
        /// <param name="merchantCertPath">商戶公鑰證書路徑</param>
        /// <param name="alipayCertPath">支付寶公鑰證書路徑</param>
        /// <param name="alipayRootCertPath">支付寶根證書路徑</param>
        public CertEnvironment(string merchantCertPath, string alipayCertPath, string alipayRootCertPath)
        {
            if (string.IsNullOrEmpty(merchantCertPath) || string.IsNullOrEmpty(alipayCertPath) || string.IsNullOrEmpty(alipayCertPath))
            {
                throw new Exception("證書引數merchantCertPath、alipayCertPath或alipayRootCertPath設定不完整。");
            }

            this.RootCertContent = File.ReadAllText(alipayRootCertPath);
            this.RootCertSN = AntCertificationUtil.GetRootCertSN(RootCertContent);

            X509Certificate merchantCert = AntCertificationUtil.ParseCert(File.ReadAllText(merchantCertPath));
            this.MerchantCertSN = AntCertificationUtil.GetCertSN(merchantCert);

            X509Certificate alipayCert = AntCertificationUtil.ParseCert(File.ReadAllText(alipayCertPath));
            string alipayCertSN = AntCertificationUtil.GetCertSN(alipayCert);
            string alipayPublicKey = AntCertificationUtil.ExtractPemPublicKeyFromCert(alipayCert);
            CachedAlipayPublicKey[alipayCertSN] = alipayPublicKey;
        }

        public string GetAlipayPublicKey(string sn)
        {
            //如果沒有指定sn，則預設取快取中的第一個值
            if (string.IsNullOrEmpty(sn))
            {
                return CachedAlipayPublicKey.Values.FirstOrDefault();
            }

            if (CachedAlipayPublicKey.ContainsKey(sn))
            {
                return CachedAlipayPublicKey[sn];
            }
            else
            {
                //閘道器在支付寶公鑰證書變更前，一定會確認通知到商戶並在商戶做出反饋後，才會更新該商戶的支付寶公鑰證書
                //TODO: 後續可以考慮加入自動升級支付寶公鑰證書邏輯，注意併發更新衝突問題
                throw new Exception("支付寶公鑰證書[" + sn + "]已過期，請重新下載最新支付寶公鑰證書並替換原證書檔案");
            }
        }
    }
}
