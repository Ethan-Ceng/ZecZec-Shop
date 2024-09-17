// This file is auto-generated, don't edit it. Thanks.
package com.alipay.easysdk.payment.app;

import com.aliyun.tea.*;
import com.alipay.easysdk.payment.app.models.*;

public class Client {

    public com.alipay.easysdk.kernel.Client _kernel;
    public Client(com.alipay.easysdk.kernel.Client kernel) throws Exception {
        this._kernel = kernel;
    }


    public AlipayTradeAppPayResponse pay(String subject, String outTradeNo, String totalAmount) throws Exception {
        java.util.Map<String, String> systemParams = TeaConverter.buildMap(
            new TeaPair("method", "alipay.trade.app.pay"),
            new TeaPair("app_id", _kernel.getConfig("appId")),
            new TeaPair("timestamp", _kernel.getTimestamp()),
            new TeaPair("format", "json"),
            new TeaPair("version", "1.0"),
            new TeaPair("alipay_sdk", _kernel.getSdkVersion()),
            new TeaPair("charset", "UTF-8"),
            new TeaPair("sign_type", _kernel.getConfig("signType")),
            new TeaPair("app_cert_sn", _kernel.getMerchantCertSN()),
            new TeaPair("alipay_root_cert_sn", _kernel.getAlipayRootCertSN())
        );
        java.util.Map<String, Object> bizParams = TeaConverter.buildMap(
            new TeaPair("subject", subject),
            new TeaPair("out_trade_no", outTradeNo),
            new TeaPair("total_amount", totalAmount)
        );
        java.util.Map<String, String> textParams = new java.util.HashMap<>();
        String sign = _kernel.sign(systemParams, bizParams, textParams, _kernel.getConfig("merchantPrivateKey"));
        java.util.Map<String, String> response = TeaConverter.buildMap(
            new TeaPair("body", _kernel.generateOrderString(systemParams, bizParams, textParams, sign))
        );
        return TeaModel.toModel(response, new AlipayTradeAppPayResponse());
    }
    
    /**
     * ISV代商戶代用，指定appAuthToken
     *
     * @param appAuthToken 代呼叫token
     * @return 本客戶端，便於鏈式呼叫
     */
    public Client agent(String appAuthToken) {
        _kernel.injectTextParam("app_auth_token", appAuthToken);
        return this;
    }

    /**
     * 使用者授權呼叫，指定authToken
     *
     * @param authToken 使用者授權token
     * @return 本客戶端，便於鏈式呼叫
     */
    public Client auth(String authToken) {
        _kernel.injectTextParam("auth_token", authToken);
        return this;
    }

    /**
     * 設定非同步通知回撥地址，此處設定將在本呼叫中覆蓋Config中的全域性配置
     *
     * @param url 非同步通知回撥地址，例如：https://www.test.com/callback
     * @return 本客戶端，便於鏈式呼叫
     */
    public Client asyncNotify(String url) {
        _kernel.injectTextParam("notify_url", url);
        return this;
    }

    /**
     * 將本次呼叫強制路由到後端系統的測試地址上，常用於線下環境內外聯調，沙箱與線上環境設定無效
     *
     * @param testUrl 後端系統測試地址
     * @return 本客戶端，便於鏈式呼叫
     */
    public Client route(String testUrl) {
        _kernel.injectTextParam("ws_service_url", testUrl);
        return this;
    }

    /**
     * 設定API入參中沒有的其他可選業務請求引數(biz_content下的欄位)
     *
     * @param key   業務請求引數名稱（biz_content下的欄位名，比如timeout_express）
     * @param value 業務請求引數的值，一個可以序列化成JSON的物件
     *              如果該欄位是一個字串型別（String、Price、Date在SDK中都是字串），請使用String儲存
     *              如果該欄位是一個數值型型別（比如：Number），請使用Long儲存
     *              如果該欄位是一個複雜型別，請使用巢狀的Map指定各下級欄位的值
     *              如果該欄位是一個數組，請使用List儲存各個值
     *              對於更復雜的情況，也支援Map和List的各種組合巢狀，比如引數是值是個List，List中的每種型別是一個複雜物件
     * @return 本客戶端，便於鏈式呼叫
     */
    public Client optional(String key, Object value) {
        _kernel.injectBizParam(key, value);
        return this;
    }

    /**
     * 批次設定API入參中沒有的其他可選業務請求引數(biz_content下的欄位)
     * optional方法的批次版本
     *
     * @param optionalArgs 可選引數集合，每個引數由key和value組成，key和value的格式請參見optional方法的註釋
     * @return 本客戶端，便於鏈式呼叫
     */
    public Client batchOptional(java.util.Map<String, Object> optionalArgs) {
        for (java.util.Map.Entry<String, Object> pair : optionalArgs.entrySet()) {
            _kernel.injectBizParam(pair.getKey(), pair.getValue());
        }
        return this;
    }
}