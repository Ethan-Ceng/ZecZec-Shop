<?php

namespace app\job\controller;


use app\common\library\alipay\AliPay;
use app\common\library\easywechat\WxPay;
use app\common\model\plus\agent\User as AgentUserModel;
use think\Request;
/**
 * 微信支付回撥
 */
class Notify
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * 微信支付回撥
     */
    public function wxpay()
    {
        // 微信支付元件：驗證非同步通知
        $WxPay = new WxPay(false);
        $WxPay->notify();
    }

    /**
     * 支付寶支付回撥（同步）
     */
    public function alipay_return()
    {
        $AliPay = new AliPay();
        $url = $AliPay->return();
        if($url){
            return redirect($url);
        }
        return false;
    }

    /**
     * 支付寶支付回撥（非同步）
     */
    public function alipay_notify()
    {
        $AliPay = new AliPay();
        $AliPay->notify();
    }

    /**
     * 支付寶支付回撥（非同步）
     */
    public function test()
    {
        $first_user = AgentUserModel::detail(4, ['grade']);
    }
}
