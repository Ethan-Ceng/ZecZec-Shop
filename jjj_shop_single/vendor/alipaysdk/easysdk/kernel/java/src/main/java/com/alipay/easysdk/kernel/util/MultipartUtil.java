/**
 * Alipay.com Inc.
 * Copyright (c) 2004-2020 All Rights Reserved.
 */
package com.alipay.easysdk.kernel.util;

import com.alipay.easysdk.kernel.AlipayConstants;
import com.google.common.base.Preconditions;

import java.io.File;

/**
 * HTTP multipart/form-data格式相關工具類
 *
 * @author zhongyu
 * @version : MulitpartUtil.java, v 0.1 2020年02月08日 11:26 上午 zhongyu Exp $
 */
public class MultipartUtil {
    /**
     * 獲取Multipart分界符
     *
     * @param boundary 用作分界的隨機字串
     * @return Multipart分界符
     */
    public static byte[] getEntryBoundary(String boundary) {
        return ("\r\n--" + boundary + "\r\n").getBytes();
    }

    /**
     * 獲取Multipart結束標記
     *
     * @param boundary 用作分界的隨機字串
     * @return Multipart結束標記
     */
    public static byte[] getEndBoundary(String boundary) {
        return ("\r\n--" + boundary + "--\r\n").getBytes();
    }

    /**
     * 獲取Multipart中的文字引數結構
     *
     * @param fieldName  欄位名稱
     * @param fieldValue 欄位值
     * @return 文字引數結構
     */
    public static byte[] getTextEntry(String fieldName, String fieldValue) {
        String entry = "Content-Disposition:form-data;name=\""
                + fieldName
                + "\"\r\nContent-Type:text/plain\r\n\r\n"
                + fieldValue;
        return entry.getBytes(AlipayConstants.DEFAULT_CHARSET);
    }

    /**
     * 獲取Multipart中的檔案引數結構（不含檔案內容，只有檔案元資料）
     *
     * @param fieldName 欄位名稱
     * @param filePath  檔案路徑
     * @return 檔案引數結構（不含檔案內容）
     */
    public static byte[] getFileEntry(String fieldName, String filePath) {
        String entry = "Content-Disposition:form-data;name=\""
                + fieldName
                + "\";filename=\""
                + getFile(filePath).getName()
                + "\"\r\nContent-Type:application/octet-stream"
                + "\r\n\r\n";
        return entry.getBytes(AlipayConstants.DEFAULT_CHARSET);
    }

    private static File getFile(String filePath) {
        File file = new File(filePath);
        Preconditions.checkArgument(file.exists(), file.getAbsolutePath() + "檔案不存在");
        Preconditions.checkArgument(file.getName().contains("."), "檔名必須帶上正確的副檔名");
        return file;
    }
}