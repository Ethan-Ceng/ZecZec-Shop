using System;
using System.Collections.Generic;
using System.Text;
using System.Web;
using System.IO;
using System.Threading.Tasks;
using Newtonsoft.Json;

using Alipay.EasySDK.Kernel.Util;

using Tea;

namespace Alipay.EasySDK.Kernel
{
    /// <summary>
    /// Tea DSL編排所需實現的原子方法
    /// </summary>
    public class Client
    {
        /// <summary>
        /// 構造成本較高的一些引數快取在上下文中
        /// </summary>
        private readonly Context context;

        /// <summary>
        /// 注入的可選額外文字引數集合
        /// </summary>
        private readonly Dictionary<string, string> optionalTextParams = new Dictionary<string, string>();

        /// <summary>
        /// 注入的可選業務引數集合
        /// </summary>
        private readonly Dictionary<string, object> optionalBizParams = new Dictionary<string, object>();

        /// <summary>
        /// 建構函式
        /// </summary>
        /// <param name="context">上下文物件</param>
        public Client(Context context)
        {
            this.context = context;
        }

        /// <summary>
        /// 注入額外文字引數
        /// </summary>
        /// <param name="key">引數名稱</param>
        /// <param name="value">引數的值</param>
        /// <returns>本客戶端本身，便於鏈路呼叫</returns>
        public Client InjectTextParam(String key, String value)
        {
            optionalTextParams.Add(key, value);
            return this;
        }

        /// <summary>
        /// 注入額外業務引數
        /// </summary>
        /// <param name="key">引數名稱</param>
        /// <param name="value">引數的值</param>
        /// <returns>本客戶端本身，便於鏈式呼叫</returns>
        public Client InjectBizParam(String key, Object value)
        {
            optionalBizParams.Add(key, value);
            return this;
        }

        /// <summary>
        /// 獲取Config中的配置項
        /// </summary>
        /// <param name="key">配置項的名稱</param>
        /// <returns>配置項的值</returns>
        public string GetConfig(string key)
        {
            return context.GetConfig(key);
        }

        /// <summary>
        /// 是否是證書模式
        /// </summary>
        /// <returns>true：是；false：不是</returns>
        public bool IsCertMode()
        {
            return context.CertEnvironment != null;
        }

        /// <summary>
        /// 獲取時間戳，格式yyyy-MM-dd HH:mm:ss
        /// </summary>
        /// <returns>當前時間戳</returns>
        public string GetTimestamp()
        {
            return DateTime.UtcNow.AddHours(8).ToString("yyyy-MM-dd HH:mm:ss");
        }

        /// <summary>
        /// 計算簽名，注意要去除key或value為null的鍵值對
        /// </summary>
        /// <param name="systemParams">系統引數集合</param>
        /// <param name="bizParams">業務引數集合</param>
        /// <param name="textParams">其他額外文字引數集合</param>
        /// <param name="privateKey">私鑰</param>
        /// <returns>簽名值的Base64串</returns>
        public string Sign(Dictionary<string, string> systemParams, Dictionary<string, object> bizParams,
            Dictionary<string, string> textParams, string privateKey)
        {
            IDictionary<string, string> sortedMap = GetSortedMap(systemParams, bizParams, textParams);

            StringBuilder content = new StringBuilder();
            foreach (var pair in sortedMap)
            {
                if (!string.IsNullOrEmpty(pair.Key) && !string.IsNullOrEmpty(pair.Value))
                {
                    content.Append(pair.Key).Append("=").Append(pair.Value).Append("&");
                }
            }
            if (content.Length > 0)
            {
                //去除尾巴上的&
                content.Remove(content.Length - 1, 1);
            }

            return Signer.Sign(content.ToString(), privateKey);
        }

        private IDictionary<string, string> GetSortedMap(Dictionary<string, string> systemParams,
            Dictionary<string, object> bizParams, Dictionary<string, string> textParams)
        {
            AddOtherParams(textParams, bizParams);

            IDictionary<string, string> sortedMap = new SortedDictionary<string, string>(systemParams, StringComparer.Ordinal);
            if (bizParams != null && bizParams.Count != 0)
            {
                sortedMap.Add(AlipayConstants.BIZ_CONTENT_FIELD, JsonUtil.ToJsonString(bizParams));
            }

            if (textParams != null)
            {
                foreach (var pair in textParams)
                {
                    sortedMap.Add(pair.Key, pair.Value);
                }
            }

            SetNotifyUrl(sortedMap);

            return sortedMap;
        }

        private void SetNotifyUrl(IDictionary<string, string> paramters)
        {
            if (GetConfig(AlipayConstants.NOTIFY_URL_CONFIG_KEY) != null && !paramters.ContainsKey(AlipayConstants.NOTIFY_URL_FIELD))
            {
                paramters.Add(AlipayConstants.NOTIFY_URL_FIELD, GetConfig(AlipayConstants.NOTIFY_URL_CONFIG_KEY));
            }
        }

