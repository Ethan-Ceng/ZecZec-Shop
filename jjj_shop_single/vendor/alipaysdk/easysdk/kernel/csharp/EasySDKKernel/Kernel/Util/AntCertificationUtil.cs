using System.Collections.Generic;
using System;
using Org.BouncyCastle.X509;
using Org.BouncyCastle.Asn1.X509;
using Org.BouncyCastle.Crypto;
using System.Security.Cryptography;
using System.Text;
using System.Linq;

namespace Alipay.EasySDK.Kernel.Util
{
    /// <summary>
    /// 證書相關工具類
    /// </summary>
    public static class AntCertificationUtil
    {
        /// <summary>
        /// 提取根證書序列號
        /// </summary>
        /// <param name="rootCertContent">根證書文字</param>
        /// <returns>根證書序列號</returns>
        public static string GetRootCertSN(string rootCertContent)
        {
            string rootCertSN = "";
            try
            {
                List<X509Certificate> x509Certificates = ReadPemCertChain(rootCertContent);
                foreach (X509Certificate cert in x509Certificates)
                {
                    //只提取與指定演算法型別匹配的證書的序列號
                    if (cert.SigAlgOid.StartsWith("1.2.840.113549.1.1", StringComparison.Ordinal))
                    {
                        string certSN = GetCertSN(cert);
                        if (string.IsNullOrEmpty(rootCertSN))
                        {
                            rootCertSN = certSN;
                        }
                        else
                        {
                            rootCertSN = rootCertSN + "_" + certSN;
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                throw new Exception("提取根證書序列號失敗。" + ex.Message);
            }
            return rootCertSN;
        }

        /// <summary>
        /// 反序列化證書文字
        /// </summary>
        /// <param name="certContent">證書文字</param>
        /// <returns>X509Certificate證書物件</returns>
        public static X509Certificate ParseCert(string certContent)
        {
            return new X509CertificateParser().ReadCertificate(Encoding.UTF8.GetBytes(certContent));
        }

        /// <summary>
        /// 計算指定證書的序列號
        /// </summary>
        /// <param name="cert">證書</param>
        /// <returns>序列號</returns>
        public static string GetCertSN(X509Certificate cert)
        {
            string issuerDN = cert.IssuerDN.ToString();
            //提取出的證書的issuerDN本身是以CN開頭的，則無需逆序，直接返回
            if (issuerDN.StartsWith("CN", StringComparison.Ordinal))
            {
                return CalculateMd5(issuerDN + cert.SerialNumber);
            }
            List<string> attributes = issuerDN.Split(',').ToList();
            attributes.Reverse();
            return CalculateMd5(string.Join(",", attributes.ToArray()) + cert.SerialNumber);
        }

        /// <summary>
        /// 校驗證書鏈是否可信
        /// </summary>
        /// <param name="certContent">需要驗證的目標證書或者證書鏈文字</param>
        /// <param name="rootCertContent">可信根證書列表文字</param>
        /// <returns>true：證書可信；false：證書不可信</returns>
        public static bool IsTrusted(string certContent, string rootCertContent)
        {
            List<X509Certificate> certs = ReadPemCertChain(certContent);
            List<X509Certificate> rootCerts = ReadPemCertChain(rootCertContent);
            return VerifyCertChain(certs, rootCerts);
        }

        /// <summary>
        /// 從證書鏈文字反序列化證書鏈集合
        /// </summary>
        /// <param name="cert">證書鏈文字</param>
        /// <returns>證書鏈集合</returns>
        private static List<X509Certificate> ReadPemCertChain(string cert)
        {
            System.Collections.ICollection collection = new X509CertificateParser().ReadCertificates(Encoding.UTF8.GetBytes(cert));
            List<X509Certificate> result = new List<X509Certificate>();
            foreach (var each in collection)
            {
                result.Add((X509Certificate)each);
            }
            return result;
        }

        /// <summary>
        /// 將證書鏈按照完整的簽發順序進行排序，排序後證書鏈為：[issuerA, subjectA]-[issuerA, subjectB]-[issuerB, subjectC]-[issuerC, subjectD]...
        /// </summary>
        /// <param name="certChain">未排序的證書鏈</param>
        /// <returns>true：排序成功；false：證書鏈不完整</returns>
        private static bool SortCertChain(List<X509Certificate> certChain)
        {
            //主題和證書的對映
            Dictionary<X509Name, X509Certificate> subject2CertMap = new Dictionary<X509Name, X509Certificate>();
            //簽發者和證書的對映
            Dictionary<X509Name, X509Certificate> issuer2CertMap = new Dictionary<X509Name, X509Certificate>();
            //是否包含自簽名證書
            bool hasSelfSignedCert = false;
            foreach (X509Certificate cert in certChain)
            {
                if (IsSelfSigned(cert))
                {
                    if (hasSelfSignedCert)
                    {
                        //同一條證書鏈中只能有一個自簽名證書
                        return false;
                    }
                    hasSelfSignedCert = true;
                }
                subject2CertMap[cert.SubjectDN] = cert;
                issuer2CertMap[cert.IssuerDN] = cert;
            }

            List<X509Certificate> orderedCertChain = new List<X509Certificate>();

            X509Certificate current = certChain[0];

            AddressingUp(subject2CertMap, orderedCertChain, current);
            AddressingDown(issuer2CertMap, orderedCertChain, current);

            //說明證書鏈不完整
            if (certChain.Count != orderedCertChain.Count)
            {
                return false;
            }

            //用排序後的結果覆蓋傳入的證書鏈集合
            for (int i = 0; i < orderedCertChain.Count; i++)
            {
                certChain[i] = orderedCertChain[i];
            }
            return true;
        }

        private static bool IsSelfSigned(X509Certificate cert)
        {
            return cert.SubjectDN.Equivalent(cert.IssuerDN);
        }

        /// <summary>
        /// 向上構造證書鏈
        /// </summary>
        /// <param name="subject2CertMap">主題與證書的對映</param>
        /// <param name="orderedCertChain">儲存排序後的證書鏈集合</param>
        /// <param name="current">當前需要插入排序後的證書鏈集合中的證書</param>
        private static void AddressingUp(Dictionary<X509Name, X509Certificate> subject2CertMap,
            List<X509Certificate> orderedCertChain, X509Certificate current)
        {
            orderedCertChain.Insert(0, current);
            if (IsSelfSigned(current))
            {
                return;
            }

            if (!subject2CertMap.ContainsKey(current.IssuerDN))
            {
                return;
            }

            X509Certificate issuer = subject2CertMap[current.IssuerDN];
            AddressingUp(subject2CertMap, orderedCertChain, issuer);
        }

        /// <summary>
        /// 向下構造證書鏈
        /// </summary>
        /// <param name="issuer2CertMap">簽發者和證書的對映</param>
        /// <param name="certChain">儲存排序後的證書鏈集合</param>
        /// <param name="current">當前需要插入排序後的證書鏈集合中的證書</param>
        private static void AddressingDown(Dictionary<X509Name, X509Certificate> issuer2CertMap,
            List<X509Certificate> certChain, X509Certificate current)
        {
            if (!issuer2CertMap.ContainsKey(current.SubjectDN))
            {
                return;
            }

            X509Certificate subject = issuer2CertMap[current.SubjectDN];
            if (IsSelfSigned(subject))
            {
                return;
            }
            certChain.Add(subject);
            AddressingDown(issuer2CertMap, certChain, subject);
        }

        /// <summary>
        /// 驗證證書是否是信任證書庫中的證書籤發的
        /// </summary>
        /// <param name="cert">待驗證證書</param>
        /// <param name="rootCerts">可信根證書列表</param>
        /// <returns>true：驗證透過；false：驗證不透過</returns>
        private static bool VerifyCert(X509Certificate cert, List<X509Certificate> rootCerts)
        {
            if (!cert.IsValidNow)
            {
                return false;
            }

            Dictionary<X509Name, X509Certificate> subject2CertMap = new Dictionary<X509Name, X509Certificate>();
            foreach (X509Certificate root in rootCerts)
            {
                subject2CertMap[root.SubjectDN] = root;
            }

            X509Name issuerDN = cert.IssuerDN;
            if (!subject2CertMap.ContainsKey(issuerDN))
            {
                return false;
            }

            X509Certificate issuer = subject2CertMap[issuerDN];
            try
            {
                AsymmetricKeyParameter publicKey = issuer.GetPublicKey();
                cert.Verify(publicKey);
            }
            catch (Exception ex)
            {
                Console.WriteLine("證書驗證出現異常。" + ex.Message);
                return false;
            }
            return true;
        }

        /// <summary>
        /// 驗證證書列表
        /// </summary>
        /// <param name="certs">待驗證的證書列表</param>
        /// <param name="rootCerts">可信根證書列表</param>
        /// <returns>true：驗證透過；false：驗證不透過</returns>
        private static bool VerifyCertChain(List<X509Certificate> certs, List<X509Certificate> rootCerts)
        {
            //證書列表排序，形成排序後的證書鏈
            bool sorted = SortCertChain(certs);
            if (!sorted)
            {
                //不是完整的證書鏈
                return false;
            }

            //先驗證第一個證書是不是信任庫中證書籤發的
            X509Certificate previous = certs[0];
            bool firstOK = VerifyCert(previous, rootCerts);
            if (!firstOK || certs.Count == 1)
            {
                return firstOK;
            }

            //驗證證書鏈
            for (int i = 1; i < certs.Count; i++)
            {
                try
                {
                    X509Certificate cert = certs[i];
                    if (!cert.IsValidNow)
                    {
                        return false;
                    }

                    //用上級證書的公鑰驗證本證書是否是上級證書籤發的
                    cert.Verify(previous.GetPublicKey());

                    previous = cert;
                }
                catch (Exception ex)
                {
                    //證書鏈驗證失敗
                    Console.WriteLine("證書鏈驗證失敗。" + ex.Message);
                    return false;
                }
            }

            return true;
        }


        private static string CalculateMd5(string input)
        {
            using (MD5 md5 = new MD5CryptoServiceProvider())
            {
                string result = "";
                byte[] bytes = md5.ComputeHash(Encoding.GetEncoding("utf-8").GetBytes(input));
                for (int i = 0; i < bytes.Length; i++)
                {
                    result += bytes[i].ToString("x2");
                }
                return result;
            }
        }

        /// <summary>
        /// 從證書中提取公鑰並轉換為PEM編碼
        /// </summary>
        /// <param name="input">證書</param>
        /// <returns>PEM編碼公鑰</returns>
        public static string ExtractPemPublicKeyFromCert(X509Certificate input)
        {
            SubjectPublicKeyInfo subjectPublicKeyInfo = SubjectPublicKeyInfoFactory.CreateSubjectPublicKeyInfo(input.GetPublicKey());
            return Convert.ToBase64String(subjectPublicKeyInfo.GetDerEncoded());
        }
    }
}