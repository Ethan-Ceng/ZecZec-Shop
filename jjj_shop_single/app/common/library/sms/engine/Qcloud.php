<?php

namespace app\common\library\sms\engine;

use app\common\library\sms\package\qcloud\SmsSingleSender;

/**
 * 騰訊雲簡訊模組引擎
 */
class Qcloud extends Server
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
    public function sendSms($mobile, $template_code, $templateParams)
    {
        try {
            $ssender = new SmsSingleSender($this->config['AccessKeyId'], $this->config['AccessKeySecret']);
            $params = [];
            foreach ($templateParams as $key => $item) {
                if ($key == 'code') {
                    $params[] = $item;
                } elseif ($key == 'order_no') {
                    $params[] = mb_substr($item, -6);
                } else {
                    $params[] = mb_substr($item, 0, 6);
                }
            }
            $result = $ssender->sendWithParam("86", $mobile, $template_code,
                $params, $this->config['sign'], "", "");
            $rsp = json_decode($result, 1);
            if ($rsp['result'] == 0) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }


}
