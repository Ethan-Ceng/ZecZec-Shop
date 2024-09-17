<?php
namespace app\common\library\sms\package\qcloud;

/**
 * 傳送Util類
 *
 */
class SmsSenderUtil
{
    /**
     * 生成隨機數
     *
     * @return int 隨機數結果
     */
    public function getRandom()
    {
        return rand(100000, 999999);
    }

    /**
     * 生成簽名
     *
     * @param string $appkey        sdkappid對應的appkey
     * @param string $random        隨機正整數
     * @param string $curTime       當前時間
     * @param array  $phoneNumbers  手機號碼
     * @return string  簽名結果
     */
    public function calculateSig($appkey, $random, $curTime, $phoneNumbers)
    {
        $phoneNumbersString = $phoneNumbers[0];
        for ($i = 1; $i < count($phoneNumbers); $i++) {
            $phoneNumbersString .= ("," . $phoneNumbers[$i]);
        }

        return hash("sha256", "appkey=".$appkey."&random=".$random
            ."&time=".$curTime."&mobile=".$phoneNumbersString);
    }

    /**
     * 生成簽名
     *
     * @param string $appkey        sdkappid對應的appkey
     * @param string $random        隨機正整數
     * @param string $curTime       當前時間
     * @param array  $phoneNumbers  手機號碼
     * @return string  簽名結果
     */
    public function calculateSigForTemplAndPhoneNumbers($appkey, $random,
                                                        $curTime, $phoneNumbers)
    {
        $phoneNumbersString = $phoneNumbers[0];
        for ($i = 1; $i < count($phoneNumbers); $i++) {
            $phoneNumbersString .= ("," . $phoneNumbers[$i]);
        }

        return hash("sha256", "appkey=".$appkey."&random=".$random
            ."&time=".$curTime."&mobile=".$phoneNumbersString);
    }

    public function phoneNumbersToArray($nationCode, $phoneNumbers)
    {
        $i = 0;
        $tel = array();
        do {
            $telElement = new \stdClass();
            $telElement->nationcode = $nationCode;
            $telElement->mobile = $phoneNumbers[$i];
            array_push($tel, $telElement);
        } while (++$i < count($phoneNumbers));

        return $tel;
    }

    /**
     * 生成簽名
     *
     * @param string $appkey        sdkappid對應的appkey
     * @param string $random        隨機正整數
     * @param string $curTime       當前時間
     * @param array  $phoneNumber   手機號碼
     * @return string  簽名結果
     */
    public function calculateSigForTempl($appkey, $random, $curTime, $phoneNumber)
    {
        $phoneNumbers = array($phoneNumber);

        return $this->calculateSigForTemplAndPhoneNumbers($appkey, $random,
            $curTime, $phoneNumbers);
    }

    /**
     * 生成簽名
     *
     * @param string $appkey        sdkappid對應的appkey
     * @param string $random        隨機正整數
     * @param string $curTime       當前時間
     * @return string 簽名結果
     */
    public function calculateSigForPuller($appkey, $random, $curTime)
    {
        return hash("sha256", "appkey=".$appkey."&random=".$random
            ."&time=".$curTime);
    }

    /**
     * 生成上傳檔案授權
     *
     * @param string $appkey        sdkappid對應的appkey
     * @param string $random        隨機正整數
     * @param string $curTime       當前時間
     * @param array  $fileSha1Sum   檔案sha1sum
     * @return string  授權結果
     */
    public function calculateAuth($appkey, $random, $curTime, $fileSha1Sum)
    {
        return hash("sha256", "appkey=".$appkey."&random=".$random
            ."&time=".$curTime."&content-sha1=".$fileSha1Sum);
    }

    /**
     * 生成sha1sum
     *
     * @param string $content  內容
     * @return string  內容sha1雜湊值
     */
    public function sha1sum($content)
    {
        return hash("sha1", $content);
    }

    /**
     * 傳送請求
     *
     * @param string $url      請求地址
     * @param array  $dataObj  請求內容
     * @return string 應答json字串
     */
    public function sendCurlPost($url, $dataObj)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataObj));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $ret = curl_exec($curl);
        if (false == $ret) {
            // curl_exec failed
            $result = "{ \"result\":" . -2 . ",\"errmsg\":\"" . curl_error($curl) . "\"}";
        } else {
            $rsp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "{ \"result\":" . -1 . ",\"errmsg\":\"". $rsp
                    . " " . curl_error($curl) ."\"}";
            } else {
                $result = $ret;
            }
        }

        curl_close($curl);

        return $result;
    }

    /**
     * 傳送請求
     *
     * @param string $req  請求物件
     * @return string 應答json字串
     */
    public function fetch($req)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $req->url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $req->headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $req->body);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);

        if (false == $result) {
            // curl_exec failed
            $result = "{ \"result\":" . -2 . ",\"errmsg\":\"" . curl_error($curl) . "\"}";
        } else {
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if (200 != $code) {
                $result = "{ \"result\":" . -1 . ",\"errmsg\":\"". $code
                    . " " . curl_error($curl) ."\"}";
            }
        }
        curl_close($curl);

        return $result;
    }
}