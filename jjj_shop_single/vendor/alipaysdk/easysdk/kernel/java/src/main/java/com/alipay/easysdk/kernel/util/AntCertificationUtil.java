package com.alipay.easysdk.kernel.util;

import com.google.common.base.Strings;
import com.google.common.io.ByteStreams;
import com.google.common.io.Files;
import org.bouncycastle.jce.provider.BouncyCastleProvider;
import org.bouncycastle.util.encoders.Base64;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.math.BigInteger;
import java.nio.charset.StandardCharsets;
import java.security.InvalidKeyException;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.security.NoSuchProviderException;
import java.security.Principal;
import java.security.PublicKey;
import java.security.Security;
import java.security.SignatureException;
import java.security.cert.Certificate;
import java.security.cert.CertificateException;
import java.security.cert.CertificateExpiredException;
import java.security.cert.CertificateFactory;
import java.security.cert.CertificateNotYetValidException;
import java.security.cert.X509Certificate;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * 證書檔案可信校驗
 *
 * @author junying.wjy
 * @version $Id: AntCertificationUtil.java, v 0.1 2019-07-29 下午04:46 junying.wjy Exp $
 */
public class AntCertificationUtil {
    private static final Logger LOGGER = LoggerFactory.getLogger(AntCertificationUtil.class);

    private static BouncyCastleProvider provider;

    static {
        provider = new BouncyCastleProvider();
        Security.addProvider(provider);
    }

    /**
     * 驗證證書是否可信
     *
     * @param certContent     需要驗證的目標證書或者證書鏈
     * @param rootCertContent 可信根證書列表
     */
    public static boolean isTrusted(String certContent, String rootCertContent) {
        X509Certificate[] certificates;
        try {
            certificates = readPemCertChain(certContent);
        } catch (Exception e) {
            LOGGER.error("讀取證書失敗", e);
            throw new RuntimeException(e);
        }

        List<X509Certificate> rootCerts = new ArrayList<X509Certificate>();
        try {
            X509Certificate[] certs = readPemCertChain(rootCertContent);
            rootCerts.addAll(Arrays.asList(certs));
        } catch (Exception e) {
            LOGGER.error("讀取根證書失敗", e);
            throw new RuntimeException(e);
        }

        return verifyCertChain(certificates, rootCerts.toArray(new X509Certificate[rootCerts.size()]));
    }

    /**
     * 驗證證書是否是信任證書庫中證書籤發的
     *
     * @param cert      目標驗證證書
     * @param rootCerts 可信根證書列表
     * @return 驗證結果
     */
    private static boolean verifyCert(X509Certificate cert, X509Certificate[] rootCerts) {
        try {
            cert.checkValidity();
        } catch (CertificateExpiredException e) {
            LOGGER.error("證書已經過期", e);
            return false;
        } catch (CertificateNotYetValidException e) {
            LOGGER.error("證書未啟用", e);
            return false;
        }

        Map<Principal, X509Certificate> subjectMap = new HashMap<Principal, X509Certificate>();

        for (X509Certificate root : rootCerts) {
            subjectMap.put(root.getSubjectDN(), root);
        }

        Principal issuerDN = cert.getIssuerDN();
        X509Certificate issuer = subjectMap.get(issuerDN);
        if (issuer == null) {
            LOGGER.error("證書鏈驗證失敗");
            return false;
        }
        try {
            PublicKey publicKey = issuer.getPublicKey();
            verifySignature(publicKey, cert);
        } catch (Exception e) {
            LOGGER.error("證書鏈驗證失敗", e);
            return false;
        }
        return true;
    }

    /**
     * 驗證證書鏈是否是信任證書庫中證書籤發的
     *
     * @param certs     目標驗證證書列表
     * @param rootCerts 可信根證書列表
     * @return 驗證結果
     */
    private static boolean verifyCertChain(X509Certificate[] certs, X509Certificate[] rootCerts) {
        boolean sorted = sortByDn(certs);
        if (!sorted) {
            LOGGER.error("證書鏈驗證失敗：不是完整的證書鏈");
            return false;
        }

        //先驗證第一個證書是不是信任庫中證書籤發的
        X509Certificate prev = certs[0];
        boolean firstOK = verifyCert(prev, rootCerts);
        if (!firstOK || certs.length == 1) {
            return firstOK;
        }

        //驗證證書鏈
        for (int i = 1; i < certs.length; i++) {
            try {
                X509Certificate cert = certs[i];
                try {
                    cert.checkValidity();
                } catch (CertificateExpiredException e) {
                    LOGGER.error("證書已經過期");
                    return false;
                } catch (CertificateNotYetValidException e) {
                    LOGGER.error("證書未啟用");
                    return false;
                }
                verifySignature(prev.getPublicKey(), cert);
                prev = cert;
            } catch (Exception e) {
                LOGGER.error("證書鏈驗證失敗");
                return false;
            }
        }

        return true;
    }

