<?php

namespace app\common\library\easywechat\wx;

/**
 * 直播房間
 */
class WxOrder extends WxBase
{
    /**
     * 上傳物流單號
     */
    public function uploadExpress($params_arr)
    {
        // 獲取 access token 例項
        $accessToken = $this->app->getAccessToken();
        $token = $accessToken->getToken();
        // 微信介面url
        $apiUrl = "https://api.weixin.qq.com/wxa/sec/order/upload_shipping_info?access_token=$token";

        $params = json_encode($params_arr, JSON_UNESCAPED_UNICODE);
        // 執行請求
        $result = $this->post($apiUrl, $params);
        // 返回結果
        $response = $this->jsonDecode($result);
        if (!isset($response['errcode'])) {
            $this->error = '請求錯誤';
            return false;
        }
        if ($response['errcode'] != 0) {
            if ($response['errcode'] == '9410000') {
                $this->error = 'empty';
            } else {
                if ($response['errcode'] == 40001) {
                    //防止token過期或更換,重新獲取
                    $accessToken->getToken(true);
                }
                $this->error = $response['errmsg'];
            }
            return false;
        }
        return true;
    }

    /**
     * 查詢物流公司
     */
    public function getExpress()
    {
        // 獲取 access token 例項
        $accessToken = $this->app->getAccessToken();
        $token = $accessToken->getToken();
        // 微信介面url
        $apiUrl = "https://api.weixin.qq.com/cgi-bin/express/delivery/open_msg/get_delivery_list?access_token=$token";
        // 請求引數
        $params = "{}";
        // 執行請求
        $result = $this->post($apiUrl, $params);
        // 返回結果
        $response = $this->jsonDecode($result);
        if (!isset($response['errcode'])) {
            $this->error = '請求錯誤';
            return false;
        }
        if ($response['errcode'] != 0) {
            if ($response['errcode'] == '9410000') {
                $this->error = 'empty';
            } else {
                if ($response['errcode'] == 40001) {
                    //防止token過期或更換,重新獲取
                    $accessToken->getToken(true);
                }
                $this->error = $response['errmsg'];
            }
            return false;
        } else {
            return $response['delivery_list'];
        }

    }
}