<?php

namespace app\common\library\alipay;

use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Factory;
use app\api\service\order\paysuccess\type\PayTypeSuccessFactory;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\settings\SettingEnum;
use app\common\exception\BaseException;
use app\common\library\helper;
use app\common\model\settings\Setting;
use app\common\model\app\App as AppModel;

/**
 * 支付寶支付
 */
class AliPay
{
    // app_id
    public $app_id = '';
    // 支付寶公鑰
    public $publicKey = '';
    // 應用私鑰
    public $privateKey = '';

    /**
     * 建構函式
     */
    public function __construct()
    {
       $this->init(null);
    }

    public function init($app_id){
        // 獲取配置
        $config = AppModel::detail($app_id);
        if($config != null){
            $this->app_id = $config['alipay_appid'];
            $this->privateKey = $config['alipay_privatekey'];
            $this->publicKey = $config['alipay_publickey'];
        }
    }
    /**
     * 統一下單API
     */
    public function unifiedorder($order_no, $totalFee, $orderType, $pay_source)
    {
        if($pay_source == 'android' || $pay_source == 'ios'){
            $notify_url = base_url() . 'index.php/job/notify/alipay_notify?order_type='.$orderType.'&pay_source='.$pay_source;
            $result = Factory::setOptions($this->getOptions($notify_url))->payment()->app()->pay("訂單支付", $order_no, helper::number2($totalFee));
            return $result->body;
        }else {
            //請求引數
            $requestConfigs = array(
                'out_trade_no' => $order_no,
                'product_code' => 'QUICK_WAP_WAY',
                'total_amount' => helper::number2($totalFee), //單位 元
                'subject' => '訂單支付',  //訂單標題
            );
            $commonConfigs = array(
                //公共引數
                'app_id' => $this->app_id,
                'method' => 'alipay.trade.wap.pay',             //介面名稱
                'format' => 'JSON',
                'return_url' => base_url() . 'index.php/job/notify/alipay_return?order_type=' . $orderType . '&pay_source=' . $pay_source,
                'charset' => 'utf-8',
                'sign_type' => 'RSA2',
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '1.0',
                'notify_url' => base_url() . 'index.php/job/notify/alipay_notify?order_type=' . $orderType . '&pay_source=' . $pay_source,
                'biz_content' => json_encode($requestConfigs, JSON_UNESCAPED_UNICODE),
            );
            $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
            return $this->buildRequestForm($commonConfigs);
        }
    }

    private function getOptions($notify_url = '')
    {
        $options = new Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
        $options->signType = 'RSA2';
        $options->appId = $this->app_id;
        // 為避免私鑰隨原始碼洩露，推薦從檔案中讀取私鑰字串而不是寫入原始碼中
        $options->merchantPrivateKey = $this->privateKey;
        //注：如果採用非證書模式，則無需賦值上面的三個證書路徑，改為賦值如下的支付寶公鑰字串即可
        $options->alipayPublicKey = $this->publicKey;
        $options->notifyUrl = $notify_url;
        return $options;
    }

    /**
     * 同步通知
     */
    public function return()
    {
        $params = $_GET;
        $order_type =  $_GET['order_type'];
        log_write($params);
        // 例項化訂單模型
        $PaySuccess = PayTypeSuccessFactory::getFactory($_GET['out_trade_no'], $order_type, 0);
        // 訂單資訊
        $order = $PaySuccess->model;
        $this->init($order['app_id']);
        unset($params['order_type']);
        unset($params['pay_source']);
        $result = $this->rsaCheck($params);
        if ($result === true) {
            if (empty($order)) {
                echo 'error';
                return false;
            }
            $query_result = $this->query($params);
            if ($query_result['alipay_trade_query_response']['code'] == '10000') {
                if ($query_result['alipay_trade_query_response']['trade_status'] == 'TRADE_SUCCESS') {
                    log_write('支付成功' . $params['out_trade_no']);
                    // 跳到支付成功頁
                    if($order_type == OrderTypeEnum::MASTER){
                        return base_url() . 'h5/pages/order/pay-success/pay-success?order_id=' . $order['order_id'];
                    }
                } else {
                    // 跳到訂單詳情
                    if($order_type == OrderTypeEnum::MASTER) {
                        return base_url() . 'h5/pages/order/order-detail?order_id=' . $order['order_id'];
                    }
                }

                if($order_type == OrderTypeEnum::GIFT){
                    return base_url() . 'h5/pages/user/index/index';
                }else if($order_type == OrderTypeEnum::BALANCE){
                    return base_url() . 'h5/pages/user/my-wallet/my-wallet';
                }else if($order_type == OrderTypeEnum::FRONT){
                    return base_url() . 'h5/pages/order/myorder';
                }
            }
        } else {
            log_write('支付失敗');
            log_write($_GET);
            echo 'error';
        }
        return false;
    }


