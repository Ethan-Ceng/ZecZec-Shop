/**
 * Alipay.com Inc.
 * Copyright (c) 2004-2020 All Rights Reserved.
 */
package com.alipay.easysdk.kernel.util;

import com.aliyun.tea.TeaModel;
import com.google.common.base.Strings;

import java.lang.reflect.Field;

/**
 * 響應檢查工具類
 *
 * @author zhongyu
 * @version : ResponseChecker.java, v 0.1 2020年06月02日 10:42 上午 zhongyu Exp $
 */
public class ResponseChecker {

    public static final String SUB_CODE_FIELD_NAME = "subCode";

    /**
     * 判斷一個請求返回的響應是否成功
     *
     * @param response 響應物件
     * @return true：成功；false：失敗
     */
    public static boolean success(TeaModel response) {
        try {
            Field subCodeField = response.getClass().getField(SUB_CODE_FIELD_NAME);
            subCodeField.setAccessible(true);
            String subCode = (String) subCodeField.get(response);
            return Strings.isNullOrEmpty(subCode);
        } catch (NoSuchFieldException | IllegalAccessException e) {
            //沒有subCode欄位的響應物件，通常是那些無需跟閘道器遠端通訊的API，只要本地執行完成都視為成功
            return true;
        }
    }
}