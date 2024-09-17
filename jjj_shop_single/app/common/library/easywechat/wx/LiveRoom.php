<?php

namespace app\common\library\easywechat\wx;

/**
 * 直播房間
 */
class LiveRoom extends WxBase
{
    /**
     * 同步小程式直播房間
     */
    public function syn()
    {
        // 獲取 access token 例項
        $accessToken = $this->app->getAccessToken();
        $token = $accessToken->getToken();
        // 微信介面url
        $apiUrl = "https://api.weixin.qq.com/wxa/business/getliveinfo?access_token={$token}";
        // 請求引數
        $params = json_encode(['start' => 0, 'limit' => 100], JSON_UNESCAPED_UNICODE);
        // 執行請求
        $result = $this->post($apiUrl, $params);
        // 返回結果
        $response = $this->jsonDecode($result);
        if (!isset($response['errcode'])) {
            $this->error = '請求錯誤';
            return false;
        }
        if ($response['errcode'] != 0) {
            if($response['errcode'] == '9410000'){
                $this->error = 'empty';
            }else{
                if($response['errcode'] == 40001){
                    //防止token過期或更換,重新獲取
                    $accessToken->getToken(true);
                }
                $this->error = $response['errmsg'];
            }

            return false;
        }
        return $response;
    }

}