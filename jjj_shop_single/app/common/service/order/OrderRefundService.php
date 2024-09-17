<?php

namespace app\common\service\order;

use app\common\library\alipay\AliPay;
use app\common\library\easywechat\AppOpen;
use app\common\model\user\User as UserModel;
use app\common\model\user\BalanceLog as BalanceLogModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;
use app\common\library\easywechat\WxPay;
use app\common\library\easywechat\AppWx;
use app\common\library\easywechat\AppMp;

/**
 * 訂單退款服務類
 */
class OrderRefundService
{
    /**
     * 執行訂單退款
     */
    public function execute(&$order, $money = null)
    {
        // 退款金額，如不指定則預設為訂單實付款金額
        is_null($money) && $money = $order['pay_price'];
        $pay_type = $order['pay_type']['value'];
        if ($pay_type != OrderPayTypeEnum::BALANCE && $order['balance'] > 0) {
            if ($order['refund_money'] < $order['balance']) {
                if ($order['refund_money'] + $money > $order['balance']) {
                    $balance = round($order['balance'] - $order['refund_money'], 2);//餘額退款金額
                    $money = round($money - $balance, 2);
                    $this->balance($order, $balance);
                } else {
                    $pay_type = 10;
                }
            }
        }
        if ($money <= 0) {
            return true;
        }
        // 1.微信支付退款
        if ($pay_type == OrderPayTypeEnum::WECHAT) {
            return $this->wxpay($order, $money);
        }
        // 2.餘額支付退款
        if ($pay_type == OrderPayTypeEnum::BALANCE) {
            return $this->balance($order, $money);
        }
        // 3.支付寶退款
        if ($pay_type == OrderPayTypeEnum::ALIPAY) {
            return $this->alipay($order, $money);
        }
        return false;
    }

    /**
     * 餘額支付退款
     */
    private function balance($order, $money)
    {
        // 回退使用者餘額
        $user = UserModel::detail($order['user_id']);
        if ($user) {
            $user->where('user_id', '=', $order['user_id'])->inc('balance', $money)->update();
            log_write('-------------餘額退款');
            // 記錄餘額明細
            $money > 0 && BalanceLogModel::add(BalanceLogSceneEnum::REFUND, [
                'user_id' => $user['user_id'],
                'money' => $money,
                'app_id' => $order['app_id'],
            ], ['order_no' => $order['order_no']]);
        }
        return true;
    }

    /**
     * 微信支付退款
     */
    private function wxpay($order, $money)
    {
        if ($order['pay_source'] == 'mp' || $order['pay_source'] == 'h5') {
            $app = AppMp::getWxPayApp($order['app_id']);
        } else if ($order['pay_source'] == 'wx') {
            $app = AppWx::getWxPayApp($order['app_id']);
        } else if ($order['pay_source'] == 'android' || $order['pay_source'] == 'ios') {
            $app = AppOpen::getWxPayApp($order['app_id']);
        }
        $WxPay = new WxPay($app);
        return $WxPay->refund($order['transaction_id'], $order['online_money'], $money, $order['pay_source']);
    }

    /**
     * 支付寶退款
     */
    private function alipay($order, $money)
    {
        $AliPay = new AliPay();
        return $AliPay->refund($order['transaction_id'], $order['trade_no'], $money);
    }
}