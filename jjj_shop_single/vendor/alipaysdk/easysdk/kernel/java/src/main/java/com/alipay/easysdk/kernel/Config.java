/**
 * Alipay.com Inc.
 * Copyright (c) 2004-2020 All Rights Reserved.
 */
package com.alipay.easysdk.kernel;

import com.aliyun.tea.NameInMap;
import com.aliyun.tea.TeaModel;
import com.aliyun.tea.Validation;

/**
 * @author zhongyu
 * @version : Config.java, v 0.1 2020年05月22日 4:25 下午 zhongyu Exp $
 */
public class Config extends TeaModel {

    /**
     * 通訊協議，通常填寫https
     */
    @NameInMap("protocol")
    @Validation(required = true)
    public String protocol;

    /**
     * 閘道器域名
     * 線上為：openapi.alipay.com
     * 沙箱為：openapi.alipaydev.com
     */
    @NameInMap("gatewayHost")
    @Validation(required = true)
    public String gatewayHost;

    /**
     * AppId
     */
    @NameInMap("appId")
    @Validation(required = true)
    public String appId;

    /**
     * 簽名型別，Alipay Easy SDK只推薦使用RSA2，估此處固定填寫RSA2
     */
    @NameInMap("signType")
    @Validation(required = true)
    public String signType;

    /**
     * 支付寶公鑰
     */
    @NameInMap("alipayPublicKey")
    public String alipayPublicKey;

    /**
     * 應用私鑰
     */
    @NameInMap("merchantPrivateKey")
    @Validation(required = true)
    public String merchantPrivateKey;

    /**
     * 應用公鑰證書檔案路徑
     */
    @NameInMap("merchantCertPath")
    public String merchantCertPath;

    /**
     * 支付寶公鑰證書檔案路徑
     */
    @NameInMap("alipayCertPath")
    public String alipayCertPath;

    /**
     * 支付寶根證書檔案路徑
     */
    @NameInMap("alipayRootCertPath")
    public String alipayRootCertPath;

    /**
     * 非同步通知回撥地址（可選）
     */
    @NameInMap("notifyUrl")
    public String notifyUrl;

    /**
     * AES金鑰（可選）
     */
    @NameInMap("encryptKey")
    public String encryptKey;

    /**
     * 簽名提供方的名稱(可選)，例：Aliyun KMS簽名，signProvider = "AliyunKMS"
     */
    @NameInMap("signProvider")
    public String signProvider;

    /**
     * 代理地址（可選）
     * 例如：http://127.0.0.1:8080
     */
    @NameInMap("httpProxy")
    public String httpProxy;


    /**
     * 忽略證書校驗（可選）
     */
    @NameInMap("ignoreSSL")
    public boolean ignoreSSL;

}