        /// <summary>
        /// 獲取商戶應用公鑰證書序列號，從證書模式執行時環境物件中直接讀取
        /// </summary>
        /// <returns>商戶應用公鑰證書序列號</returns>
        public string GetMerchantCertSN()
        {
            if (context.CertEnvironment == null)
            {
                return null;
            }

            return context.CertEnvironment.MerchantCertSN;
        }

        /// <summary>
        /// 獲取支付寶根證書序列號，從證書模式執行時環境物件中直接讀取
        /// </summary>
        /// <returns>支付寶根證書序列號</returns>
        public string GetAlipayRootCertSN()
        {
            if (context.CertEnvironment == null)
            {
                return null;
            }
            return context.CertEnvironment.RootCertSN;
        }

        /// <summary>
        /// 將業務引數和其他額外文字引數按www-form-urlencoded格式轉換成HTTP Body中的位元組陣列，注意要做URL Encode
        /// </summary>
        /// <param name="bizParams">業務引數</param>
        /// <returns>HTTP Body中的位元組陣列</returns>
        public byte[] ToUrlEncodedRequestBody(Dictionary<string, object> bizParams)
        {

            IDictionary<string, string> sortedMap = GetSortedMap(new Dictionary<string, string>(), bizParams, null);
            return AlipayConstants.DEFAULT_CHARSET.GetBytes(BuildQueryString(sortedMap));
        }

        private string BuildQueryString(IDictionary<string, string> sortedMap)
        {
            StringBuilder content = new StringBuilder();
            int index = 0;
            foreach (var pair in sortedMap)
            {
                if (!string.IsNullOrEmpty(pair.Key) && !string.IsNullOrEmpty(pair.Value))
                {
                    content.Append(index == 0 ? "" : "&")
                            .Append(pair.Key)
                            .Append("=")
                            .Append(HttpUtility.UrlEncode(pair.Value, AlipayConstants.DEFAULT_CHARSET));
                    index++;
                }
            }
            return content.ToString();
        }

        /// <summary>
        /// 生成隨機分界符，用於multipart格式的HTTP請求Body的多個欄位間的分隔
        /// </summary>
        /// <returns>隨機分界符</returns>
        public string GetRandomBoundary()
        {
            return DateTime.Now.Ticks.ToString("X");
        }

        /// <summary>
        /// 字串拼接
        /// </summary>
        /// <param name="a">字串a</param>
        /// <param name="b">字串b</param>
        /// <returns>字串a和b拼接後的字串</returns>
        public string ConcatStr(string a, string b)
        {
            return a + b;
        }

        /// <summary>
        /// 將其他額外文字引數和檔案引數按multipart/form-data格式轉換成HTTP Body中的位元組陣列流
        /// </summary>
        /// <param name="textParams">其他額外文字引數</param>
        /// <param name="fileParams">業務檔案引數</param>
        /// <param name="boundary">HTTP Body中multipart格式的分隔符</param>
        /// <returns>Multipart格式的位元組流</returns>
        public Stream ToMultipartRequestBody(Dictionary<string, string> textParams, Dictionary<string, string> fileParams, string boundary)
        {
            MemoryStream stream = new MemoryStream();

            //補充其他額外引數
            AddOtherParams(textParams, null);

            foreach (var pair in textParams)
            {
                if (!string.IsNullOrEmpty(pair.Key) && !string.IsNullOrEmpty(pair.Value))
                {
                    MultipartUtil.WriteToStream(stream, MultipartUtil.GetEntryBoundary(boundary));
                    MultipartUtil.WriteToStream(stream, MultipartUtil.GetTextEntry(pair.Key, pair.Value));
                }
            }

            //組裝檔案引數
            foreach (var pair in fileParams)
            {
                if (!string.IsNullOrEmpty(pair.Key) && pair.Value != null)
                {
                    MultipartUtil.WriteToStream(stream, MultipartUtil.GetEntryBoundary(boundary));
                    MultipartUtil.WriteToStream(stream, MultipartUtil.GetFileEntry(pair.Key, pair.Value));
                    MultipartUtil.WriteToStream(stream, File.ReadAllBytes(pair.Value));
                }
            }

            //新增結束標記
            MultipartUtil.WriteToStream(stream, MultipartUtil.GetEndBoundary(boundary));

            stream.Seek(0, SeekOrigin.Begin);
            return stream;
        }

