// This file is auto-generated, don't edit it. Thanks.
package com.alipay.easysdk.kernel;

import com.alipay.easysdk.kernel.util.AES;
import com.alipay.easysdk.kernel.util.JsonUtil;
import com.alipay.easysdk.kernel.util.MultipartUtil;
import com.alipay.easysdk.kernel.util.PageUtil;
import com.alipay.easysdk.kernel.util.SignContentExtractor;
import com.alipay.easysdk.kernel.util.Signer;
import com.aliyun.tea.TeaResponse;
import com.google.common.base.Strings;
import com.google.common.io.Files;
import com.google.gson.Gson;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Collections;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;
import java.util.Map.Entry;
import java.util.TimeZone;
import java.util.TreeMap;

public class Client {
    private static final Logger LOGGER = LoggerFactory.getLogger(Client.class);

    /**
     * 構造成本較高的一些引數快取在上下文中
     */
    private final Context context;

    /**
     * 注入的可選額外文字引數集合
     */
    private final Map<String, String> optionalTextParams = new HashMap<>();

    /**
     * 注入的可選業務引數集合
     */
    private final Map<String, Object> optionalBizParams = new HashMap<>();

    /**
     * 建構函式
     *
     * @param context 上下文物件
     */
    public Client(Context context) {
        this.context = context;
    }

    /**
     * 注入額外文字引數
     *
     * @param key   引數名稱
     * @param value 引數的值
     * @return 本客戶端本身，便於鏈路呼叫
     */
    public Client injectTextParam(String key, String value) {
        optionalTextParams.put(key, value);
        return this;
    }

    /**
     * 注入額外業務引數
     *
     * @param key   業務引數名稱
     * @param value 業務引數的值
     * @return 本客戶端本身，便於鏈式呼叫
     */
    public Client injectBizParam(String key, Object value) {
        optionalBizParams.put(key, value);
        return this;
    }

    /**
     * 獲取時間戳，格式yyyy-MM-dd HH:mm:ss
     *
     * @return 當前時間戳
     */
    public String getTimestamp() throws Exception {
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        df.setTimeZone(TimeZone.getTimeZone("GMT+8"));
        return df.format(new Date());
    }

    /**
     * 獲取Config中的配置項
     *
     * @param key 配置項的名稱
     * @return 配置項的值
     */
    public String getConfig(String key) throws Exception {
        return context.getConfig(key);
    }

    /**
     * 獲取SDK版本資訊
     *
     * @return SDK版本資訊
     */
    public String getSdkVersion() throws Exception {
        return context.getSdkVersion();
    }

    /**
     * 將業務引數和其他額外文字引數按www-form-urlencoded格式轉換成HTTP Body中的位元組陣列，注意要做URL Encode
     *
     * @param bizParams 業務引數
     * @return HTTP Body中的位元組陣列
     */
    public byte[] toUrlEncodedRequestBody(java.util.Map<String, ?> bizParams) throws Exception {
        Map<String, String> sortedMap = getSortedMap(Collections.<String, String>emptyMap(), bizParams, null);
        return buildQueryString(sortedMap).getBytes(AlipayConstants.DEFAULT_CHARSET);
    }

    /**
     * 將閘道器響應發序列化成Map，同時將API的介面名稱和響應原文插入到響應Map的method和body欄位中
     *
     * @param response HTTP響應
     * @param method   呼叫的OpenAPI的介面名稱
     * @return 響應反序列化的Map
     */
    @SuppressWarnings("unchecked")
    public java.util.Map<String, Object> readAsJson(TeaResponse response, String method) throws Exception {
        String responseBody = response.getResponseBody();
        Map map = new Gson().fromJson(responseBody, Map.class);
        map.put(AlipayConstants.BODY_FIELD, responseBody);
        map.put(AlipayConstants.METHOD_FIELD, method);
        closeConnection(response);
        return map;
    }

    private void closeConnection(TeaResponse response) {
        if (response.getResponse() != null) {
            try {
                response.getResponse().close();
            } catch (IOException e) {
                LOGGER.warn("關閉連結遭遇異常：" + e.getMessage(), e);
            }
        }
    }

