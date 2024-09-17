/**
 * Alipay.com Inc.
 * Copyright (c) 2004-2020 All Rights Reserved.
 */
package com.alipay.easysdk.factory;

import com.alipay.easysdk.kernel.AlipayConstants;
import com.alipay.easysdk.kernel.Client;
import com.alipay.easysdk.kernel.Config;
import com.alipay.easysdk.kernel.Context;
import com.alipay.easysdk.kms.aliyun.AliyunKMSClient;
import com.alipay.easysdk.kms.aliyun.AliyunKMSSigner;
import com.aliyun.tea.TeaModel;

/**
 * @author junying
 * @version : MultipleFactory.java, v 0.1 2020年12月23日 2:14 下午 junying Exp $
 */
public class MultipleFactory {

    public final String SDK_VERSION = "alipay-easysdk-java-2.2.2";

    /**
     * 將一些初始化耗時較多的資訊快取在上下文中
     */
    public Context context;

    /**
     * 設定客戶端引數，只需設定一次，即可反覆使用各種場景下的API Client
     *
     * @param options 客戶端引數物件
     */
    public void setOptions(Config options) {
        try {
            context = new Context(options, SDK_VERSION);

            if (AlipayConstants.AliyunKMS.equals(context.getConfig(AlipayConstants.SIGN_PROVIDER_CONFIG_KEY))) {
                context.setSigner(new AliyunKMSSigner(new AliyunKMSClient(TeaModel.buildMap(options))));
            }

        } catch (Exception e) {
            throw new RuntimeException(e.getMessage(), e);
        }
    }

    /**
     * 獲取支付通用API Client
     *
     * @return 支付通用API Client
     */
    public com.alipay.easysdk.payment.common.Client Common() throws Exception {
        return new com.alipay.easysdk.payment.common.Client(new Client(context));
    }

    /**
     * 獲取花唄相關API Client
     *
     * @return 花唄相關API Client
     */
    public com.alipay.easysdk.payment.huabei.Client Huabei() throws Exception {
        return new com.alipay.easysdk.payment.huabei.Client(new Client(context));
    }

    /**
     * 獲取當面付相關API Client
     *
     * @return 當面付相關API Client
     */
    public com.alipay.easysdk.payment.facetoface.Client FaceToFace() throws Exception {
        return new com.alipay.easysdk.payment.facetoface.Client(new Client(context));
    }

    /**
     * 獲取電腦網站支付相關API Client
     *
     * @return 電腦網站支付相關API Client
     */
    public com.alipay.easysdk.payment.page.Client Page() throws Exception {
        return new com.alipay.easysdk.payment.page.Client(new Client(context));
    }

    /**
     * 獲取手機網站支付相關API Client
     *
     * @return 手機網站支付相關API Client
     */
    public com.alipay.easysdk.payment.wap.Client Wap() throws Exception {
        return new com.alipay.easysdk.payment.wap.Client(new Client(context));
    }

    /**
     * 獲取手機APP支付相關API Client
     *
     * @return 手機APP支付相關API Client
     */
    public com.alipay.easysdk.payment.app.Client App() throws Exception {
        return new com.alipay.easysdk.payment.app.Client(new Client(context));
    }


    /**
     * 獲取圖片相關API Client
     *
     * @return 圖片相關API Client
     */
    public com.alipay.easysdk.base.image.Client Image() throws Exception {
        return new com.alipay.easysdk.base.image.Client(new Client(context));
    }

    /**
     * 獲取影片相關API Client
     *
     * @return 影片相關API Client
     */
    public com.alipay.easysdk.base.video.Client Video() throws Exception {
        return new com.alipay.easysdk.base.video.Client(new Client(context));
    }

    /**
     * 獲取OAuth認證相關API Client
     *
     * @return OAuth認證相關API Client
     */
    public com.alipay.easysdk.base.oauth.Client OAuth() throws Exception {
        return new com.alipay.easysdk.base.oauth.Client(new Client(context));
    }

    /**
     * 獲取小程式二維碼相關API Client
     *
     * @return 小程式二維碼相關API Client
     */
    public com.alipay.easysdk.base.qrcode.Client Qrcode() throws Exception {
        return new com.alipay.easysdk.base.qrcode.Client(new Client(context));
    }


    /**
     * 獲取生活號相關API Client
     *
     * @return 生活號相關API Client
     */
    public com.alipay.easysdk.marketing.openlife.Client OpenLife() throws Exception {
        return new com.alipay.easysdk.marketing.openlife.Client(new Client(context));
    }

    /**
     * 獲取支付寶卡包相關API Client
     *
     * @return 支付寶卡包相關API Client
     */
    public com.alipay.easysdk.marketing.pass.Client Pass() throws Exception {
        return new com.alipay.easysdk.marketing.pass.Client(new Client(context));
    }

    /**
     * 獲取小程式模板訊息相關API Client
     *
     * @return 小程式模板訊息相關API Client
     */
    public com.alipay.easysdk.marketing.templatemessage.Client TemplateMessage() throws Exception {
        return new com.alipay.easysdk.marketing.templatemessage.Client(new Client(context));
    }


    /**
     * 獲取支付寶身份認證相關API Client
     *
     * @return 支付寶身份認證相關API Client
     */
    public com.alipay.easysdk.member.identification.Client Identification() throws Exception {
        return new com.alipay.easysdk.member.identification.Client(new Client(context));
    }

    /**
     * 獲取文字風險識別相關API Client
     *
     * @return 文字風險識別相關API Client
     */
    public com.alipay.easysdk.security.textrisk.Client TextRisk() throws Exception {
        return new com.alipay.easysdk.security.textrisk.Client(new Client(context));
    }

    /**
     * 獲取OpenAPI通用介面，可透過自行拼裝引數，呼叫幾乎所有OpenAPI
     *
     * @return OpenAPI通用介面
     */
    public com.alipay.easysdk.util.generic.Client Generic() throws Exception {
        return new com.alipay.easysdk.util.generic.Client(new Client(context));
    }

    /**
     * 獲取AES128加解密相關API Client，常用於會員手機號的解密
     *
     * @return AES128加解密相關API Client
     */
    public com.alipay.easysdk.util.aes.Client AES() throws Exception {
        return new com.alipay.easysdk.util.aes.Client(new Client(context));
    }
}