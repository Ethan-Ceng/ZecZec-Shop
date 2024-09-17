using System;
using System.Text;
using System.IO;

namespace Alipay.EasySDK.Kernel.Util
{
    /// <summary>
    /// HTTP multipart/form-data格式相關工具類
    /// </summary>
    public static class MultipartUtil
    {
        /// <summary>
        /// 獲取Multipart分界符
        /// </summary>
        /// <param name="boundary">用作分界的隨機字串</param>
        /// <returns>Multipart分界符</returns>
        public static byte[] GetEntryBoundary(string boundary)
        {
            return Encoding.UTF8.GetBytes("\r\n--" + boundary + "\r\n");
        }

        /// <summary>
        /// 獲取Multipart結束標記
        /// </summary>
        /// <param name="boundary">用作分界的隨機字串</param>
        /// <returns>Multipart結束標記</returns>
        public static byte[] GetEndBoundary(string boundary)
        {
            return Encoding.UTF8.GetBytes("\r\n--" + boundary + "--\r\n");
        }

        /// <summary>
        /// 獲取Multipart中的文字引數結構
        /// </summary>
        /// <param name="fieldName">欄位名稱</param>
        /// <param name="fieldValue">欄位值</param>
        /// <returns>文字引數結構</returns>
        public static byte[] GetTextEntry(string fieldName, string fieldValue)
        {
            string entry = "Content-Disposition:form-data;name=\""
                    + fieldName
                    + "\"\r\nContent-Type:text/plain\r\n\r\n"
                    + fieldValue;
            return AlipayConstants.DEFAULT_CHARSET.GetBytes(entry);
        }

        /// <summary>
        /// 獲取Multipart中的檔案引數結構（不含檔案內容，只有檔案元資料）
        /// </summary>
        /// <param name="fieldName">欄位名稱</param>
        /// <param name="filePath">檔案路徑</param>
        /// <returns>檔案引數結構（不含檔案內容）</returns>
        public static byte[] GetFileEntry(String fieldName, String filePath)
        {
            ArgumentValidator.CheckArgument(File.Exists(filePath),
                Path.GetFullPath(filePath) + "檔案不存在");
            ArgumentValidator.CheckArgument(Path.GetFileName(filePath).Contains("."),
                "檔名必須帶上正確的副檔名");

            String entry = "Content-Disposition:form-data;name=\""
                    + fieldName
                    + "\";filename=\""
                    + Path.GetFileName(filePath)
                    + "\"\r\nContent-Type:application/octet-stream"
                    + "\r\n\r\n";
            return AlipayConstants.DEFAULT_CHARSET.GetBytes(entry);
        }

        /// <summary>
        /// 往指定流中寫入整個位元組陣列
        /// </summary>
        /// <param name="stream">流</param>
        /// <param name="content">位元組陣列</param>
        public static void WriteToStream(Stream stream, byte[] content)
        {
            stream.Write(content, 0, content.Length);
        }
    }
}
