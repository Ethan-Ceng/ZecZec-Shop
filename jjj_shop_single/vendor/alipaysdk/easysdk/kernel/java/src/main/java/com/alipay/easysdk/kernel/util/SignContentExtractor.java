/**
 * Alipay.com Inc. Copyright (c) 2004-2019 All Rights Reserved.
 */
package com.alipay.easysdk.kernel.util;

import com.alipay.easysdk.kernel.AlipayConstants;

import java.util.LinkedList;

/**
 * 待驗籤原文提取器
 * <p>
 * 注：此處不可使用JSON反序列化工具進行提取，會破壞原有格式，對於簽名而言差個空格都會驗籤不透過
 *
 * @author zhongyu
 * @version $Id: SignContentExtractor.java, v 0.1 2019年12月19日 9:07 PM zhongyu Exp $
 */
public class SignContentExtractor {
    /**
     * 左大括號
     */
    public static final char LEFT_BRACE = '{';

    /**
     * 右大括號
     */
    public static final char RIGHT_BRACE = '}';

    /**
     * 雙引號
     */
    public static final char DOUBLE_QUOTES = '"';

    /**
     * 獲取待驗籤的原文
     *
     * @param body   閘道器的整體響應字串
     * @param method 本次呼叫的OpenAPI介面名稱
     * @return 待驗籤的原文
     */
    public static String getSignSourceData(String body, String method) {
        // 加簽源串起點
        String rootNode = method.replace('.', '_') + AlipayConstants.RESPONSE_SUFFIX;
        String errorRootNode = AlipayConstants.ERROR_RESPONSE;

        int indexOfRootNode = body.indexOf(rootNode);
        int indexOfErrorRoot = body.indexOf(errorRootNode);

        if (indexOfRootNode > 0) {
            return parseSignSourceData(body, rootNode, indexOfRootNode);
        } else if (indexOfErrorRoot > 0) {
            return parseSignSourceData(body, errorRootNode, indexOfErrorRoot);
        } else {
            return null;
        }
    }

    private static String parseSignSourceData(String body, String rootNode, int indexOfRootNode) {
        //第一個字母 + 長度 + 冒號 + 引號
        int signDataStartIndex = indexOfRootNode + rootNode.length() + 2;

        int indexOfSign = body.indexOf("\"" + AlipayConstants.SIGN_FIELD + "\"");
        if (indexOfSign < 0) {
            return null;
        }

        SignSourceData signSourceData = extractSignContent(body, signDataStartIndex);

        //如果提取的待驗籤原始內容後還有rootNode
        if (body.lastIndexOf(rootNode) > signSourceData.getEndIndex()) {
            throw new RuntimeException("檢測到響應報文中有重複的" + rootNode + "，驗籤失敗。");
        }

        return signSourceData.getSourceData();
    }

    private static SignSourceData extractSignContent(String str, int begin) {
        if (str == null) {
            return null;
        }

        int beginIndex = extractBeginPosition(str, begin);
        if (beginIndex >= str.length()) {
            return null;
        }

        int endIndex = extractEndPosition(str, beginIndex);
        return new SignSourceData(str.substring(beginIndex, endIndex), beginIndex, endIndex);
    }

    private static int extractBeginPosition(String responseString, int begin) {
        int beginPosition = begin;
        //找到第一個左大括號（對應響應的是JSON物件的情況：普通呼叫OpenAPI響應明文）
        //或者雙引號（對應響應的是JSON字串的情況：加密呼叫OpenAPI響應Base64串），作為待驗籤內容的起點
        while (beginPosition < responseString.length()
                && responseString.charAt(beginPosition) != LEFT_BRACE
                && responseString.charAt(beginPosition) != DOUBLE_QUOTES) {
            ++beginPosition;
        }
        return beginPosition;
    }

    private static int extractEndPosition(String responseString, int beginPosition) {
        //提取明文驗籤內容終點
        if (responseString.charAt(beginPosition) == LEFT_BRACE) {
            return extractJsonObjectEndPosition(responseString, beginPosition);
        }
        //提取密文驗籤內容終點
        else {
            return extractJsonBase64ValueEndPosition(responseString, beginPosition);
        }
    }

    private static int extractJsonBase64ValueEndPosition(String responseString, int beginPosition) {
        for (int index = beginPosition; index < responseString.length(); ++index) {
            //找到第2個雙引號作為終點，由於中間全部是Base64編碼的密文，所以不會有干擾的特殊字元
            if (responseString.charAt(index) == DOUBLE_QUOTES && index != beginPosition) {
                return index + 1;
            }
        }
        //如果沒有找到第2個雙引號，說明驗籤內容片段提取失敗，直接嘗試選取剩餘整個響應字串進行驗籤
        return responseString.length();
    }

    private static int extractJsonObjectEndPosition(String responseString, int beginPosition) {
        //記錄當前尚未發現配對閉合的大括號
        LinkedList<Character> braces = new LinkedList<Character>();
        //記錄當前字元是否在雙引號中
        boolean inQuotes = false;
        //記錄當前字元前面連續的跳脫字元個數
        int consecutiveEscapeCount = 0;
        //從待驗簽字符的起點開始遍歷後續字串，找出待驗簽字符串的終止點，終點即是與起點{配對的}
        for (int index = beginPosition; index < responseString.length(); ++index) {
            //提取當前字元
            char currentChar = responseString.charAt(index);

            //如果當前字元是"且前面有偶數個轉義標記（0也是偶數）
            if (currentChar == DOUBLE_QUOTES && consecutiveEscapeCount % 2 == 0) {
                //是否在引號中的狀態取反
                inQuotes = !inQuotes;
            }
            //如果當前字元是{且不在引號中
            else if (currentChar == LEFT_BRACE && !inQuotes) {
                //將該{加入未閉合括號中
                braces.push(LEFT_BRACE);
            }
            //如果當前字元是}且不在引號中
            else if (currentChar == RIGHT_BRACE && !inQuotes) {
                //彈出一個未閉合括號
                braces.pop();
                //如果彈出後，未閉合括號為空，說明已經找到終點
                if (braces.isEmpty()) {
                    return index + 1;
                }
            }

            //如果當前字元是跳脫字元
            if (currentChar == '\\') {
                //連續跳脫字元個數+1
                ++consecutiveEscapeCount;
            } else {
                //連續跳脫字元個數置0
                consecutiveEscapeCount = 0;
            }
        }

        //如果沒有找到配對的閉合括號，說明驗籤內容片段提取失敗，直接嘗試選取剩餘整個響應字串進行驗籤
        return responseString.length();
    }

    private static class SignSourceData {
        /**
         * 待驗籤原始內容
         */
        private final String sourceData;
        /**
         * 待驗籤原始內容在響應字串中的起始位置
         */
        private final int    beginIndex;
        /**
         * 待驗籤原始內容在響應字串中的結束位置
         */
        private final int    endIndex;

        SignSourceData(String sourceData, int beginIndex, int endIndex) {
            this.sourceData = sourceData;
            this.beginIndex = beginIndex;
            this.endIndex = endIndex;
        }

        String getSourceData() {
            return sourceData;
        }

        public int getBeginIndex() {
            return beginIndex;
        }

        int getEndIndex() {
            return endIndex;
        }
    }
}