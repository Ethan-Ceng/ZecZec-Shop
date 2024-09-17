<?php

namespace app\common\service\message;

use app\common\library\easywechat\AppMp;

/**
 * 公眾號訊息通知服務
 */
class MpMessageService
{
    /**
     * 訂單支付成功後通知
     */
    public static function send($data, $mp_template, $touser, $app_id)
    {
        try {
            $data['title'] = '';
            $data['remark'] = '';
            $mp_template = json_decode($mp_template, true);

            $var_data = $mp_template['var_data'];
            $send_data = [];
            foreach ($var_data as $key => $value) {
                if (isset($data[$key])) {
                    if ($key == "title" || $key == "remark") {
                        $send_data[$value['field_name']]['value'] = $value['filed_value'];
                    } else {
                        $send_data[$value['field_name']]['value'] = $data[$key];
                    }
                } else {
                    $send_data[$key]['value'] = $value['filed_value'];
                }
            }
            foreach ($send_data as $key => $value) {
                if (mb_strlen($value['value']) > 20) {
                    $send_data[$key]['value'] = mb_substr($value['value'], 0, 20);
                }
            }
            $app = AppMp::getApp($app_id);
            $api = $app->getClient();
            $accessToken = $app->getAccessToken(); // 使用easywechat自帶的方法,獲取訪問令牌
            $token = $accessToken->getToken(); // string
            $template = [
                'touser' => $touser,
                'template_id' => $mp_template['template_id'],
                'page' => '',
                'miniprogram' => '',
                'data' => $send_data
            ];
            $url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/bizsend?access_token=" . $token;
            $result = $api->postJson($url, $template);
            $result = $result->toArray(false);
            log_write($result);
        } catch (\Exception $e) {
            log_write('公眾號訊息傳送失敗');
            log_write($e->getMessage());
        }
    }

    /**
     * 訂單支付成功後通知
     */
    public static function sendNoTrans($data, $template_id, $touser, $app_id)
    {
        try {
            $app = AppMp::getApp($app_id);
            $app->template_message->send([
                'touser' => $touser,
                'template_id' => $template_id,
                'url' => '',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            log_write('公眾號訊息傳送失敗');
            log_write($e->getMessage());
        }
    }
}