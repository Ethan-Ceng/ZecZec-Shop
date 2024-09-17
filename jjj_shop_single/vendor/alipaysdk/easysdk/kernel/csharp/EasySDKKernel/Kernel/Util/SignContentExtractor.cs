using System;
using System.Collections.Generic;

namespace Alipay.EasySDK.Kernel.Util
{
    /// <summary>
    /// 待驗籤原文提取器
    /// 注：此處不可使用JSON反序列化工具進行提取，會破壞原有格式，對於簽名而言差個空格都會驗籤不透過
    /// </summary>
    public class SignContentExtractor
    {
        /// <summary>
        /// 左大括號
        /// </summary>
        public const char LEFT_BRACE = '{';

        /// <summary>
        /// 右大括號
        /// </summary>
        public const char RIGHT_BRACE = '}';

        /// <summary>
        /// 雙引號
        /// </summary>
        public const char DOUBLE_QUOTES = '"';

        /// <summary>
        /// 獲取待驗籤的原文
        /// </summary>
        /// <param name="body">閘道器的整體響應字串</param>
        /// <param name="method">本次呼叫的OpenAPI介面名稱</param>
        /// <returns>待驗籤的原文</returns>
        public static string GetSignSourceData(string body, string method)
        {
            string rootNode = method.Replace(".", "_") + AlipayConstants.RESPONSE_SUFFIX;
            string errorRootNode = AlipayConstants.ERROR_RESPONSE;

            int indexOfRootNode = body.IndexOf(rootNode, StringComparison.Ordinal);
            int indexOfErrorRoot = body.IndexOf(errorRootNode, StringComparison.Ordinal);

            string result = null;
            if (indexOfRootNode > 0)
            {
                result = ParseSignSourceData(body, rootNode, indexOfRootNode);
            }
            else if (indexOfErrorRoot > 0)
            {
                result = ParseSignSourceData(body, errorRootNode, indexOfErrorRoot);
            }

            return result;
        }

        private static string ParseSignSourceData(string body, string rootNode, int indexOfRootNode)
        {
            int signDataStartIndex = indexOfRootNode + rootNode.Length + 2;
            int indexOfSign = body.IndexOf("\"" + AlipayConstants.SIGN_FIELD + "\"", StringComparison.Ordinal);
            if (indexOfSign < 0)
            {
                return null;
            }
            SignSourceData signSourceData = ExtractSignContent(body, signDataStartIndex);

            //如果提取的待驗籤原始內容後還有rootNode
            if (body.LastIndexOf(rootNode, StringComparison.Ordinal) > signSourceData.EndIndex)
            {
                throw new Exception("檢測到響應報文中有重複的" + rootNode + "，驗籤失敗。");
            }

            return signSourceData.SourceData;
        }

        private static SignSourceData ExtractSignContent(string str, int begin)
        {
            if (str == null)
            {
                return null;
            }

            int beginIndex = ExtractBeginPosition(str, begin);
            if (beginIndex >= str.Length)
            {
                return null;
            }

            int endIndex = ExtractEndPosition(str, beginIndex);
            return new SignSourceData()
            {
                SourceData = str.Substring(beginIndex, endIndex - beginIndex),
                BeginIndex = beginIndex,
                EndIndex = endIndex
            };
        }

        private static int ExtractBeginPosition(string responseString, int begin)
        {
            int beginPosition = begin;
            //找到第一個左大括號（對應響應的是JSON物件的情況：普通呼叫OpenAPI響應明文）
            //或者雙引號（對應響應的是JSON字串的情況：加密呼叫OpenAPI響應Base64串），作為待驗籤內容的起點
            while (beginPosition < responseString.Length
                    && responseString[beginPosition] != LEFT_BRACE
                    && responseString[beginPosition] != DOUBLE_QUOTES)
            {
                ++beginPosition;
            }
            return beginPosition;
        }

        private static int ExtractEndPosition(string responseString, int beginPosition)
        {
            //提取明文驗籤內容終點
            if (responseString[beginPosition] == LEFT_BRACE)
            {
                return ExtractJsonObjectEndPosition(responseString, beginPosition);
            }
            //提取密文驗籤內容終點
            else
            {
                return ExtractJsonBase64ValueEndPosition(responseString, beginPosition);
            }
        }

        private static int ExtractJsonBase64ValueEndPosition(string responseString, int beginPosition)
        {
            for (int index = beginPosition; index < responseString.Length; ++index)
            {
                //找到第2個雙引號作為終點，由於中間全部是Base64編碼的密文，所以不會有干擾的特殊字元
                if (responseString[index] == DOUBLE_QUOTES && index != beginPosition)
                {
                    return index + 1;
                }
            }
            //如果沒有找到第2個雙引號，說明驗籤內容片段提取失敗，直接嘗試選取剩餘整個響應字串進行驗籤
            return responseString.Length;
        }

        private static int ExtractJsonObjectEndPosition(string responseString, int beginPosition)
        {
            //記錄當前尚未發現配對閉合的大括號
            LinkedList<char> braces = new LinkedList<char>();
            //記錄當前字元是否在雙引號中
            bool inQuotes = false;
            //記錄當前字元前面連續的跳脫字元個數
            int consecutiveEscapeCount = 0;
            //從待驗簽字符的起點開始遍歷後續字串，找出待驗簽字符串的終止點，終點即是與起點{配對的}
            for (int index = beginPosition; index < responseString.Length; ++index)
            {
                //提取當前字元
                char currentChar = responseString[index];

                //如果當前字元是"且前面有偶數個轉義標記（0也是偶數）
                if (currentChar == DOUBLE_QUOTES && consecutiveEscapeCount % 2 == 0)
                {
                    //是否在引號中的狀態取反
                    inQuotes = !inQuotes;
                }
                //如果當前字元是{且不在引號中
                else if (currentChar == LEFT_BRACE && !inQuotes)
                {
                    //將該{加入未閉合括號中
                    braces.AddLast(LEFT_BRACE);
                }
                //如果當前字元是}且不在引號中
                else if (currentChar == RIGHT_BRACE && !inQuotes)
                {
                    //彈出一個未閉合括號
                    braces.RemoveLast();
                    //如果彈出後，未閉合括號為空，說明已經找到終點
                    if (braces.Count == 0)
                    {
                        return index + 1;
                    }
                }

                //如果當前字元是跳脫字元
                if (currentChar == '\\')
                {
                    //連續跳脫字元個數+1
                    ++consecutiveEscapeCount;
                }
                else
                {
                    //連續跳脫字元個數置0
                    consecutiveEscapeCount = 0;
                }
            }

            //如果沒有找到配對的閉合括號，說明驗籤內容片段提取失敗，直接嘗試選取剩餘整個響應字串進行驗籤
            return responseString.Length;
        }

        /// <summary>
        /// 從響應字串中提取到的待驗籤原始內容
        /// </summary>
        public class SignSourceData
        {
            /// <summary>
            /// 待驗籤原始內容
            /// </summary>
            public string SourceData { get; set; }

            /// <summary>
            /// 待驗籤原始內容在響應字串中的起始位置
            /// </summary>
            public int BeginIndex { get; set; }

            /// <summary>
            /// 待驗籤原始內容在響應字串中的結束位置
            /// </summary>
            public int EndIndex { get; set; }
        }
    }
}