    /**
     * 從響應Map中提取返回值物件的Map，並將響應原文插入到body欄位中
     *
     * @param respMap 響應Map
     * @return 返回值物件Map
     */
    public java.util.Map<String, Object> toRespModel(java.util.Map<String, Object> respMap) throws Exception {
        String methodName = (String) respMap.get(AlipayConstants.METHOD_FIELD);
        String responseNodeName = methodName.replace('.', '_') + AlipayConstants.RESPONSE_SUFFIX;
        String errorNodeName = AlipayConstants.ERROR_RESPONSE;

        //先找正常響應節點
        for (Entry<String, Object> pair : respMap.entrySet()) {
            if (responseNodeName.equals(pair.getKey())) {
                Map<String, Object> model = (Map<String, Object>) pair.getValue();
                model.put(AlipayConstants.BODY_FIELD, respMap.get(AlipayConstants.BODY_FIELD));
                return model;
            }
        }

        //再找異常響應節點
        for (Entry<String, Object> pair : respMap.entrySet()) {
            if (errorNodeName.equals(pair.getKey())) {
                Map<String, Object> model = (Map<String, Object>) pair.getValue();
                model.put(AlipayConstants.BODY_FIELD, respMap.get(AlipayConstants.BODY_FIELD));
                return model;
            }
        }

        throw new RuntimeException("響應格式不符合預期，找不到" + responseNodeName + "或" + errorNodeName + "節點");
    }

    /**
     * 生成隨機分界符，用於multipart格式的HTTP請求Body的多個欄位間的分隔
     *
     * @return 隨機分界符
     */
    public String getRandomBoundary() throws Exception {
        return System.currentTimeMillis() + "";
    }

    /**
     * 將其他額外文字引數和檔案引數按multipart/form-data格式轉換成HTTP Body中的位元組陣列流
     *
     * @param textParams 其他額外文字引數
     * @param fileParams 業務檔案引數
     * @param boundary   HTTP Body中multipart格式的分隔符
     * @return Multipart格式的位元組流
     */
    public java.io.InputStream toMultipartRequestBody(java.util.Map<String, String> textParams,
                                                      java.util.Map<String, String> fileParams, String boundary) throws Exception {
        ByteArrayOutputStream stream = new ByteArrayOutputStream();

        //補充其他額外引數
        addOtherParams(textParams, null);

        for (Entry<String, String> pair : textParams.entrySet()) {
            if (!Strings.isNullOrEmpty(pair.getKey()) && !Strings.isNullOrEmpty(pair.getValue())) {
                stream.write(MultipartUtil.getEntryBoundary(boundary));
                stream.write(MultipartUtil.getTextEntry(pair.getKey(), pair.getValue()));
            }
        }

        //組裝檔案引數
        for (Entry<String, String> pair : fileParams.entrySet()) {
            if (!Strings.isNullOrEmpty(pair.getKey()) && pair.getValue() != null) {
                stream.write(MultipartUtil.getEntryBoundary(boundary));
                stream.write(MultipartUtil.getFileEntry(pair.getKey(), pair.getValue()));
                stream.write(Files.toByteArray(new File(pair.getValue())));
            }
        }

        //新增結束標記
        stream.write(MultipartUtil.getEndBoundary(boundary));

        return new ByteArrayInputStream(stream.toByteArray());
    }

    private void addOtherParams(Map<String, String> textParams, Map<String, ?> bizParams) throws Exception {
        //為null表示此處不是擴充套件此類引數的時機
        if (textParams != null) {
            for (Entry<String, String> pair : optionalTextParams.entrySet()) {
                if (!textParams.containsKey(pair.getKey())) {
                    textParams.put(pair.getKey(), pair.getValue());
                }
            }
            setNotifyUrl(textParams);
        }

        //為null表示此處不是擴充套件此類引數的時機
        if (bizParams != null) {
            for (Entry<String, Object> pair : optionalBizParams.entrySet()) {
                if (!bizParams.containsKey(pair.getKey())) {
                    ((Map<String, Object>) bizParams).put(pair.getKey(), pair.getValue());
                }
            }
        }
    }