        /// <summary>
        /// 將閘道器響應發序列化成Map，同時將API的介面名稱和響應原文插入到響應Map的method和body欄位中
        /// </summary>
        /// <param name="response">HTTP響應</param>
        /// <param name="method">呼叫的OpenAPI的介面名稱</param>
        /// <returns>響應反序列化的Map</returns>
        public Dictionary<string, object> ReadAsJson(TeaResponse response, string method)
        {
            string responseBody = TeaCore.GetResponseBody(response);
            Dictionary<string, object> dictionary = JsonConvert.DeserializeObject<Dictionary<string, object>>(responseBody);
            dictionary.Add(AlipayConstants.BODY_FIELD, responseBody);
            dictionary.Add(AlipayConstants.METHOD_FIELD, method);
            return DictionaryUtil.ObjToDictionary(dictionary);
        }

        /// <summary>
        /// 適配Tea DSL自動生成的程式碼
        /// </summary>
        public async Task<Dictionary<string, object>> ReadAsJsonAsync(TeaResponse response, string method)
        {
            return ReadAsJson(response, method);
        }

        /// <summary>
        /// 從響應Map中提取支付寶公鑰證書序列號
        /// </summary>
        /// <param name="respMap">響應Map</param>
        /// <returns>支付寶公鑰證書序列號</returns>
        public string GetAlipayCertSN(Dictionary<string, object> respMap)
        {
            return (string)respMap[AlipayConstants.ALIPAY_CERT_SN_FIELD];
        }

        /// <summary>
        /// 獲取支付寶公鑰，從證書執行時環境物件中直接讀取
        /// 如果快取的使用者指定的支付寶公鑰證書的序列號與閘道器響應中攜帶的支付寶公鑰證書序列號不一致，需要報錯給出提示或自動更新支付寶公鑰證書
        /// </summary>
        /// <param name="alipayCertSN">閘道器響應中攜帶的支付寶公鑰證書序列號</param>
        /// <returns>支付寶公鑰</returns>
        public string ExtractAlipayPublicKey(string alipayCertSN)
        {
            if (context.CertEnvironment == null)
            {
                return null;
            }
            return context.CertEnvironment.GetAlipayPublicKey(alipayCertSN);
        }

        /// <summary>
        /// 驗證簽名
        /// </summary>
        /// <param name="respMap">響應Map，可以從中提取出sign和body</param>
        /// <param name="alipayPublicKey">支付寶公鑰</param>
        /// <returns>true：驗籤透過；false：驗籤不透過</returns>
        public bool Verify(Dictionary<string, object> respMap, string alipayPublicKey)
        {
            string sign = (string)respMap[AlipayConstants.SIGN_FIELD];
            string content = SignContentExtractor.GetSignSourceData((string)respMap[AlipayConstants.BODY_FIELD],
                    (string)respMap[AlipayConstants.METHOD_FIELD]);
            return Signer.Verify(content, sign, alipayPublicKey);
        }

        /// <summary>
        /// 從響應Map中提取返回值物件的Map，並將響應原文插入到body欄位中
        /// </summary>
        /// <param name="respMap">響應Map</param>
        /// <returns>返回值物件Map</returns>
        public Dictionary<string, object> ToRespModel(Dictionary<string, object> respMap)
        {
            string methodName = (string)respMap[AlipayConstants.METHOD_FIELD];
            string responseNodeName = methodName.Replace('.', '_') + AlipayConstants.RESPONSE_SUFFIX;
            string errorNodeName = AlipayConstants.ERROR_RESPONSE;

            //先找正常響應節點
            foreach (var pair in respMap)
            {
                if (responseNodeName.Equals(pair.Key))
                {
                    Dictionary<string, object> model = (Dictionary<string, object>)pair.Value;
                    model.Add(AlipayConstants.BODY_FIELD, respMap[AlipayConstants.BODY_FIELD]);
                    return model;
                }
            }

            //再找異常響應節點
            foreach (var pair in respMap)
            {
                if (errorNodeName.Equals(pair.Key))
                {
                    Dictionary<string, object> model = (Dictionary<string, object>)pair.Value;
                    model.Add(AlipayConstants.BODY_FIELD, respMap[AlipayConstants.BODY_FIELD]);
                    return model;
                }
            }

            throw new Exception("響應格式不符合預期，找不到" + responseNodeName + "節點");
        }