    private static void verifySignature(PublicKey publicKey, X509Certificate cert)
            throws NoSuchProviderException, CertificateException, NoSuchAlgorithmException, InvalidKeyException,
            SignatureException {
        cert.verify(publicKey, provider.getName());
    }

    /**
     * 將證書鏈按照完整的簽發順序進行排序，排序後證書鏈為：[issuerA, subjectA]-[issuerA, subjectB]-[issuerB, subjectC]-[issuerC, subjectD]...
     *
     * @param certs 證書鏈
     * @return true：排序成功，false：證書鏈不完整
     */
    private static boolean sortByDn(X509Certificate[] certs) {
        //主題和證書的對映
        Map<Principal, X509Certificate> subjectMap = new HashMap<Principal, X509Certificate>();
        //簽發者和證書的對映
        Map<Principal, X509Certificate> issuerMap = new HashMap<Principal, X509Certificate>();
        //是否包含自簽名證書
        boolean hasSelfSignedCert = false;

        for (X509Certificate cert : certs) {
            if (isSelfSigned(cert)) {
                if (hasSelfSignedCert) {
                    return false;
                }
                hasSelfSignedCert = true;
            }

            Principal subjectDN = cert.getSubjectDN();
            Principal issuerDN = cert.getIssuerDN();

            subjectMap.put(subjectDN, cert);
            issuerMap.put(issuerDN, cert);
        }

        List<X509Certificate> certChain = new ArrayList<X509Certificate>();

        X509Certificate current = certs[0];
        addressingUp(subjectMap, certChain, current);
        addressingDown(issuerMap, certChain, current);

        //說明證書鏈不完整
        if (certs.length != certChain.size()) {
            return false;
        }

        //將證書鏈複製到原先的資料
        for (int i = 0; i < certChain.size(); i++) {
            certs[i] = certChain.get(i);
        }
        return true;
    }

    /**
     * 驗證證書是否是自簽發的
     *
     * @param cert 目標證書
     * @return true；自簽發，false；不是自簽發
     */
    private static boolean isSelfSigned(X509Certificate cert) {
        return cert.getSubjectDN().equals(cert.getIssuerDN());
    }

    /**
     * 向上構造證書鏈
     *
     * @param subjectMap 主題和證書的對映
     * @param certChain  證書鏈
     * @param current    當前需要插入證書鏈的證書，include
     */
    private static void addressingUp(final Map<Principal, X509Certificate> subjectMap, List<X509Certificate> certChain,
                                     final X509Certificate current) {
        certChain.add(0, current);
        if (isSelfSigned(current)) {
            return;
        }
        Principal issuerDN = current.getIssuerDN();
        X509Certificate issuer = subjectMap.get(issuerDN);
        if (issuer == null) {
            return;
        }
        addressingUp(subjectMap, certChain, issuer);
    }

    /**
     * 向下構造證書鏈
     *
     * @param issuerMap 簽發者和證書的對映
     * @param certChain 證書鏈
     * @param current   當前需要插入證書鏈的證書，exclude
     */
    private static void addressingDown(final Map<Principal, X509Certificate> issuerMap, List<X509Certificate> certChain,
                                       final X509Certificate current) {
        Principal subjectDN = current.getSubjectDN();
        X509Certificate subject = issuerMap.get(subjectDN);
        if (subject == null) {
            return;
        }
        if (isSelfSigned(subject)) {
            return;
        }
        certChain.add(subject);
        addressingDown(issuerMap, certChain, subject);
    }

    private static X509Certificate[] readPemCertChain(String cert) throws CertificateException {
        ByteArrayInputStream inputStream = new ByteArrayInputStream(cert.getBytes());
        CertificateFactory factory = CertificateFactory.getInstance("X.509", provider);
        Collection<? extends Certificate> certificates = factory.generateCertificates(inputStream);
        return certificates.toArray(new X509Certificate[certificates.size()]);
    }

