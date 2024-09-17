<?php

namespace app\common\library\sms\engine;
/**
 * 華為雲簡訊模組引擎
 */
class Hwcloud extends Server
{
    private $config;

    /**
     * 構造方法
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 傳送簡訊通知
     */
    /**
     * 傳送簡訊通知
     */
    public function sendSms($mobile, $template_code, $templateParams)
    {
        //必填,請參考"開發準備"獲取如下資料,替換為實際值
//        $url = 'https://smsapi.cn-north-4.myhuaweicloud.com:443/sms/batchSendSms/v1'; //APP接入地址(在控制檯"應用管理"頁面獲取)+介面訪問URI
        $url = $this->config['url'].'/sms/batchSendSms/v1'; //APP接入地址(在控制檯"應用管理"頁面獲取)+介面訪問URI
        $APP_KEY = $this->config['AccessKeyId']; //APP_Key
        $APP_SECRET = $this->config['AccessKeySecret']; //APP_Secret
//        $sender = '1069368924410000990'; //國內簡訊簽名通道號或國際/港澳臺簡訊通道號
        //$TEMPLATE_ID = $TEMPLATE_ID;//'aa1b7b620f424b2fb6d5ee64a5dc3528'; //模板ID

        //條件必填,國內簡訊關注,當templateId指定的模板型別為通用模板時生效且必填,必須是已稽核透過的,與模板型別一致的簽名名稱
        //國際/港澳臺簡訊不用關注該引數
        //$signature = $signature;//'華為雲簡訊測試'; //簽名名稱

        //必填,全域性號碼格式(包含國家碼),示例:+86151****6789,多個號碼之間用英文逗號分隔
        $receiver = $mobile; //簡訊接收人號碼

        //選填,簡訊狀態報告接收地址,推薦使用域名,為空或者不填表示不接收狀態報告
        $statusCallback = '';
//        $code = str_pad(mt_rand(100000, 999999), 6, "0", STR_PAD_BOTH);
        /**
         * 選填,使用無變數模板時請賦空值 $TEMPLATE_PARAS = '';
         * 單變數模板示例:模板內容為"您的驗證碼是${1}"時,$TEMPLATE_PARAS可填寫為'["369751"]'
         * 雙變數模板示例:模板內容為"您有${1}件快遞請到${2}領取"時,$TEMPLATE_PARAS可填寫為'["3","人民公園正門"]'
         * 模板中的每個變數都必須賦值，且取值不能為空
         * 檢視更多模板和變數規範:產品介紹>模板和變數規範
         * @var string $TEMPLATE_PARAS
         */
//        $TEMPLATE_PARAS = "[$code]"; //模板變數，此處以單變數驗證碼簡訊為例，請客戶自行生成6位驗證碼，並定義為字串型別，以杜絕首位0丟失的問題（例如：002569變成了2569）。
        //請求Headers
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
            'X-WSSE: ' . $this->buildWsseHeader($APP_KEY, $APP_SECRET)
        ];
        //請求Body
        $data = http_build_query([
            'from' => $this->config['sender'],
            'to' => $receiver,
            'templateId' => $template_code,
            'templateParas' => "['{$templateParams['code']}']",
            'statusCallback' => $statusCallback,
            'signature' => $this->config['sign'] //使用國內簡訊通用模板時,必須填寫簽名名稱
        ]);

        $context_options = [
            'http' => ['method' => 'POST', 'header' => $headers, 'content' => $data, 'ignore_errors' => true],
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false] //為防止因HTTPS證書認證失敗造成API呼叫失敗，需要先忽略證書信任問題
        ];
        $response = file_get_contents($url, false, stream_context_create($context_options));
        $result = json_decode($response, 1);
        if ($result['code'] == '000000' && $result['result'][0]['status'] == "000000") {
            return true;
        }
        return false;
    }

    /**
     * 構造X-WSSE引數值
     * @param string $appKey
     * @param string $appSecret
     * @return string
     */
    private function buildWsseHeader(string $appKey, string $appSecret)
    {
        date_default_timezone_set('Asia/Shanghai');
        $now = date('Y-m-d\TH:i:s\Z'); //Created
        $nonce = uniqid(); //Nonce
        $base64 = base64_encode(hash('sha256', ($nonce . $now . $appSecret))); //PasswordDigest
        return sprintf("UsernameToken Username=\"%s\",PasswordDigest=\"%s\",Nonce=\"%s\",Created=\"%s\"",
            $appKey, $base64, $nonce, $now);
    }


}