        /// <summary>
        /// 生成頁面類請求所需URL或Form表單
        /// </summary>
        /// <param name="method">GET或POST，決定是生成URL還是Form表單</param>
        /// <param name="systemParams">系統引數集合</param>
        /// <param name="bizParams">業務引數集合</param>
        /// <param name="textParams">其他額外文字引數集合</param>
        /// <param name="sign">所有引數的簽名值</param>
        /// <returns>生成的URL字串或表單</returns>
        public string GeneratePage(string method, Dictionary<string, string> systemParams, Dictionary<string, object> bizParams,
            Dictionary<string, string> textParams, string sign)
        {
            if (AlipayConstants.GET.Equals(method))
            {
                //採集並排序所有引數
                IDictionary<string, string> sortedMap = GetSortedMap(systemParams, bizParams, textParams);
                sortedMap.Add(AlipayConstants.SIGN_FIELD, sign);

                //將所有引數置於URL中
                return GetGatewayServerUrl() + "?" + BuildQueryString(sortedMap);
            }
            else if (AlipayConstants.POST.Equals(method))
            {
                //將系統引數、額外文字引數排序後置於URL中
                IDictionary<string, string> urlParams = GetSortedMap(systemParams, null, textParams);
                urlParams.Add(AlipayConstants.SIGN_FIELD, sign);
                string actionUrl = GetGatewayServerUrl() + "?" + BuildQueryString(urlParams);

                //將業務引數排序後置於form表單中
                AddOtherParams(null, bizParams);
                IDictionary<string, string> formParams = new SortedDictionary<string, string>()
                {
                    { AlipayConstants.BIZ_CONTENT_FIELD, JsonUtil.ToJsonString(bizParams)}
                };
                return PageUtil.BuildForm(actionUrl, formParams);
            }
            else
            {
                throw new Exception("_generatePage中method只支援傳入GET或POST");
            }
        }

        /// <summary>
        /// 生成訂單串
        /// </summary>
        /// <param name="systemParams">系統引數集合</param>
        /// <param name="bizParams">業務引數集合</param>
        /// <param name="textParams">其他文字引數集合</param>
        /// <param name="sign">所有引數的簽名值</param>
        /// <returns>訂單串</returns>
        public string GenerateOrderString(Dictionary<string, string> systemParams, Dictionary<string, object> bizParams,
            Dictionary<string, string> textParams, string sign)
        {
            //採集並排序所有引數
            IDictionary<string, string> sortedMap = GetSortedMap(systemParams, bizParams, textParams);
            sortedMap.Add(AlipayConstants.SIGN_FIELD, sign);

            //將所有引數置於URL中
            return BuildQueryString(sortedMap);
        }

        private string GetGatewayServerUrl()
        {
            return GetConfig(AlipayConstants.PROTOCOL_CONFIG_KEY) + "://" + GetConfig(AlipayConstants.HOST_CONFIG_KEY) + "/gateway.do";
        }

        /// <summary>
        /// AES加密
        /// </summary>
        /// <param name="plainText">明文</param>
        /// <param name="key">金鑰</param>
        /// <returns>密文</returns>
        public string AesEncrypt(string plainText, string key)
        {
            return AES.Encrypt(plainText, key);
        }

        /// <summary>
        /// AES解密
        /// </summary>
        /// <param name="chiperText">密文</param>
        /// <param name="key">金鑰</param>
        /// <returns>明文</returns>
        public string AesDecrypt(string chiperText, string key)
        {
            return AES.Decrypt(chiperText, key);
        }

        /// <summary>
        /// 對支付類請求的非同步通知的引數集合進行驗籤
        /// </summary>
        /// <param name="parameters">引數集合</param>
        /// <param name="alipayPublicKey">支付寶公鑰</param>
        /// <returns>true：驗證成功；false：驗證失敗</returns>
        public bool VerifyParams(Dictionary<string, string> parameters, string alipayPublicKey)
        {
            return Signer.VerifyParams(parameters, alipayPublicKey);
        }

        /// <summary>
        /// 獲取SDK版本資訊
        /// </summary>
        /// <returns>SDK版本資訊</returns>
        public string GetSdkVersion()
        {
            return context.SdkVersion;
        }

        /// <summary>
        /// 將隨機順序的Map轉換為有序的Map
        /// </summary>
        /// <param name="input">隨機順序的Map</param>
        /// <returns>有序的Map</returns>
        public Dictionary<string, string> SortMap(Dictionary<string, string> input)
        {
            //GO語言的Map是隨機順序的，每次訪問順序都不同，才需排序
            return input;
        }

        private void AddOtherParams(Dictionary<string, string> textParams, Dictionary<string, object> bizParams)
        {
            //為null表示此處不是擴充套件此類引數的時機
            if (textParams != null)
            {
                foreach (var pair in optionalTextParams)
                {
                    if (!textParams.ContainsKey(pair.Key))
                    {
                        textParams.Add(pair.Key, pair.Value);
                    }
                }
                SetNotifyUrl(textParams);
            }

            //為null表示此處不是擴充套件此類引數的時機
            if (bizParams != null)
            {
                foreach (var pair in optionalBizParams)
                {
                    if (!bizParams.ContainsKey(pair.Key))
                    {
                        bizParams.Add(pair.Key, pair.Value);
                    }
                }
            }
        }
    }
}