    /**
     * 獲取支付寶根證書序列號
     *
     * @param rootCertContent 支付寶根證書內容
     * @return 支付寶根證書序列號
     */
    public static String getRootCertSN(String rootCertContent) {
        String rootCertSN = null;
        try {
            X509Certificate[] x509Certificates = readPemCertChain(rootCertContent);
            MessageDigest md = MessageDigest.getInstance("MD5");
            for (X509Certificate c : x509Certificates) {
                if (c.getSigAlgOID().startsWith("1.2.840.113549.1.1")) {
                    md.update((c.getIssuerX500Principal().getName() + c.getSerialNumber()).getBytes());
                    String certSN = new BigInteger(1, md.digest()).toString(16);
                    //BigInteger會把0省略掉，需補全至32位
                    certSN = fillMD5(certSN);
                    if (Strings.isNullOrEmpty(rootCertSN)) {
                        rootCertSN = certSN;
                    } else {
                        rootCertSN = rootCertSN + "_" + certSN;
                    }
                }

            }
        } catch (Exception e) {
            LOGGER.error("提取根證書失敗");
        }
        return rootCertSN;
    }

    /**
     * 獲取公鑰證書序列號
     *
     * @param certContent 公鑰證書內容
     * @return 公鑰證書序列號
     */
    public static String getCertSN(String certContent) {
        try {
            InputStream inputStream = new ByteArrayInputStream(certContent.getBytes());
            CertificateFactory factory = CertificateFactory.getInstance("X.509", "BC");
            X509Certificate cert = (X509Certificate) factory.generateCertificate(inputStream);
            return md5((cert.getIssuerX500Principal().getName() + cert.getSerialNumber()).getBytes());
        } catch (Exception e) {
            throw new RuntimeException(e.getMessage(), e);
        }
    }

    private static String md5(byte[] bytes) throws NoSuchAlgorithmException {
        MessageDigest md = MessageDigest.getInstance("MD5");
        md.update(bytes);
        String certSN = new BigInteger(1, md.digest()).toString(16);
        //BigInteger會把0省略掉，需補全至32位
        certSN = fillMD5(certSN);
        return certSN;
    }

    private static String fillMD5(String md5) {
        return md5.length() == 32 ? md5 : fillMD5("0" + md5);
    }

    /**
     * 提取公鑰證書中的公鑰
     *
     * @param certContent 公鑰證書內容
     * @return 公鑰證書中的公鑰
     */
    public static String getCertPublicKey(String certContent) {
        try {
            InputStream inputStream = new ByteArrayInputStream(certContent.getBytes());
            CertificateFactory factory = CertificateFactory.getInstance("X.509", "BC");
            X509Certificate cert = (X509Certificate) factory.generateCertificate(inputStream);
            return Base64.toBase64String(cert.getPublicKey().getEncoded());
        } catch (Exception e) {
            throw new RuntimeException(e.getMessage(), e);
        }
    }

    /**
     * 從檔案中讀取證書內容
     *
     * @param certPath 證書路徑
     * @return 證書內容
     */
    public static String readCertContent(String certPath) {
        if (existsInFileSystem(certPath)) {
            return readFromFileSystem(certPath);
        }
        return readFromClassPath(certPath);
    }

    private static boolean existsInFileSystem(String certPath) {
        try {
            return new File(certPath).exists();
        } catch (Throwable e) {
            return false;
        }
    }

    private static String readFromFileSystem(String certPath) {
        try {
            return new String(Files.toByteArray(new File(certPath)), StandardCharsets.UTF_8);
        } catch (IOException e) {
            throw new RuntimeException("從檔案系統中讀取[" + certPath + "]失敗，" + e.getMessage(), e);
        }
    }

    private static String readFromClassPath(String certPath) {
        try (InputStream inputStream = Thread.currentThread().getContextClassLoader().getResourceAsStream(certPath)) {
            return new String(ByteStreams.toByteArray(inputStream), StandardCharsets.UTF_8);
        } catch (Exception e) {
            String errorMessage = e.getMessage() == null ? "" : e.getMessage() + "。";
            if (certPath.startsWith("/")) {
                errorMessage += "ClassPath路徑不可以/開頭，請去除後重試。";
            }
            throw new RuntimeException("讀取[" + certPath + "]失敗。" + errorMessage, e);
        }
    }
}
