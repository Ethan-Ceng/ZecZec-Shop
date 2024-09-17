<?php


namespace Alipay\EasySDK\Kernel;

use Alipay\EasySDK\Kernel\Util\AES;
use Alipay\EasySDK\Kernel\Util\JsonUtil;
use Alipay\EasySDK\Kernel\Util\PageUtil;
use Alipay\EasySDK\Kernel\Util\SignContentExtractor;
use Alipay\EasySDK\Kernel\Util\Signer;
use AlibabaCloud\Tea\FileForm\FileForm;
use AlibabaCloud\Tea\FileForm\FileForm\FileField;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;

class EasySDKKernel
{

    private $config;

    private $optionalTextParams;

    private $optionalBizParams;

    private $textParams;

    private $bizParams;


    public function __construct($config)
    {
        $this->config = $config;
    }

    public function injectTextParam($key, $value)
    {
        if ($key != null) {
            $this->optionalTextParams[$key] = $value;
        }
    }

    public function injectBizParam($key, $value)
    {
        if ($key != null) {
            $this->optionalBizParams[$key] = $value;
        }
    }

    /**
     * 獲取時間戳，格式yyyy-MM-dd HH:mm:ss
     * @return false|string 當前時間戳
     */
    public function getTimestamp()
    {
        return date("Y-m-d H:i:s");
    }

    public function getConfig($key)
    {
        return $this->config->$key;
    }

    public function getSdkVersion()
    {
        return AlipayConstants::SDK_VERSION;
    }

    /**
     * 將業務引數和其他額外文字引數按www-form-urlencoded格式轉換成HTTP Body中的位元組陣列，注意要做URL Encode
     *
     * @param $bizParams array 業務引數
     * @return false|string|null
     */
    public function toUrlEncodedRequestBody($bizParams)
    {
        $sortedMap = $this->getSortedMap(null, $bizParams, null);
        if (empty($sortedMap)) {
            return null;
        }
        return $this->buildQueryString($sortedMap);
    }

    /**
     * 解析閘道器響應內容，同時將API的介面名稱和響應原文插入到響應陣列的method和body欄位中
     *
     * @param $response ResponseInterface HTTP響應
     * @param $method   string 呼叫的OpenAPI的介面名稱
     * @return array    響應的結果
     */
    public function readAsJson($response, $method)
    {
        $responseBody = (string)$response->getBody();
        $map = [];
        $map[AlipayConstants::BODY_FIELD] = $responseBody;
        $map[AlipayConstants::METHOD_FIELD] = $method;
        return $map;
    }

    /**
     * 生成隨機分界符，用於multipart格式的HTTP請求Body的多個欄位間的分隔
     *
     * @return string 隨機分界符
     */
    public function getRandomBoundary()
    {
        return date("Y-m-d H:i:s") . '';
    }

    /**
     * 將其他額外文字引數和檔案引數按multipart/form-data格式轉換成HTTP Body中
     * @param $textParams
     * @param $fileParams
     * @param $boundary
     * @return false|string
     */
    public function toMultipartRequestBody($textParams, $fileParams, $boundary)
    {
        $this->textParams = $textParams;
        if ($textParams != null && $this->optionalTextParams != null) {
            $this->textParams = array_merge($textParams, $this->optionalTextParams);
        } else if ($textParams == null) {
            $this->textParams = $this->optionalTextParams;
        }
        if (count($fileParams) > 0) {

            foreach ($fileParams as $key => $value) {
                $fileField = new FileField();
                $fileField->filename = $value;
                $fileField->contentType = 'multipart/form-data;charset=utf-8;boundary=' . $boundary;
                $fileField->content = new Stream(fopen($value, 'r'));
                $this->textParams[$key] = $fileField;
            }
        }
        $stream = FileForm::toFileForm($this->textParams, $boundary);

//        do {
//            $readLength = $stream->read(1024);
//        } while (0 != $readLength);
        return $stream;
    }

