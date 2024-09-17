// This file is auto-generated, don't edit it. Thanks.

using System;
using System.Collections;
using System.Collections.Generic;
using System.IO;
using System.Threading.Tasks;

using Tea;
using Tea.Utils;


namespace Alipay.EasySDK.Util.AES
{
    public class Client 
    {
        protected Alipay.EasySDK.Kernel.Client _kernel;

        public Client(Alipay.EasySDK.Kernel.Client kernel)
        {
            this._kernel = kernel;
        }


        public string Decrypt(string cipherText)
        {
            return this._kernel.AesDecrypt(cipherText, this._kernel.GetConfig("encryptKey"));
        }

        public string Encrypt(string plainText)
        {
            return this._kernel.AesEncrypt(plainText, this._kernel.GetConfig("encryptKey"));
        }

        
        /// <summary>
        /// ISV代商戶代用，指定appAuthToken
        /// </summary>
        /// <param name="appAuthToken">代呼叫token</param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client Agent(string appAuthToken)
        {
            _kernel.InjectTextParam("app_auth_token", appAuthToken);
            return this;
        }

        /// <summary>
        /// 使用者授權呼叫，指定authToken
        /// </summary>
        /// <param name="authToken">使用者授權token</param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client Auth(string authToken)
        {
            _kernel.InjectTextParam("auth_token", authToken);
            return this;
        }

        /// <summary>
        /// 設定非同步通知回撥地址，此處設定將在本呼叫中覆蓋Config中的全域性配置
        /// </summary>
        /// <param name="url">非同步通知回撥地址，例如：https://www.test.com/callback </param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client AsyncNotify(string url)
        {
            _kernel.InjectTextParam("notify_url", url);
            return this;
        }

        /// <summary>
        /// 將本次呼叫強制路由到後端系統的測試地址上，常用於線下環境內外聯調，沙箱與線上環境設定無效
        /// </summary>
        /// <param name="testUrl">後端系統測試地址</param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client Route(string testUrl)
        {
            _kernel.InjectTextParam("ws_service_url", testUrl);
            return this;
        }

        /// <summary>
        /// 設定API入參中沒有的其他可選業務請求引數(biz_content下的欄位)
        /// </summary>
        /// <param name="key">業務請求引數名稱（biz_content下的欄位名，比如timeout_express）</param>
        /// <param name="value">
        /// 業務請求引數的值，一個可以序列化成JSON的物件
        /// 如果該欄位是一個字串型別（String、Price、Date在SDK中都是字串），請使用string儲存
        /// 如果該欄位是一個數值型型別（比如：Number），請使用long儲存
        /// 如果該欄位是一個複雜型別，請使用巢狀的Dictionary指定各下級欄位的值
        /// 如果該欄位是一個數組，請使用List儲存各個值
        /// 對於更復雜的情況，也支援Dictionary和List的各種組合巢狀，比如引數是值是個List，List中的每種型別是一個複雜物件
        /// </param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client Optional(string key, object value)
        {
            _kernel.InjectBizParam(key, value);
            return this;
        }

        /// <summary>
        /// 批次設定API入參中沒有的其他可選業務請求引數(biz_content下的欄位)
        /// optional方法的批次版本
        /// </summary>
        /// <param name="optionalArgs">可選引數集合，每個引數由key和value組成，key和value的格式請參見optional方法的註釋</param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client BatchOptional(Dictionary<string, object> optionalArgs)
        {
            foreach (var pair in optionalArgs)
            {
                _kernel.InjectBizParam(pair.Key, pair.Value);
            }
            return this;
        }
    }
}