﻿using System.Collections.Generic;
using Newtonsoft.Json.Linq;

namespace Alipay.EasySDK.Kernel.Util
{
    /// <summary>
    /// 字典工具類
    /// </summary>
    public static class DictionaryUtil
    {
        /// <summary>
        /// 將字典各層次Value中的JObject和JArray轉換成C#標準庫中的Dictionary和List
        /// </summary>
        /// <param name="iputObj">輸入字典</param>
        /// <returns>轉換後的輸出字典</returns>
        public static Dictionary<string, object> ObjToDictionary(Dictionary<string, object> iputObj)
        {
            Dictionary<string, object> result = new Dictionary<string, object>();
            foreach (string key in iputObj.Keys)
            {
                if (iputObj[key] is JArray)
                {
                    List<object> objList = ((JArray)iputObj[key]).ToObject<List<object>>();
                    result.Add(key, ConvertList(objList));
                }
                else if (iputObj[key] is JObject)
                {
                    Dictionary<string, object> dicObj = ((JObject)iputObj[key]).ToObject<Dictionary<string, object>>();
                    result.Add(key, ObjToDictionary(dicObj));
                }
                else
                {
                    result.Add(key, iputObj[key]);
                }
            }
            return result;
        }


        private static List<object> ConvertList(List<object> inputList)
        {
            List<object> result = new List<object>();
            foreach (var obj in inputList)
            {
                if (obj is JArray)
                {
                    List<object> listObj = ((JArray)obj).ToObject<List<object>>();
                    result.Add(ConvertList(listObj));
                }
                else if (obj is JObject)
                {
                    Dictionary<string, object> dicObj = ((JObject)obj).ToObject<Dictionary<string, object>>();
                    result.Add(ObjToDictionary(dicObj));
                }
                else
                {
                    result.Add(obj);
                }
            }
            return result;
        }
    }
}
