package com.alipay.easysdk.kms.aliyun;

import com.alipay.easysdk.kernel.util.Signer;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * KMS簽名器
 *
 * @author aliyunkms
 * @version $Id: AliyunKMSSigner.java, v 0.1 2020年05月08日 9:10 PM aliyunkms Exp $
 */
public class AliyunKMSSigner extends Signer {
    private AliyunKMSClient client;
    private static final Logger LOGGER = LoggerFactory.getLogger(Signer.class);

    public AliyunKMSSigner(AliyunKMSClient aliyunKmsClient) {
        this.client = aliyunKmsClient;
    }

    /**
     * 計算簽名
     *
     * @param content       待簽名的內容
     * @param privateKeyPem 私鑰，使用KMS簽名不使用此引數
     * @return 簽名值的Base64串
     */
    public String sign(String content, String privateKeyPem) {
        try {
            return this.client.sign(content);
        } catch (Exception e) {
            String errorMessage = "簽名遭遇異常，content=" + content + " reason=" + e.getMessage();
            LOGGER.error(errorMessage, e);
            throw new RuntimeException(errorMessage, e);
        }
    }

    public AliyunKMSClient getClient() {
        return this.client;
    }

    public void setClient(AliyunKMSClient client) {
        this.client = client;
    }
}