    /**
     * 生成頁面類請求所需URL或Form表單
     * @param $method
     * @param $systemParams
     * @param $bizParams
     * @param $textParams
     * @param $sign
     * @return string
     * @throws \Exception
     */
    public function generatePage($method, $systemParams, $bizParams, $textParams, $sign)
    {
        if ($method == AlipayConstants::GET) {
            //採集並排序所有引數
            $sortedMap = $this->getSortedMap($systemParams, $bizParams, $textParams);
            $sortedMap[AlipayConstants::SIGN_FIELD] = $sign;
            return $this->getGatewayServerUrl() . '?' . $this->buildQueryString($sortedMap);
        } elseif ($method == AlipayConstants::POST) {
            //採集並排序所有引數
            $sortedMap = $this->getSortedMap($systemParams, $this->bizParams, $this->textParams);
            $sortedMap[AlipayConstants::SIGN_FIELD] = $sign;
            $pageUtil = new PageUtil();
            return $pageUtil->buildForm($this->getGatewayServerUrl(), $sortedMap);
        } else {
            throw new \Exception("不支援" . $method);
        }
    }

    /**
     *  獲取商戶應用公鑰證書序列號，從證書模式執行時環境物件中直接讀取
     *
     * @return mixed 商戶應用公鑰證書序列號
     */
    public function getMerchantCertSN()
    {
        return $this->config->merchantCertSN;
    }

    /**
     * 從響應Map中提取支付寶公鑰證書序列號
     *
     * @param array $respMap string 響應Map
     * @return mixed   支付寶公鑰證書序列號
     */
    public function getAlipayCertSN(array $respMap)
    {
        if (!empty($this->config->merchantCertSN)) {
            $body = json_decode($respMap[AlipayConstants::BODY_FIELD]);
            $alipayCertSN = $body->alipay_cert_sn;
            return $alipayCertSN;
        }
    }

    /**
     * 獲取支付寶根證書序列號，從證書模式執行時環境物件中直接讀取
     *
     * @return mixed 支付寶根證書序列號
     */
    public function getAlipayRootCertSN()
    {
        return $this->config->alipayRootCertSN;
    }

    /**
     * 是否是證書模式
     * @return mixed true：是；false：不是
     */
    public function isCertMode()
    {
        return $this->config->merchantCertSN;
    }

    public function extractAlipayPublicKey($alipayCertSN)
    {
        // PHP 版本只儲存一個版本支付寶公鑰
        return $this->config->alipayPublicKey;
    }

    /**
     * 驗證簽名
     *
     * @param $respMap  string 響應內容，可以從中提取出sign和body
     * @param $alipayPublicKey string 支付寶公鑰
     * @return bool  true：驗籤透過；false：驗籤不透過
     * @throws \Exception
     */
    public function verify($respMap, $alipayPublicKey)
    {
        $resp = json_decode($respMap[AlipayConstants::BODY_FIELD], true);
        $sign = $resp[AlipayConstants::SIGN_FIELD];
        $signContentExtractor = new SignContentExtractor();
        $content = $signContentExtractor->getSignSourceData($respMap[AlipayConstants::BODY_FIELD],
            $respMap[AlipayConstants::METHOD_FIELD]);
        $signer = new Signer();
        return $signer->verify($content, $sign, $alipayPublicKey);
    }

    /**
     * 計算簽名，注意要去除key或value為null的鍵值對
     *
     * @param $systemParams   array 系統引數集合
     * @param $bizParams      array 業務引數集合
     * @param $textParams     array 其他額外文字引數集合
     * @param $privateKey     string 私鑰
     * @return string 簽名值的Base64串
     */
    public function sign($systemParams, $bizParams, $textParams, $privateKey)
    {
        $sortedMap = $this->getSortedMap($systemParams, $bizParams, $textParams);
        $data = $this->getSignContent($sortedMap);
        $sign = new Signer();
        return $sign->sign($data, $privateKey);
    }

    /**
     * AES加密
     * @param $content
     * @param $encryptKey
     * @return string
     * @throws \Exception
     */
    public function aesEncrypt($content, $encryptKey)
    {
        $aes = new AES();
        return $aes->aesEncrypt($content, $encryptKey);
    }

    /**
     * AES解密
     * @param $content
     * @param $encryptKey
     * @return false|string
     * @throws \Exception
     */
    public function aesDecrypt($content, $encryptKey)
    {
        $aes = new AES();
        return $aes->aesDecrypt($content, $encryptKey);
    }

    /**
     * 生成sdkExecute類請求所需URL
     *
     * @param $systemParams
     * @param $bizParams
     * @param $textParams
     * @param $sign
     * @return string
     */
    public function generateOrderString($systemParams, $bizParams, $textParams, $sign)
    {
        //採集並排序所有引數
        $sortedMap = $this->getSortedMap($systemParams, $bizParams, $textParams);
        $sortedMap[AlipayConstants::SIGN_FIELD] = $sign;
        return http_build_query($sortedMap);
    }

