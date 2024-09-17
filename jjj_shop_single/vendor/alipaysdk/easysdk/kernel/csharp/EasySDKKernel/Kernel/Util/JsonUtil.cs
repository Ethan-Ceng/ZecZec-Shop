using System.Collections.Generic;
using Tea;
using Newtonsoft.Json;

namespace Alipay.EasySDK.Kernel.Util
{
    /// <summary>
    /// JSON工具類
    /// </summary>
    public class JsonUtil
    {
        /// <summary>
        /// 將字典集合轉換為Json字串，轉換過程中對於TeaModel，使用標註的欄位名稱而不是欄位的變數名
        /// </summary>
        /// <param name="input">字典集合</param>
        /// <returns>Json字串</returns>
        public static string ToJsonString(IDictionary<string, object> input)
        {
            IDictionary<string, object> result = new Dictionary<string, object>();
            foreach (var pair in input)
            {
                if (pair.Value is TeaModel)
                {
                    result.Add(pair.Key, GetTeaModelMap((TeaModel)pair.Value));
                }
                else
                {
                    result.Add(pair.Key, pair.Value);
                }
            }
            return JsonConvert.SerializeObject(result);
        }

        private static IDictionary<string, object> GetTeaModelMap(TeaModel teaModel)
        {

            IDictionary<string, object> result = new Dictionary<string, object>();
            IDictionary<string, object> teaModelMap = teaModel.ToMap();
            foreach (var pair in teaModelMap)
            {
                if (pair.Value is TeaModel)
                {
                    result.Add(pair.Key, GetTeaModelMap((TeaModel)pair.Value));
                }
                else
                {
                    result.Add(pair.Key, pair.Value);
                }
            }
            return result;
        }
    }
}
