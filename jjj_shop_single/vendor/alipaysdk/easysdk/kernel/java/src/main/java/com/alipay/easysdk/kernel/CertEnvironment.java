/**
 * Alipay.com Inc. Copyright (c) 2004-2020 All Rights Reserved.
 */
package com.alipay.easysdk.kernel;

import com.alipay.easysdk.kernel.util.AntCertificationUtil;
import com.google.common.base.Strings;

import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;

/**
 * 證書模式執行時環境
 *
 * @author zhongyu
 * @version $Id: CertEnvironment.java, v 0.1 2020年01月02日 5:21 PM zhongyu Exp $
 */
public class CertEnvironment {
    /**
     * 支付寶根證書內容
     */
    private String rootCertContent;

    /**
     * 支付寶根證書序列號
     */
    private String rootCertSN;

    /**
     * 商戶應用公鑰證書序列號
     */
    private String merchantCertSN;

    /**
     * 快取的不同支付寶公鑰證書序列號對應的支付寶公鑰
     */
    private Map<String, String> cachedAlipayPublicKey = new ConcurrentHashMap<String, String>();

    /**
     * 構造證書執行環境
     *
     * @param merchantCertPath   商戶公鑰證書路徑
     * @param alipayCertPath     支付寶公鑰證書路徑
     * @param alipayRootCertPath 支付寶根證書路徑
     */
    public CertEnvironment(String merchantCertPath, String alipayCertPath, String alipayRootCertPath) {
        if (Strings.isNullOrEmpty(merchantCertPath) || Strings.isNullOrEmpty(alipayCertPath) || Strings.isNullOrEmpty(alipayCertPath)) {
            throw new RuntimeException("證書引數merchantCertPath、alipayCertPath或alipayRootCertPath設定不完整。");
        }

        this.rootCertContent = AntCertificationUtil.readCertContent(alipayRootCertPath);
        this.rootCertSN = AntCertificationUtil.getRootCertSN(rootCertContent);
        this.merchantCertSN = AntCertificationUtil.getCertSN(AntCertificationUtil.readCertContent((merchantCertPath)));

        String alipayPublicCertContent = AntCertificationUtil.readCertContent(alipayCertPath);
        cachedAlipayPublicKey.put(AntCertificationUtil.getCertSN(alipayPublicCertContent),
                AntCertificationUtil.getCertPublicKey(alipayPublicCertContent));
    }

    public String getRootCertSN() {
        return rootCertSN;
    }

    public String getMerchantCertSN() {
        return merchantCertSN;
    }

    public String getAlipayPublicKey(String sn) {
        //如果沒有指定sn，則預設取快取中的第一個值
        if (Strings.isNullOrEmpty(sn)) {
            return cachedAlipayPublicKey.values().iterator().next();
        }

        if (cachedAlipayPublicKey.containsKey(sn)) {
            return cachedAlipayPublicKey.get(sn);
        } else {
            //閘道器在支付寶公鑰證書變更前，一定會確認通知到商戶並在商戶做出反饋後，才會更新該商戶的支付寶公鑰證書
            //TODO: 後續可以考慮加入自動升級支付寶公鑰證書邏輯，注意併發更新衝突問題
            throw new RuntimeException("支付寶公鑰證書[" + sn + "]已過期，請重新下載最新支付寶公鑰證書並替換原證書檔案");
        }
    }
}