    private function query($params)
    {
        //請求引數
        $requestConfigs = array(
            'out_trade_no' => $params['out_trade_no'],
            'trade_no' => $params['trade_no'],
        );
        $commonConfigs = array(
            //公共引數
            'app_id' => $this->app_id,
            'method' => 'alipay.trade.query',             //介面名稱
            'format' => 'JSON',
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = curlPost('https://openapi.alipay.com/gateway.do?charset=utf-8', $commonConfigs);
        return json_decode($result, true);
    }


    /**
     * 支付成功非同步通知
     */
    public function notify()
    {
        $params = $_POST;
        $order_type = $_POST['order_type'];
        $pay_source = $_POST['pay_source'];
        unset($params['order_type']);
        unset($params['pay_source']);
        log_write('支付寶回撥');
        //處理你的邏輯，例如獲取訂單號$_POST['out_trade_no']，訂單金額$_POST['total_amount']等
        // 例項化訂單模型
        $PaySuccess = PayTypeSuccessFactory::getFactory($_POST['out_trade_no'], $order_type);
        // 訂單資訊
        $order = $PaySuccess->model;
        if (empty($order)) {
            echo 'error';
            exit();
        }
        $this->init($order['app_id']);
        //驗證簽名
        $result = $this->rsaCheck($params);
        if ($result === true && $_POST['trade_status'] == 'TRADE_SUCCESS') {
            log_write('支付寶回撥----驗證成功');

            // 訂單支付成功業務處理,相容微信引數
            $data['attach'] = '{"order_type": "' . $order_type . '","pay_source":"' . $pay_source . '"}';
            $data['transaction_id'] = $params['trade_no'];
            $status = $PaySuccess->onPaySuccess(OrderPayTypeEnum::ALIPAY, $data);
            if ($status == false) {
                echo 'error';
                exit();
            }
            //程式執行完後必須列印輸出“success”（不包含引號）。如果商戶反饋給支付寶的字元不是success這7個字元，支付寶伺服器會不斷重發通知，直到超過24小時22分鐘。一般情況下，25小時以內完成8次通知（通知的間隔頻率一般是：4m,10m,10m,1h,2h,6h,15h）；
            echo 'success';
            exit();
        }
        log_write('支付寶回撥----驗證失敗');
        echo 'error';
        exit();
    }

    /**
     * 申請退款API
     */
    public function refund($transaction_id, $order_no, $refund_fee)
    {
        //請求引數
        $requestConfigs = array(
            'trade_no' => $transaction_id,
            'out_trade_no' => $order_no,
            'refund_amount' => $refund_fee,
        );
        $commonConfigs = array(
            //公共引數
            'app_id' => $this->app_id,
            'method' => 'alipay.trade.refund',             //介面名稱
            'format' => 'JSON',
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = curlPost('https://openapi.alipay.com/gateway.do?charset=utf-8', $commonConfigs);
        $resultArr = json_decode($result, true);
        $result = $resultArr['alipay_trade_refund_response'];
        if($result['code'] && $result['code']=='10000'){
            return true;
        }else{
            throw new BaseException(['msg' => 'return_msg: ' . $result['msg'].','.$result['sub_msg']]);
        }
    }

    /**
     * 建立請求，以表單HTML形式構造（預設）
     */
    protected function buildRequestForm($para_temp)
    {
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='https://openapi.alipay.com/gateway.do?charset=utf-8' method='POST'>";
        foreach ($para_temp as $key => $val) {
            if (false === $this->checkEmpty($val)) {
                $val = str_replace("'", "&apos;", $val);
                $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
            }
        }
        //submit按鈕控制元件請不要含有name屬性
        $sHtml = $sHtml . "<input type='submit' value='ok' style='display:none;''></form>";
        return $sHtml;
    }


    public function generateSign($params, $signType = "RSA")
    {
        return $this->sign($this->getSignContent($params), $signType);
    }

    protected function sign($data, $signType = "RSA")
    {
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($this->privateKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私鑰格式錯誤，請檢查RSA私鑰配置');
        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }

    public function getSignContent($params)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 轉換成目標字元集
                $v = $this->characet($v, 'utf-8');
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 轉換字元集編碼
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = 'utf-8';
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }

    /**
     * 校驗$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value)
    {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }

    /**
     *  驗證簽名
     **/
    public function rsaCheck($params)
    {
        $sign = $params['sign'];
        $signType = $params['sign_type'];
        unset($params['sign_type']);
        unset($params['sign']);
        return $this->verify($this->getSignContent($params), $sign, $signType);
    }


    function verify($data, $sign, $signType = 'RSA')
    {
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($this->publicKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付寶RSA公鑰錯誤。請檢查公鑰檔案格式是否正確');

        //呼叫openssl內建方法驗籤，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
        return $result;
    }
}
