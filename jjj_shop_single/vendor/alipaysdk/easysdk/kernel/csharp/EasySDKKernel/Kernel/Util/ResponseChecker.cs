using System.Reflection;
using Tea;

namespace Alipay.EasySDK.Kernel.Util
{
    /// <summary>
    /// 響應檢查工具類
    /// </summary>
    public class ResponseChecker
    {
        public const string SUB_CODE_FIELD_NAME = "SubCode";

        /// <summary>
        /// 判斷一個請求返回的響應是否成功
        /// </summary>
        /// <param name="response">響應物件</param>
        /// <returns>true：成功；false：失敗</returns>
        public static bool Success(TeaModel response)
        {
            PropertyInfo propertyInfo = response.GetType().GetProperty(SUB_CODE_FIELD_NAME);
            if (propertyInfo == null)
            {
                //沒有SubCode屬性的響應物件，通常是那些無需跟閘道器遠端通訊的API，只要本地執行完成都視為成功
                return true;
            }

            string subCode = (string)propertyInfo.GetValue(response);
            return string.IsNullOrEmpty(subCode);
        }
    }
}
