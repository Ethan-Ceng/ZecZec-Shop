<?php

namespace Alipay\EasySDK\Kernel\Util;

use Alipay\EasySDK\Kernel\AlipayConstants;

class SignContentExtractor
{
    private $RESPONSE_SUFFIX = "_response";
    private $ERROR_RESPONSE = "error_response";

    /**
     * @param $body    string 閘道器的整體響應字串
     * @param $method  string 本次呼叫的OpenAPI介面名稱
     * @return false|string|null   待驗籤的原文
     */
    public function getSignSourceData($body, $method)
    {
        $rootNodeName = str_replace(".", "_", $method) . $this->RESPONSE_SUFFIX;
        $rootIndex = strpos($body, $rootNodeName);
        if ($rootIndex !== strrpos($body, $rootNodeName)) {
            throw new \Exception('檢測到響應報文中有重複的' . $rootNodeName . ',驗籤失敗。');
        }
        $errorIndex = strpos($body, $this->ERROR_RESPONSE);
        if ($rootIndex > 0) {
            return $this->parserJSONSource($body, $rootNodeName, $rootIndex);
        } else if ($errorIndex > 0) {
            return $this->parserJSONSource($body, $this->ERROR_RESPONSE, $errorIndex);
        } else {
            return null;
        }
    }

    /**
     *
     * @param $responseContent
     * @param $nodeName
     * @param $nodeIndex
     * @return false|string|null
     */
    function parserJSONSource($responseContent, $nodeName, $nodeIndex)
    {
        $signDataStartIndex = $nodeIndex + strlen($nodeName) + 2;
        if (strrpos($responseContent, AlipayConstants::ALIPAY_CERT_SN_FIELD)) {
            $signIndex = strrpos($responseContent, "\"" . AlipayConstants::ALIPAY_CERT_SN_FIELD . "\"");
        } else {
            $signIndex = strrpos($responseContent, "\"" . AlipayConstants::SIGN_FIELD . "\"");
        }
        // 簽名前-逗號
        $signDataEndIndex = $signIndex - 1;
        $indexLen = $signDataEndIndex - $signDataStartIndex;
        if ($indexLen < 0) {
            return null;
        }
        return substr($responseContent, $signDataStartIndex, $indexLen);
    }

}