    public function sortMap($randomMap)
    {
        return $randomMap;
    }


    /**
     *  從響應Map中提取返回值物件的Map，並將響應原文插入到body欄位中
     *
     * @param $respMap  string 響應內容
     * @return mixed
     */
    public function toRespModel($respMap)
    {
        $body = $respMap[AlipayConstants::BODY_FIELD];
        $methodName = $respMap[AlipayConstants::METHOD_FIELD];
        $responseNodeName = str_replace(".", "_", $methodName) . AlipayConstants::RESPONSE_SUFFIX;

        $model = json_decode($body, true);
        if (strpos($body, AlipayConstants::ERROR_RESPONSE)) {
            $result = $model[AlipayConstants::ERROR_RESPONSE];
            $result[AlipayConstants::BODY_FIELD] = $body;
        } else {
            $result = $model[$responseNodeName];
            $result[AlipayConstants::BODY_FIELD] = $body;
        }
        return $result;
    }

    public function verifyParams($parameters, $publicKey)
    {
        $sign = new Signer();
        return $sign->verifyParams($parameters, $publicKey);
    }

    /**
     * 字串拼接
     *
     * @param $a
     * @param $b
     * @return string 字串a和b拼接後的字串
     */
    public function concatStr($a, $b)
    {
        return $a . $b;
    }


    private function buildQueryString(array $sortedMap)
    {
        $requestUrl = null;
        foreach ($sortedMap as $sysParamKey => $sysParamValue) {
            $requestUrl .= "$sysParamKey=" . urlencode($this->characet($sysParamValue, AlipayConstants::DEFAULT_CHARSET)) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);
        return $requestUrl;

    }

    private function getSortedMap($systemParams, $bizParams, $textParams)
    {
        $this->textParams = $textParams;
        $this->bizParams = $bizParams;
        if ($textParams != null && $this->optionalTextParams != null) {
            $this->textParams = array_merge($textParams, $this->optionalTextParams);
        } else if ($textParams == null) {
            $this->textParams = $this->optionalTextParams;
        }
        if ($bizParams != null && $this->optionalBizParams != null) {
            $this->bizParams = array_merge($bizParams, $this->optionalBizParams);
        } else if ($bizParams == null) {
            $this->bizParams = $this->optionalBizParams;
        }
        $json = new JsonUtil();
        if ($this->bizParams != null) {
            $bizParams = $json->toJsonString($this->bizParams);
        }
        $sortedMap = $systemParams;
        if (!empty($bizParams)) {
            $sortedMap[AlipayConstants::BIZ_CONTENT_FIELD] = json_encode($bizParams, JSON_UNESCAPED_UNICODE);
        }
        if (!empty($this->textParams)) {
            if (!empty($sortedMap)) {
                $sortedMap = array_merge($sortedMap, $this->textParams);
            } else {
                $sortedMap = $this->textParams;
            }
        }
        if ($this->getConfig(AlipayConstants::NOTIFY_URL_CONFIG_KEY) != null) {
            $sortedMap[AlipayConstants::NOTIFY_URL_FIELD] = $this->getConfig(AlipayConstants::NOTIFY_URL_CONFIG_KEY);
        }
        return $sortedMap;
    }

    /**
     * 獲取簽名字串
     *
     * @param $params
     * @return string
     */
    private function getSignContent($params)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 轉換成目標字元集
                $v = $this->characet($v, AlipayConstants::DEFAULT_CHARSET);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    private function setNotifyUrl($params)
    {
        if ($this->config(AlipayConstants::NOTIFY_URL_CONFIG_KEY) != null && $params(AlipayConstants::NOTIFY_URL_CONFIG_KEY) == null) {
            $params[AlipayConstants::NOTIFY_URL_CONFIG_KEY] = $this->config(AlipayConstants::NOTIFY_URL_CONFIG_KEY);
        }
    }

    private function getGatewayServerUrl()
    {
        return $this->getConfig(AlipayConstants::PROTOCOL_CONFIG_KEY) . '://' . $this->getConfig(AlipayConstants::HOST_CONFIG_KEY) . '/gateway.do';
    }

    /**
     * 校驗$value是否非空
     *
     * @param $value
     * @return bool if not set ,return true;if is null , return true;
     */
    function checkEmpty($value)
    {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }

    /**
     * 轉換字元集編碼
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = AlipayConstants::DEFAULT_CHARSET;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }

}