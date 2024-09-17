<?php

// This file is auto-generated, don't edit it. Thanks.
namespace Alipay\EasySDK\Security\TextRisk;

use Alipay\EasySDK\Kernel\EasySDKKernel;
use AlibabaCloud\Tea\Tea;
use AlibabaCloud\Tea\Request;
use AlibabaCloud\Tea\Exception\TeaError;
use \Exception;
use AlibabaCloud\Tea\Exception\TeaUnableRetryError;

use Alipay\EasySDK\Security\TextRisk\Models\AlipaySecurityRiskContentDetectResponse;
use AlibabaCloud\Tea\Response;

class Client {
    protected $_kernel;

    public function __construct($kernel){
        $this->_kernel = $kernel;
    }

    /**
     * @param string $content
     * @return AlipaySecurityRiskContentDetectResponse
     * @throws TeaError
     * @throws Exception
     * @throws TeaUnableRetryError
     */
    public function detect($content){
        $_runtime = [
            "ignoreSSL" => $this->_kernel->getConfig("ignoreSSL"),
            "httpProxy" => $this->_kernel->getConfig("httpProxy"),
            "connectTimeout" => 15000,
            "readTimeout" => 15000,
            "retry" => [
                "maxAttempts" => 0
            ]
        ];
        $_lastRequest = null;
        $_lastException = null;
        $_now = time();
        $_retryTimes = 0;
        while (Tea::allowRetry(@$_runtime["retry"], $_retryTimes, $_now)) {
            if ($_retryTimes > 0) {
                $_backoffTime = Tea::getBackoffTime(@$_runtime["backoff"], $_retryTimes);
                if ($_backoffTime > 0) {
                    Tea::sleep($_backoffTime);
                }
            }
            $_retryTimes = $_retryTimes + 1;
            try {
                $_request = new Request();
                $systemParams = [
                    "method" => "alipay.security.risk.content.detect",
                    "app_id" => $this->_kernel->getConfig("appId"),
                    "timestamp" => $this->_kernel->getTimestamp(),
                    "format" => "json",
                    "version" => "1.0",
                    "alipay_sdk" => $this->_kernel->getSdkVersion(),
                    "charset" => "UTF-8",
                    "sign_type" => $this->_kernel->getConfig("signType"),
                    "app_cert_sn" => $this->_kernel->getMerchantCertSN(),
                    "alipay_root_cert_sn" => $this->_kernel->getAlipayRootCertSN()
                ];
                $bizParams = [
                    "content" => $content
                ];
                $textParams = [];
                $_request->protocol = $this->_kernel->getConfig("protocol");
                $_request->method = "POST";
                $_request->pathname = "/gateway.do";
                $_request->headers = [
                    "host" => $this->_kernel->getConfig("gatewayHost"),
                    "content-type" => "application/x-www-form-urlencoded;charset=utf-8"
                ];
                $_request->query = $this->_kernel->sortMap(Tea::merge([
                    "sign" => $this->_kernel->sign($systemParams, $bizParams, $textParams, $this->_kernel->getConfig("merchantPrivateKey"))
                ], $systemParams, $textParams));
                $_request->body = $this->_kernel->toUrlEncodedRequestBody($bizParams);
                $_lastRequest = $_request;
                $_response= Tea::send($_request, $_runtime);
                $respMap = $this->_kernel->readAsJson($_response, "alipay.security.risk.content.detect");
                if ($this->_kernel->isCertMode()) {
                    if ($this->_kernel->verify($respMap, $this->_kernel->extractAlipayPublicKey($this->_kernel->getAlipayCertSN($respMap)))) {
                        return AlipaySecurityRiskContentDetectResponse::fromMap($this->_kernel->toRespModel($respMap));
                    }
                }
                else {
                    if ($this->_kernel->verify($respMap, $this->_kernel->getConfig("alipayPublicKey"))) {
                        return AlipaySecurityRiskContentDetectResponse::fromMap($this->_kernel->toRespModel($respMap));
                    }
                }
                throw new TeaError([
                    "message" => "驗籤失敗，請檢查支付寶公鑰設定是否正確。"
                ]);
            }
            catch (Exception $e) {
                if (!($e instanceof TeaError)) {
                    $e = new TeaError([], $e->getMessage(), $e->getCode(), $e);
                }
                if (Tea::isRetryable($e)) {
                    $_lastException = $e;
                    continue;
                }
                throw $e;
            }
        }
        throw new TeaUnableRetryError($_lastRequest, $_lastException);
    }

    /**
     * ISV代商戶代用，指定appAuthToken
     *
     * @param $appAuthToken String 代呼叫token
     * @return $this 本客戶端，便於鏈式呼叫
     */
    public function agent($appAuthToken)
    {
        $this->_kernel->injectTextParam("app_auth_token", $appAuthToken);
        return $this;
    }

    /**
    * 使用者授權呼叫，指定authToken
    *
    * @param $authToken String 使用者授權token
    * @return $this
    */
    public function auth($authToken)
    {
        $this->_kernel->injectTextParam("auth_token", $authToken);
        return $this;
    }

    /**
    * 設定非同步通知回撥地址，此處設定將在本呼叫中覆蓋Config中的全域性配置
    *
    * @param $url String 非同步通知回撥地址，例如：https://www.test.com/callback
    * @return $this
    */
    public function asyncNotify($url)
    {
        $this->_kernel->injectTextParam("notify_url", $url);
        return $this;
    }

    /**
    * 將本次呼叫強制路由到後端系統的測試地址上，常用於線下環境內外聯調，沙箱與線上環境設定無效
    *
    * @param $testUrl String 後端系統測試地址
    * @return $this
    */
    public function route($testUrl)
    {
        $this->_kernel->injectTextParam("ws_service_url", $testUrl);
        return $this;
    }

    /**
    * 設定API入參中沒有的其他可選業務請求引數(biz_content下的欄位)
    *
    * @param $key   String 業務請求引數名稱（biz_content下的欄位名，比如timeout_express）
    * @param $value object 業務請求引數的值，一個可以序列化成JSON的物件
    *               如果該欄位是一個字串型別（String、Price、Date在SDK中都是字串），請使用String儲存
    *               如果該欄位是一個數值型型別（比如：Number），請使用Long儲存
    *               如果該欄位是一個複雜型別，請使用巢狀的array指定各下級欄位的值
    *               如果該欄位是一個數組，請使用array儲存各個值
    * @return $this
    */
    public function optional($key, $value)
    {
        $this->_kernel->injectBizParam($key, $value);
        return $this;
    }

    /**
    * 批次設定API入參中沒有的其他可選業務請求引數(biz_content下的欄位)
    * optional方法的批次版本
    *
    * @param $optionalArgs array 可選引數集合，每個引數由key和value組成，key和value的格式請參見optional方法的註釋
    * @return $this
    */
    public function batchOptional($optionalArgs)
    {
        foreach ($optionalArgs as $key => $value) {
            $this->_kernel->injectBizParam($key, $value);
        }
        return $this;
    }

}