    /**
     * 生成頁面類請求所需URL或Form表單
     *
     * @param method       GET或POST，決定是生成URL還是Form表單
     * @param systemParams 系統引數集合
     * @param bizParams    業務引數集合
     * @param textParams   其他額外文字引數集合
     * @param sign         所有引數的簽名值
     * @return 生成的URL字串或表單
     */
    public String generatePage(String method, java.util.Map<String, String> systemParams, java.util.Map<String, ?> bizParams,
                               java.util.Map<String, String> textParams, String sign) throws Exception {
        if (AlipayConstants.GET.equalsIgnoreCase(method)) {
            //採集並排序所有引數
            Map<String, String> sortedMap = getSortedMap(systemParams, bizParams, textParams);
            sortedMap.put(AlipayConstants.SIGN_FIELD, sign);

            //將所有引數置於URL中
            return getGatewayServerUrl() + "?" + buildQueryString(sortedMap);
        } else if (AlipayConstants.POST.equalsIgnoreCase(method)) {
            //將系統引數、額外文字引數排序後置於URL中
            Map<String, String> urlParams = getSortedMap(systemParams, null, textParams);
            urlParams.put(AlipayConstants.SIGN_FIELD, sign);
            String actionUrl = getGatewayServerUrl() + "?" + buildQueryString(urlParams);

            //將業務引數置於form表單中
            addOtherParams(null, bizParams);
            Map<String, String> formParams = new TreeMap<>();
            formParams.put(AlipayConstants.BIZ_CONTENT_FIELD, JsonUtil.toJsonString(bizParams));
            return PageUtil.buildForm(actionUrl, formParams);
        } else {
            throw new RuntimeException("_generatePage中method只支援傳入GET或POST");
        }
    }

    /**
     * 獲取商戶應用公鑰證書序列號，從證書模式執行時環境物件中直接讀取
     *
     * @return 商戶應用公鑰證書序列號
     */
    public String getMerchantCertSN() throws Exception {
        if (context.getCertEnvironment() == null) {
            return null;
        }
        return context.getCertEnvironment().getMerchantCertSN();
    }

    /**
     * 從響應Map中提取支付寶公鑰證書序列號
     *
     * @param respMap 響應Map
     * @return 支付寶公鑰證書序列號
     */
    public String getAlipayCertSN(java.util.Map<String, Object> respMap) throws Exception {
        return (String) respMap.get(AlipayConstants.ALIPAY_CERT_SN_FIELD);
    }

    /**
     * 獲取支付寶根證書序列號，從證書模式執行時環境物件中直接讀取
     *
     * @return 支付寶根證書序列號
     */
    public String getAlipayRootCertSN() throws Exception {
        if (context.getCertEnvironment() == null) {
            return null;
        }
        return context.getCertEnvironment().getRootCertSN();
    }

    /**
     * 是否是證書模式
     *
     * @return true：是；false：不是
     */
    public Boolean isCertMode() throws Exception {
        return context.getCertEnvironment() != null;
    }

    /**
     * 獲取支付寶公鑰，從證書執行時環境物件中直接讀取
     * 如果快取的使用者指定的支付寶公鑰證書的序列號與閘道器響應中攜帶的支付寶公鑰證書序列號不一致，需要報錯給出提示或自動更新支付寶公鑰證書
     *
     * @param alipayCertSN 閘道器響應中攜帶的支付寶公鑰證書序列號
     * @return 支付寶公鑰
     */
    public String extractAlipayPublicKey(String alipayCertSN) throws Exception {
        if (context.getCertEnvironment() == null) {
            return null;
        }
        return context.getCertEnvironment().getAlipayPublicKey(alipayCertSN);
    }

    /**
     * 驗證簽名
     *
     * @param respMap         響應Map，可以從中提取出sign和body
     * @param alipayPublicKey 支付寶公鑰
     * @return true：驗籤透過；false：驗籤不透過
     */
    public Boolean verify(java.util.Map<String, Object> respMap, String alipayPublicKey) throws Exception {
        String sign = (String) respMap.get(AlipayConstants.SIGN_FIELD);
        String content = SignContentExtractor.getSignSourceData((String) respMap.get(AlipayConstants.BODY_FIELD),
                (String) respMap.get(AlipayConstants.METHOD_FIELD));
        return Signer.verify(content, sign, alipayPublicKey);
    }

    /**
     * 計算簽名，注意要去除key或value為null的鍵值對
     *
     * @param systemParams       系統引數集合
     * @param bizParams          業務引數集合
     * @param textParams         其他額外文字引數集合
     * @param merchantPrivateKey 私鑰
     * @return 簽名值的Base64串
     */
    public String sign(java.util.Map<String, String> systemParams, java.util.Map<String, ?> bizParams,
                       java.util.Map<String, String> textParams, String merchantPrivateKey) throws Exception {
        Map<String, String> sortedMap = getSortedMap(systemParams, bizParams, textParams);

        StringBuilder content = new StringBuilder();
        int index = 0;
        for (Entry<String, String> pair : sortedMap.entrySet()) {
            if (!Strings.isNullOrEmpty(pair.getKey()) && !Strings.isNullOrEmpty(pair.getValue())) {
                content.append(index == 0 ? "" : "&").append(pair.getKey()).append("=").append(pair.getValue());
                index++;
            }
        }
        return context.getSigner().sign(content.toString(), merchantPrivateKey);
    }

