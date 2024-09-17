<?php

namespace app\common\library\easywechat\wx;


/**
 * 微信api基類
 */
class WxBase
{
    protected $app;

    protected $error;

    /**
     * 建構函式
     */
    public function __construct($app = null)
    {
        $this->app = $app;
    }

    /**
     * 寫入日誌記錄
     */
    protected function doLogs($values)
    {
        return log_write($values);
    }


    /**
     * 模擬GET請求 HTTPS的頁面
     * @param string $url 請求地址
     * @return string $result
     */
    protected function get($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // https請求 不驗證證書和hosts
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 模擬POST請求
     * @param $url
     * @param array $data
     * @param bool $useCert
     * @param array $sslCert
     * @return mixed
     */
    protected function post($url, $data = [], $useCert = false, $sslCert = [])
    {
        $header = [
            'Content-type: application/json;'
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        if ($useCert == true) {
            // 設定證書：cert 與 key 分別屬於兩個.pem檔案
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLCERT, $sslCert['certPem']);
            curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLKEY, $sslCert['keyPem']);
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 陣列轉json
     * @param $data
     * @return string
     */
    protected function jsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * json轉陣列
     * @param $json
     * @return mixed
     */
    protected function jsonDecode($json)
    {
        return json_decode($json, true);
    }

    /**
     * 返回錯誤資訊
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

}
