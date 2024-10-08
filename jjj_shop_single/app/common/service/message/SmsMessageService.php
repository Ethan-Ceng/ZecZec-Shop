<?php

namespace app\common\service\message;

use app\common\library\sms\Driver as SmsDriver;
use app\common\model\settings\Setting as SettingModel;

/**
 * 簡訊訊息通知服務
 */
class SmsMessageService
{
    /**
     * 訂單支付成功後通知
     */
    public static function send($data, $sms_template, $mobile, $app_id)
    {
        try{
            $sms_template = json_decode($sms_template, true);

            $var_data = $sms_template['var_data'];
            $send_data = [];
            foreach ($var_data as $key => $value){
                if(isset($data[$key])){
                    $send_data[$value['field_name']] = $data[$key];
                }else{
                    $send_data[$key] = $value['filed_value'];
                }
            }

            $smsConfig = SettingModel::getItem('sms', $app_id);

            $SmsDriver = new SmsDriver($smsConfig);
            $SmsDriver->sendSms($mobile, $sms_template['template_id'], $send_data);
        }catch (\Exception $e){
            log_write('簡訊訊息傳送失敗'.$e->getMessage());
        }
    }

}