    /**
     * 將隨機順序的Map轉換為有序的Map
     *
     * @param input 隨機順序的Map
     * @return 有序的Map
     */
    public java.util.Map<String, String> sortMap(java.util.Map<String, String> input) throws Exception {
        //GO語言的Map是隨機順序的，每次訪問順序都不同，才需排序
        return input;
    }

    /**
     * AES加密
     *
     * @param plainText 明文
     * @param key       金鑰
     * @return 密文
     */
    public String aesEncrypt(String plainText, String key) throws Exception {
        return AES.encrypt(plainText, key);
    }

    /**
     * AES解密
     *
     * @param cipherText 密文
     * @param key        金鑰
     * @return 明文
     */
    public String aesDecrypt(String cipherText, String key) throws Exception {
        return AES.decrypt(cipherText, key);
    }

    /**
     * 生成訂單串
     *
     * @param systemParams 系統引數集合
     * @param bizParams    業務引數集合
     * @param textParams   額外文字引數集合
     * @param sign         所有引數的簽名值
     * @return 訂單串
     */
    public String generateOrderString(java.util.Map<String, String> systemParams, java.util.Map<String, Object> bizParams,
                                      java.util.Map<String, String> textParams, String sign) throws Exception {
        //採集並排序所有引數
        Map<String, String> sortedMap = getSortedMap(systemParams, bizParams, textParams);
        sortedMap.put(AlipayConstants.SIGN_FIELD, sign);

        //將所有引數置於URL中
        return buildQueryString(sortedMap);
    }

    /**
     * 對支付類請求的非同步通知的引數集合進行驗籤
     *
     * @param parameters 引數集合
     * @param publicKey  支付寶公鑰
     * @return true：驗證成功；false：驗證失敗
     */
    public Boolean verifyParams(java.util.Map<String, String> parameters, String publicKey) throws Exception {
        return Signer.verifyParams(parameters, publicKey);
    }

    private Map<String, String> getSortedMap(Map<String, String> systemParams, Map<String, ?> bizParams,
                                             Map<String, String> textParams) throws Exception {
        addOtherParams(textParams, bizParams);

        Map<String, String> sortedMap = new TreeMap<>(systemParams);
        if (bizParams != null && !bizParams.isEmpty()) {
            sortedMap.put(AlipayConstants.BIZ_CONTENT_FIELD, JsonUtil.toJsonString(bizParams));
        }
        if (textParams != null) {
            sortedMap.putAll(textParams);
        }
        return sortedMap;
    }

    private void setNotifyUrl(Map<String, String> params) throws Exception {
        if (getConfig(AlipayConstants.NOTIFY_URL_CONFIG_KEY) != null && !params.containsKey(AlipayConstants.NOTIFY_URL_FIELD)) {
            params.put(AlipayConstants.NOTIFY_URL_FIELD, getConfig(AlipayConstants.NOTIFY_URL_CONFIG_KEY));
        }
    }

    /**
     * 字串拼接
     *
     * @param a 字串a
     * @param b 字串b
     * @return 字串a和b拼接後的字串
     */
    public String concatStr(String a, String b) {
        return a + b;
    }

    private String buildQueryString(Map<String, String> sortedMap) throws UnsupportedEncodingException {
        StringBuilder content = new StringBuilder();
        int index = 0;
        for (Entry<String, String> pair : sortedMap.entrySet()) {
            if (!Strings.isNullOrEmpty(pair.getKey()) && !Strings.isNullOrEmpty(pair.getValue())) {
                content.append(index == 0 ? "" : "&")
                        .append(pair.getKey())
                        .append("=")
                        .append(URLEncoder.encode(pair.getValue(), AlipayConstants.DEFAULT_CHARSET.name()));
                index++;
            }
        }
        return content.toString();
    }

    private String getGatewayServerUrl() throws Exception {
        return getConfig(AlipayConstants.PROTOCOL_CONFIG_KEY) + "://" + getConfig(AlipayConstants.HOST_CONFIG_KEY) + "/gateway.do";
    }
}
