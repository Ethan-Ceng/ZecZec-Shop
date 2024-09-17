<?php

namespace app\api\service\order;

use app\common\library\alipay\AliPay;
use app\common\library\easywechat\AppOpen;
use app\common\library\easywechat\AppWx;
use app\common\library\easywechat\AppMp;
use app\common\library\easywechat\WxPay;

class PaymentService
{
    /**
     * 構建微信支付
     */
    public static function wechat(
        $user,
        $orderId,
        $orderNo,
        $payPrice,
        $orderType,
        $pay_source
    )
    {
        // 統一下單API
        if ($pay_source == 'wx') {
            $app = AppWx::getWxPayApp($user['app_id']);
            $open_id = $user['open_id'];
        } else if ($pay_source == 'mp') {
            $app = AppMp::getWxPayApp($user['app_id']);
            $open_id = $user['mpopen_id'];
        } else if ($pay_source == 'h5') {
            $app = AppMp::getWxPayApp($user['app_id']);
            $open_id = '';
        } else if ($pay_source == 'android' || $pay_source == 'ios') {
            $open_id = '';
            $app = AppOpen::getWxPayApp($user['app_id']);
        }
        $WxPay = new WxPay($app);
        $payment = $WxPay->unifiedorder($orderNo, $open_id, $payPrice, $orderType, $pay_source, $user['app_id']);
        return $payment;
    }

    /**
     * 構建支付寶支付
     */
    public static function alipay(
        $user,
        $orderId,
        $orderNo,
        $payPrice,
        $orderType,
        $pay_source
    )
    {
        $AliPay = new AliPay();
        $payment = $AliPay->unifiedorder($orderNo, $payPrice, $orderType, $pay_source);
        return $payment;
    }
}