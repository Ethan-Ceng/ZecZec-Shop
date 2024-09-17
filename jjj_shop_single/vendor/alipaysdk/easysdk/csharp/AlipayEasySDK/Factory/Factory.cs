using System;
using Alipay.EasySDK.Kernel;
using System.Reflection;

namespace Alipay.EasySDK.Factory
{
    /// <summary>
    /// 客戶端工廠，用於快速配置和訪問各種場景下的API Client
    ///
    /// 注：該Factory獲取的Client不可儲存重複使用，請每次均透過Factory完成呼叫
    /// </summary>
    public static class Factory
    {
        public const string SDK_VERSION = "alipay-easysdk-net-2.1.0";

        /// <summary>
        /// 將一些初始化耗時較多的資訊快取在上下文中
        /// </summary>
        private static Context context;

        /// <summary>
        /// 設定客戶端引數，只需設定一次，即可反覆使用各種場景下的API Client
        /// </summary>
        /// <param name="options">客戶端引數物件</param>
        public static void SetOptions(Config options)
        {
            context = new Context(options, SDK_VERSION);
        }

        /// <summary>
        /// 獲取呼叫OpenAPI所需的客戶端例項
        /// 本方法用於呼叫SDK擴充套件包中的API Client下的方法
        /// 
        /// 注：返回的例項不可重複使用，只可用於單次呼叫
        /// </summary>
        /// <typeparam name="T">泛型引數</typeparam>
        /// <param name="client">API Client的型別物件</param>
        /// <returns>client例項，用於發起單次呼叫</returns>
        public static T GetClient<T>()
        {
            Type type = typeof(T);
            ConstructorInfo constructor = type.GetConstructor(new Type[] { typeof(Client) });
            context.SdkVersion = GetSdkVersion(type);
            return (T)constructor.Invoke(new object[] { new Client(context) });
        }

        private static string GetSdkVersion(Type client)
        {
            return context.SdkVersion + "-" + client.FullName
                    .Replace("EasySDK.", "")
                    .Replace(".Client", "")
                    .Replace(".", "-");
        }

        /// <summary>
        /// 基礎能力相關
        /// </summary>
        public static class Base
        {
            /// <summary>
            /// 獲取圖片相關API Client
            /// </summary>
            /// <returns>圖片相關API Client</returns>
            public static EasySDK.Base.Image.Client Image()
            {
                return new EasySDK.Base.Image.Client(new Client(context));
            }

            /// <summary>
            /// 獲取影片相關API Client
            /// </summary>
            /// <returns>影片相關API Client</returns>
            public static EasySDK.Base.Video.Client Video()
            {
                return new EasySDK.Base.Video.Client(new Client(context));
            }

            /// <summary>
            /// 獲取OAuth認證相關API Client
            /// </summary>
            /// <returns>OAuth認證相關API Client</returns>
            public static EasySDK.Base.OAuth.Client OAuth()
            {
                return new EasySDK.Base.OAuth.Client(new Client(context));
            }

            /// <summary>
            /// 獲取小程式二維碼相關API Client
            /// </summary>
            /// <returns>小程式二維碼相關API Client</returns>
            public static EasySDK.Base.Qrcode.Client Qrcode()
            {
                return new EasySDK.Base.Qrcode.Client(new Client(context));
            }
        }

        /// <summary>
        /// 營銷能力相關
        /// </summary>
        public static class Marketing
        {
            /// <summary>
            /// 獲取生活號相關API Client
            /// </summary>
            /// <returns>生活號相關API Client</returns>
            public static EasySDK.Marketing.OpenLife.Client OpenLife()
            {
                return new EasySDK.Marketing.OpenLife.Client(new Client(context));
            }

            /// <summary>
            /// 獲取支付寶卡包相關API Client
            /// </summary>
            /// <returns>支付寶卡包相關API Client</returns>
            public static EasySDK.Marketing.Pass.Client Pass()
            {
                return new EasySDK.Marketing.Pass.Client(new Client(context));
            }

            /// <summary>
            /// 獲取小程式模板訊息相關API Client
            /// </summary>
            /// <returns>小程式模板訊息相關API Client</returns>
            public static EasySDK.Marketing.TemplateMessage.Client TemplateMessage()
            {
                return new EasySDK.Marketing.TemplateMessage.Client(new Client(context));
            }
        }

        /// <summary>
        /// 會員能力相關
        /// </summary>
        public static class Member
        {
            /// <summary>
            /// 獲取支付寶身份認證相關API Client
            /// </summary>
            /// <returns>支付寶身份認證相關API Client</returns>
            public static EasySDK.Member.Identification.Client Identification()
            {
                return new EasySDK.Member.Identification.Client(new Client(context));
            }
        }

        /// <summary>
        /// 支付能力相關
        /// </summary>
        public static class Payment
        {
            /// <summary>
            /// 獲取支付通用API Client
            /// </summary>
            /// <returns>支付通用API Client</returns>
            public static EasySDK.Payment.Common.Client Common()
            {
                return new EasySDK.Payment.Common.Client(new Client(context));
            }

            /// <summary>
            /// 獲取當面付API Client
            /// </summary>
            /// <returns>當面付API Client</returns>
            public static EasySDK.Payment.FaceToFace.Client FaceToFace()
            {
                return new EasySDK.Payment.FaceToFace.Client(new Client(context));
            }

            /// <summary>
            /// 獲取花唄API Client
            /// </summary>
            /// <returns>花唄API Client</returns>
            public static EasySDK.Payment.Huabei.Client Huabei()
            {
                return new EasySDK.Payment.Huabei.Client(new Client(context));
            }

            /// <summary>
            /// 獲取手機APP支付API Client
            /// </summary>
            /// <returns>手機APP支付API Client</returns>
            public static EasySDK.Payment.App.Client App()
            {
                return new EasySDK.Payment.App.Client(new Client(context));
            }

            /// <summary>
            /// 獲取電腦網站支付API Client
            /// </summary>
            /// <returns>電腦網站支付API</returns>
            public static EasySDK.Payment.Page.Client Page()
            {
                return new EasySDK.Payment.Page.Client(new Client(context));
            }

            /// <summary>
            /// 獲取手機網站支付API Client
            /// </summary>
            /// <returns>手機網站支付API</returns>
            public static EasySDK.Payment.Wap.Client Wap()
            {
                return new EasySDK.Payment.Wap.Client(new Client(context));
            }
        }

        /// <summary>
        /// 安全能力相關
        /// </summary>
        public static class Security
        {
            /// <summary>
            /// 獲取文字風險識別相關API Client
            /// </summary>
            /// <returns>文字風險識別相關API Client</returns>
            public static EasySDK.Security.TextRisk.Client TextRisk()
            {
                return new EasySDK.Security.TextRisk.Client(new Client(context));
            }
        }

        /// <summary>
        /// 輔助工具
        /// </summary>
        public static class Util
        {
            /// <summary>
            /// 獲取OpenAPI通用介面，可透過自行拼裝引數，呼叫幾乎所有OpenAPI
            /// </summary>
            /// <returns>OpenAPI通用介面</returns>
            public static EasySDK.Util.Generic.Client Generic()
            {
                return new EasySDK.Util.Generic.Client(new Client(context));
            }

            /// <summary>
            /// 獲取AES128加解密相關API Client，常用於會員手機號的解密
            /// </summary>
            /// <returns>AES128加解密相關API Client</returns>
            public static EasySDK.Util.AES.Client AES()
            {
                return new EasySDK.Util.AES.Client(new Client(context));
            }
        }
    }
}
