<?php

namespace app\common\library\sms\package\qcloud;

use app\common\library\sms\package\qcloud\SmsSenderUtil;

/**
 * 單發簡訊類
 *
 */
class SmsSingleSender
{
    private $url;
    private $appid;
    private $appkey;
    private $util;

    /**
     * 建構函式
     *
     * @param string $appid sdkappid
     * @param string $appkey sdkappid對應的appkey
     */
    public function __construct($appid, $appkey)
    {
        $this->url = "https://yun.tim.qq.com/v5/tlssmssvr/sendsms";
        $this->appid = $appid;
        $this->appkey = $appkey;
        $this->util = new SmsSenderUtil();
    }

    /**
     * 普通單發
     *
     * 普通單發需明確指定內容，如果有多個簽名，請在內容中以【】的方式新增到資訊內容中，否則系統將使用預設簽名。
     *
     * @param int $type 簡訊型別，0 為普通簡訊，1 營銷簡訊
     * @param string $nationCode 國家碼，如 86 為中國
     * @param string $phoneNumber 不帶國家碼的手機號
     * @param string $msg 資訊內容，必須與申請的模板格式一致，否則將返回錯誤
     * @param string $extend 擴充套件碼，可填空串
     * @param string $ext 服務端原樣返回的引數，可填空串
     * @return string 應答json字串，詳細內容參見騰訊雲協議文件
     */
    public function send($type, $nationCode, $phoneNumber, $msg, $extend = "", $ext = "")
    {
        $random = $this->util->getRandom();
        $curTime = time();
        $wholeUrl = $this->url . "?sdkappid=" . $this->appid . "&random=" . $random;

        // 按照協議組織 post 包體
        $data = new \stdClass();
        $tel = new \stdClass();
        $tel->nationcode = "" . $nationCode;
        $tel->mobile = "" . $phoneNumber;

        $data->tel = $tel;
        $data->type = (int)$type;
        $data->msg = $msg;
        $data->sig = hash("sha256",
            "appkey=" . $this->appkey . "&random=" . $random . "&time="
            . $curTime . "&mobile=" . $phoneNumber, FALSE);
        $data->time = $curTime;
        $data->extend = $extend;
        $data->ext = $ext;

        return $this->util->sendCurlPost($wholeUrl, $data);
    }

    /**
     * 指定模板單發
     *
     * @param string $nationCode 國家碼，如 86 為中國
     * @param string $phoneNumber 不帶國家碼的手機號
     * @param int $templId 模板 id
     * @param array $params 模板引數列表，如模板 {1}...{2}...{3}，那麼需要帶三個引數
     * @param string $sign 簽名，如果填空串，系統會使用預設簽名
     * @param string $extend 擴充套件碼，可填空串
     * @param string $ext 服務端原樣返回的引數，可填空串
     * @return string 應答json字串，詳細內容參見騰訊雲協議文件
     */
    public function sendWithParam($nationCode, $phoneNumber, $templId, $params,
                                  $sign, $extend, $ext = "")
    {
        $random = $this->util->getRandom();
        $curTime = time();
        $wholeUrl = $this->url . "?sdkappid=" . $this->appid . "&random=" . $random;

        // 按照協議組織 post 包體
        $data = new \stdClass();
        $tel = new \stdClass();
        $tel->nationcode = "" . $nationCode;
        $tel->mobile = "" . $phoneNumber;

        $data->tel = $tel;
        $data->sig = $this->util->calculateSigForTempl($this->appkey, $random,
            $curTime, $phoneNumber);
        $data->tpl_id = $templId;
        $data->params = $params;
        $data->sign = $sign;
        $data->time = $curTime;
        $data->extend = $extend;
        $data->ext = $ext;

        return $this->util->sendCurlPost($wholeUrl, $data);
    }
}