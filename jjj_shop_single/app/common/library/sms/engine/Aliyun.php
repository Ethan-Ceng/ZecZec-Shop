<?php

namespace app\common\library\sms\engine;

use app\common\library\sms\package\aliyun\SignatureHelper;

/**
 * 阿里雲簡訊模組引擎
 */
class Aliyun extends Server
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

        $params = [];
        // *** 需使用者填寫部分 ***

        // 必填: 簡訊接收號碼
        $params["PhoneNumbers"] = $mobile;

        // 必填: 簡訊簽名，應嚴格按"簽名名稱"填寫，請參考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = $this->config['sign'];

        // 必填: 簡訊模板Code，應嚴格按"模板CODE"填寫, 請參考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $template_code;

        // 可選: 設定模板引數, 假如模板中存在變數需要替換則為必填項
        $params['TemplateParam'] = $templateParams;

        // 可選: 設定傳送簡訊流水號
        // $params['OutId'] = "12345";

        // 可選: 上行簡訊擴充套件碼, 擴充套件碼欄位控制在7位或以下，無特殊需求使用者請忽略此欄位
        // $params['SmsUpExtendCode'] = "1234567";

        // *** 需使用者填寫部分結束, 以下程式碼若無必要無需更改 ***
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper例項用於設定引數，簽名以及傳送請求
        $helper = new SignatureHelper;

        // 此處可能會丟擲異常，注意catch
        $response = $helper->request(
            $this->config['AccessKeyId']
            , $this->config['AccessKeySecret']
            , "dysmsapi.aliyuncs.com"
            , array_merge($params, [
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ])
            // 選填: 啟用https
            , true
        );

        // 記錄日誌
        log_write([
            'config' => $this->config,
            'params' => $params
        ]);
        log_write($response);

        $this->error = $response->Message;
        return $response->Code === 'OK';
    }


}
