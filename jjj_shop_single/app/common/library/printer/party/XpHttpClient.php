<?php
/**
 * 傳送http的json請求
 *
 * @param $url 請求url
 * @param $jsonStr 傳送的json字串
 * @return array
 */

namespace app\common\library\printer\party;

use Exception;

class XpHttpClient
{
    public function post($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);// 傳送一個常規的Post請求
        curl_setopt($ch, CURLOPT_URL, $url);// 要訪問的地址
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 對認證證書來源的檢測
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 設定超時限制防止死循
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);

        return $response;
    }
}

?>