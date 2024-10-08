/**
 * Alipay.com Inc.
 * Copyright (c) 2004-2020 All Rights Reserved.
 */
package com.alipay.easysdk.kernel;

import com.alipay.easysdk.kernel.util.Signer;
import com.aliyun.tea.TeaModel;
import com.google.common.base.Preconditions;
import com.google.common.base.Strings;

import java.util.Map;

/**
 * @author zhongyu
 * @version : Context.java, v 0.1 2020年05月24日 10:41 上午 zhongyu Exp $
 */
public class Context {
    /**
     * 客戶端配置引數
     */
    private final Map<String, Object> config;

    /**
     * SDK版本號
     */
    private String sdkVersion;

    /**
     * 證書模式執行時環境
     */
    private CertEnvironment certEnvironment;

    /**
     * SHA256WithRSA簽名器
     */
    private Signer signer;

    public Context(Config options, String sdkVersion) throws Exception {
        config = TeaModel.buildMap(options);
        this.sdkVersion = sdkVersion;
        Preconditions.checkArgument(AlipayConstants.RSA2.equals(getConfig(AlipayConstants.SIGN_TYPE_CONFIG_KEY)),
                "Alipay Easy SDK只允許使用RSA2簽名方式，RSA簽名方式由於安全性相比RSA2弱已不再推薦。");

        if (!Strings.isNullOrEmpty(getConfig(AlipayConstants.ALIPAY_CERT_PATH_CONFIG_KEY))) {
            certEnvironment = new CertEnvironment(
                    getConfig(AlipayConstants.MERCHANT_CERT_PATH_CONFIG_KEY),
                    getConfig(AlipayConstants.ALIPAY_CERT_PATH_CONFIG_KEY),
                    getConfig(AlipayConstants.ALIPAY_ROOT_CERT_PATH_CONFIG_KEY));
        }
        signer = new Signer();
    }

    public String getConfig(String key) {
        if (String.valueOf(config.get(key)) == "null") {
            return null;
        } else {
            return String.valueOf(config.get(key));
        }
    }

    public String getSdkVersion() {
        return sdkVersion;
    }

    public void setSdkVersion(String sdkVersion) {
        this.sdkVersion = sdkVersion;
    }

    public CertEnvironment getCertEnvironment() {
        return certEnvironment;
    }

    public Signer getSigner() {
        return signer;
    }

    public void setSigner(Signer signer) {
        this.signer = signer;
    }
}