/**
 * Alipay.com Inc. Copyright (c) 2004-2020 All Rights Reserved.
 */
package com.alipay.easysdk.kernel;

import java.nio.charset.Charset;
import java.nio.charset.StandardCharsets;

/**
 * 支付寶開放平臺閘道器互動常用常量
 *
 * @author zhongyu
 * @version $Id: AlipayConstants.java, v 0.1 2020年01月02日 7:53 PM zhongyu Exp $
 */
public final class AlipayConstants {
    /**
     * Config配置引數Key值
     */
    public static final String PROTOCOL_CONFIG_KEY              = "protocol";
    public static final String HOST_CONFIG_KEY                  = "gatewayHost";
    public static final String ALIPAY_CERT_PATH_CONFIG_KEY      = "alipayCertPath";
    public static final String MERCHANT_CERT_PATH_CONFIG_KEY    = "merchantCertPath";
    public static final String ALIPAY_ROOT_CERT_PATH_CONFIG_KEY = "alipayRootCertPath";
    public static final String SIGN_TYPE_CONFIG_KEY             = "signType";
    public static final String NOTIFY_URL_CONFIG_KEY            = "notifyUrl";
    public static final String SIGN_PROVIDER_CONFIG_KEY         = "signProvider";

    /**
     * 與閘道器HTTP互動中涉及到的欄位值
     */
    public static final String BIZ_CONTENT_FIELD    = "biz_content";
    public static final String ALIPAY_CERT_SN_FIELD = "alipay_cert_sn";
    public static final String SIGN_FIELD           = "sign";
    public static final String SIGN_TYPE_FIELD      = "sign_type";
    public static final String BODY_FIELD           = "http_body";
    public static final String NOTIFY_URL_FIELD     = "notify_url";
    public static final String METHOD_FIELD         = "method";
    public static final String RESPONSE_SUFFIX      = "_response";
    public static final String ERROR_RESPONSE       = "error_response";

    /**
     * 預設字元集編碼，EasySDK統一固定使用UTF-8編碼，無需使用者感知編碼，使用者面對的總是String而不是bytes
     */
    public static final Charset DEFAULT_CHARSET = StandardCharsets.UTF_8;

    /**
     * 預設的簽名演算法，EasySDK統一固定使用RSA2簽名演算法（即SHA_256_WITH_RSA），但此引數依然需要使用者指定以便使用者感知，因為在開放平臺介面簽名配置介面中需要選擇同樣的演算法
     */
    public static final String RSA2 = "RSA2";

    /**
     * RSA2對應的真實簽名演算法名稱
     */
    public static final String SHA_256_WITH_RSA = "SHA256WithRSA";

    /**
     * RSA2對應的真實非對稱加密演算法名稱
     */
    public static final String RSA = "RSA";

    /**
     * 申請生成的重定向網頁的請求型別，GET表示生成URL
     */
    public static final String GET = "GET";

    /**
     * 申請生成的重定向網頁的請求型別，POST表示生成form表單
     */
    public static final String POST = "POST";

    /**
     * 使用Aliyun KMS簽名服務時簽名提供方的名稱
     */
    public static final String AliyunKMS = "AliyunKMS";
}