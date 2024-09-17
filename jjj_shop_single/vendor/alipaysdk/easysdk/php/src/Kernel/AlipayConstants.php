<?php


namespace Alipay\EasySDK\Kernel;


class AlipayConstants
{
    /**
     * Config配置引數Key值
     */
    const PROTOCOL_CONFIG_KEY              = "protocol";
    const HOST_CONFIG_KEY                  = "gatewayHost";
    const ALIPAY_CERT_PATH_CONFIG_KEY      = "alipayCertPath";
    const MERCHANT_CERT_PATH_CONFIG_KEY    = "merchantCertPath";
    const ALIPAY_ROOT_CERT_PATH_CONFIG_KEY = "alipayRootCertPath";
    const SIGN_TYPE_CONFIG_KEY             = "signType";
    const NOTIFY_URL_CONFIG_KEY            = "notifyUrl";

    /**
     * 與閘道器HTTP互動中涉及到的欄位值
     */
    const BIZ_CONTENT_FIELD    = "biz_content";
    const ALIPAY_CERT_SN_FIELD = "alipay_cert_sn";
    const SIGN_FIELD           = "sign";
    const BODY_FIELD           = "http_body";
    const NOTIFY_URL_FIELD     = "notify_url";
    const METHOD_FIELD         = "method";
    const RESPONSE_SUFFIX      = "_response";
    const ERROR_RESPONSE       = "error_response";
    const SDK_VERSION          = "alipay-easysdk-php-2.2.3";

    /**
     * 預設字元集編碼，EasySDK統一固定使用UTF-8編碼，無需使用者感知編碼，使用者面對的總是String而不是bytes
     */
    const DEFAULT_CHARSET = "UTF-8";

    /**
     * 預設的簽名演算法，EasySDK統一固定使用RSA2簽名演算法（即SHA_256_WITH_RSA），但此引數依然需要使用者指定以便使用者感知，因為在開放平臺介面簽名配置介面中需要選擇同樣的演算法
     */
    const RSA2 = "RSA2";

    /**
     * RSA2對應的真實簽名演算法名稱
     */
    const SHA_256_WITH_RSA = "SHA256WithRSA";

    /**
     * RSA2對應的真實非對稱加密演算法名稱
     */
    const RSA = "RSA";

    /**
     * 申請生成的重定向網頁的請求型別，GET表示生成URL
     */
    const GET = "GET";

    /**
     * 申請生成的重定向網頁的請求型別，POST表示生成form表單
     */
    const POST = "POST";

}