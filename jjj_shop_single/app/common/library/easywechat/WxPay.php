<?php

namespace app\common\library\easywechat;

use app\admin\model\settings\Setting as SettingModel;
use app\api\service\order\paysuccess\type\PayTypeSuccessFactory;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\exception\BaseException;
use app\common\model\app\App as AppModel;
use app\common\model\app\AppWx as AppWxModel;

/**
 * 微信支付
 */
class WxPay
{
    // 微信支付配置
    private $app;

    /**
     * 建構函式
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 統一下單API
     */
    public function unifiedorder($order_no, $openid, $totalFee, $orderType, $pay_source, $app_id)
    {
        $data = [
            "mchid" => $this->app->getConfig()['mch_id'],
            "out_trade_no" => $order_no,
            "appid" => $this->app->getConfig()['app_id'],
            "description" => $order_no,
            "notify_url" => base_url() . 'index.php/job/notify/wxpay',
            'attach' => json_encode(['order_type' => $orderType, 'pay_source' => $pay_source]),
            "amount" => [
                "total" => intval($totalFee * 100),
                "currency" => "CNY"
            ],
            "payer" => [
                "openid" => $openid
            ]
        ];
        $url = "v3/pay/transactions/jsapi";
        //h5支付差異
        if ($pay_source == 'h5') {
            $url = "v3/pay/transactions/h5";
            unset($data['payer']);
            $data['scene_info'] = [
                "payer_client_ip" => request()->ip(),
                "h5_info" => [
                    "type" => "Wap"
                ]
            ];
        }
        if ($pay_source == 'android' || $pay_source == 'ios') {
            unset($data['payer']);
            $url = "v3/pay/transactions/app";
        }
        // 是否開啟服務商支付
        if (isset($this->app->getConfig()['sub_appid']) && $this->app->getConfig()['sub_appid']) {
            $url = "v3/pay/partner/transactions/jsapi";
            unset($data['mchid']);
            unset($data['appid']);
            unset($data['payer']['openid']);
            $data['sp_appid'] = $this->app->getConfig()['sp_appid'];
            $data['sp_mchid'] = $this->app->getConfig()['sp_mchid'];
            $data['sub_appid'] = $this->app->getConfig()['sub_appid'];
            $data['sub_mchid'] = $this->app->getConfig()['sub_mch_id'];
            $data['payer']['sub_openid'] = $openid;
        }
        // 統一下單
        $payApp = $this->app->getClient();
        $response = $payApp->postJson($url, $data);
        $result = $response->toArray(false);

        //如果是微信小程式
        if ($pay_source == 'wx' || $pay_source == 'android' || $pay_source == 'ios' || $pay_source == 'mp') {
            // 請求失敗
            if (!isset($result['prepay_id'])) {
                throw new BaseException(['msg' => "微信支付api：{$result['message']}", 'code' => 0]);
            }
            if ($pay_source == 'wx' || $pay_source == 'mp') {
                $prepayId = $result['prepay_id'];
                $utils = $this->app->getUtils();
                $appId = $this->app->getConfig()['app_id'];
                $signType = 'RSA';
                $config = $utils->buildMiniAppConfig($prepayId, $appId, $signType);
                return [
                    'appId' => $appId,
                    'nonceStr' => $config['nonceStr'],
                    'timeStamp' => $config['timeStamp'],
                    'paySign' => $config['paySign'],
                    "signType" => $config['signType'],
                    'package' => $config['package'],
                ];
            } else if ($pay_source == 'android' || $pay_source == 'ios') {
                $prepayId = $result['prepay_id'];
                $utils = $this->app->getUtils();
                $appId = $this->app->getConfig()['app_id'];
                $signType = 'RSA';
                $config = $utils->buildAppConfig($prepayId, $appId, $signType);
                return $config;
            }
        }
        // 請求失敗
        if (!isset($result['h5_url'])) {
            throw new BaseException(['msg' => "微信支付api：{$result['message']}", 'code' => 0]);
        }
        return $result;
    }

    /**
     * 支付成功非同步通知
     */
    public function notify()
    {
        if (!$json = file_get_contents('php://input')) {
            log_write('Not found DATA');
            $this->returnCode(false, 'Not found DATA');
        }
        log_write($json);
        $wechatpay_serial = request()->header('wechatpay-serial');
        $json = json_decode($json, true);
        $apikey = AppModel::getBySerial($wechatpay_serial);
        $AesUtil = new AesUtil($apikey);
        $data = $AesUtil->decryptToString($json['resource']['associated_data'], $json['resource']['nonce'], $json['resource']['ciphertext']);
        $data = json_decode($data, true);
        $attach = json_decode($data['attach'], true);
        // 例項化訂單模型
        $PaySuccess = PayTypeSuccessFactory::getFactory($data['out_trade_no'], $attach['order_type']);
        // 訂單資訊
        $order = $PaySuccess->model;
        empty($order) && $this->returnCode(false, '訂單不存在');
        if ($data['trade_state'] != 'SUCCESS') {
            $this->returnCode(false, $data['trade_state_desc']);
        }
        // 訂單支付成功業務處理
        $status = $PaySuccess->onPaySuccess(OrderPayTypeEnum::WECHAT, $data);
        if ($status == false) {
            $this->returnCode(false, $PaySuccess->error);
        }
        // 返回狀態
        $this->returnCode(true, 'OK');
    }

    /**
     * 申請退款API
     */
    public function refund($transaction_id, $total_fee, $refund_fee)
    {
        $out_refund_no = time();
        $data = [
            "transaction_id" => $transaction_id,
            "out_refund_no" => "{$out_refund_no}",
            "notify_url" => base_url(),
            "amount" => [
                "refund" => intval($refund_fee * 100),
                "total" => intval($total_fee * 100),
                "currency" => "CNY"
            ],
        ];
        $url = 'v3/refund/domestic/refunds';
        // 是否開啟服務商支付
        if (isset($this->app->getConfig()['sub_appid']) && $this->app->getConfig()['sub_appid']) {
            $url = "v3/ecommerce/refunds/apply";
            $data['sp_appid'] = $this->app->getConfig()['sp_appid'];
            $data['sp_mchid'] = $this->app->getConfig()['sp_mchid'];
            //$data['sub_appid'] = $this->app->getConfig()['sub_appid'];
            $data['sub_mchid'] = $this->app->getConfig()['sub_mch_id'];
        }
        $payApp = $this->app->getClient();
        $result = $payApp->postJson($url, $data);
        $result = $result->toArray(false);
        // 請求失敗
        if (!isset($result['refund_id'])) {
            throw new BaseException(['msg' => isset($result['message']) ? $result['message'] : '退款失敗']);
        }
        return true;
    }

    /**
     * 企業付款到零錢API
     */
    public function transfers($order_no, $openid, $amount, $desc, $real_name)
    {
        $api = $this->app->getClient();
        $result = $api->postXml('/mmpaymkttransfers/promotion/transfers', [
            'mch_appid' => $this->app->getConfig()['app_id'],
            'mchid' => $this->app->getConfig()['mch_id'],
            'partner_trade_no' => $order_no,
            'openid' => $openid,
            'check_name' => 'FORCE_CHECK',
            're_user_name' => $real_name,
            'amount' => $amount,
            'desc' => $desc,
        ]);
        $result = $result->toArray(false);
        // 請求失敗
        if (empty($result)) {
            throw new BaseException(['msg' => '微信提現到零錢api請求失敗']);
        }
        // 請求失敗
        if ($result['return_code'] === 'FAIL') {
            throw new BaseException(['msg' => 'return_msg: ' . $result['return_msg']]);
        }
        if ($result['result_code'] === 'FAIL') {
            throw new BaseException(['msg' => 'err_code_des: ' . $result['err_code_des']]);
        }
        return true;
    }

    /**
     * 返回狀態給微信伺服器
     */
    private function returnCode($returnCode = true, $msg = null)
    {
        // 返回狀態
        $return = [
            'return_code' => $returnCode ? 'SUCCESS' : 'FAIL',
            'return_msg' => $msg ?: 'OK',
        ];
        // 記錄日誌
        log_write([
            'describe' => '返回微信支付狀態',
            'data' => $return
        ]);
        die($this->toXml($return));
    }

    /**
     * 輸出xml字元
     * @param $values
     * @return bool|string
     */
    private function toXml($values)
    {
        if (!is_array($values)
            || count($values) <= 0
        ) {
            return false;
        }

        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }


    public function wechatTrans($pars, $app_id)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/transfer/batches';
        $http_method = 'POST';//請求方法（GET,POST,PUT）
        $timestamp = time();//請求時間戳
        $url_parts = parse_url($url);//獲取請求的絕對URL
        $nonce = $timestamp . rand('10000', '99999');//請求隨機串
        $body = json_encode((object)$pars);//請求報文主體
        $app = AppModel::detail($app_id);

        $apiclient_cert_arr = openssl_x509_parse($app['cert_pem']);
        $serial_no = $apiclient_cert_arr['serialNumberHex'];//證書序列號
        $mch_private_key = $app['key_pem'];//金鑰
        $merchant_id = $app['mchid'];//商戶id
        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?{$url_parts['query']}" : ""));
        $message = $http_method . "\n" .
            $canonical_url . "\n" .
            $timestamp . "\n" .
            $nonce . "\n" .
            $body . "\n";
        openssl_sign($message, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);//簽名
        $schema = 'WECHATPAY2-SHA256-RSA2048';
        $token = sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $merchant_id, $nonce, $timestamp, $serial_no, $sign);//微信返回token
        return $this->https_request(json_encode($pars), $token, $app['serial_no']);
    }


    public function https_request($data, $token, $serial_no)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/transfer/batches';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, (string)$url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //新增請求頭
        $headers = [
            'Authorization:WECHATPAY2-SHA256-RSA2048 ' . $token,
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8',
            'Wechatpay-Serial:' . $serial_no,
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
        ];
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function getEncrypt($str, $app_id)
    {
        //$str是待加密字串
        $public_key_path = root_path() . 'runtime/cert/app/' . $app_id . '/' . 'platform.pem';
        $public_key = file_get_contents($public_key_path);
        $encrypted = '';
        if (openssl_public_encrypt($str, $encrypted, $public_key, OPENSSL_PKCS1_OAEP_PADDING)) {
            //base64編碼
            $sign = base64_encode($encrypted);
        } else {
            throw new Exception('encrypt failed');
        }
        return $sign;
    }
}
