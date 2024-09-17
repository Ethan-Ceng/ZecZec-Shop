<?php

namespace app\common\service\message;

use app\common\model\settings\Setting as SettingModel;
use app\common\model\user\User as UserModel;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\settings\MessageSettings as MessageSettingsModel;
use app\common\model\settings\Message as MessageModel;
use app\common\enum\order\OrderPayTypeEnum;

/**
 * 訊息通知服務
 */
class MessageService
{
    /**
     * 訂單支付成功後通知
     */
    public function payment($order, $orderType = OrderTypeEnum::MASTER)
    {
        $message = MessageModel::detailByEname('order_pay_user');
        $settings = MessageSettingsModel::detailByMessageId($message['message_id'], $order['app_id']);
        if (!$settings) {
            return;
        }
        $data = [
            // 訂單編號
            'order_no' => $order['order_no'],
            // 商品名稱
            'product_name' => $this->formatProductName($order['product']),
            // 訂單金額
            'pay_price' => $order['pay_price'],
            // 支付方式
            'pay_type' => OrderPayTypeEnum::data()[$order['pay_type']['value']]['name'],
            // 支付時間
            'pay_time' => date('Y-m-d H:i:s', $order['pay_time'])
        ];

        //傳送公眾號訊息
        if ($settings['mp_status'] == 1 && $order['user']['mpopen_id'] != '') {
            MpMessageService::send($data, $settings['mp_template'], $order['user']['mpopen_id'], $order['app_id']);
        }
        //傳送小程式訂閱訊息
        if ($settings['wx_status'] == 1 && $order['user']['open_id'] != '') {
            WxMessageService::send($data, $settings['wx_template'], $order['user']['open_id'], $order['app_id']);
        }
        //傳送簡訊訊息
        if ($settings['sms_status'] == 1 && $order['user']['mobile'] != '') {
            SmsMessageService::send($data, $settings['sms_template'], $order['user']['mobile'], $order['app_id']);
        }

        // 商家簡訊通知
        $this->newOrder($order, $data, $orderType);
    }

    /**
     * 商家簡訊通知
     */
    public function newOrder($order, $data, $orderType = OrderTypeEnum::MASTER)
    {
        $message = MessageModel::detailByEname('order_pay_store');
        $settings = MessageSettingsModel::detailByMessageId($message['message_id']);
        if (!$settings || $settings['sms_status'] == 0) {
            return;
        }
        // 商家簡訊通知
        $smsConfig = SettingModel::getItem('sms', $order['app_id']);
        $phone = $smsConfig['accept_phone'];

        if (empty($phone)) {
            return;
        }
        $data['name'] = SettingModel::getItem('store', $order['app_id'])['name'];
        SmsMessageService::send($data, $settings['sms_template'], $phone, $order['app_id']);
    }

    /**
     * 後臺發貨通知
     */
    public function delivery($order, $orderType = OrderTypeEnum::MASTER)
    {
        $message = MessageModel::detailByEname('order_delivery_user');
        $settings = MessageSettingsModel::detailByMessageId($message['message_id']);
        if (!$settings) {
            return;
        }
        $data = [
            // 訂單編號
            'order_no' => $order['order_no'],
            // 商品資訊
            'product_name' => $this->formatProductName($order['product']),
            //收貨人
            'name' => $order['address']['name'],
            // 收貨地址
            'address' => implode('', $order['address']['region']) . $order['address']['detail'],
            // 物流公司
            'express_name' => $order['express']['express_name'],
            // 物流單號
            'express_no' => $order['express_no'],
            // 發貨時間
            'express_time' => date('Y-m-d H:i:s', $order['delivery_time']),
        ];

        //傳送公眾號訊息
        if ($settings['mp_status'] == 1 && $order['user']['mpopen_id'] != '') {
            MpMessageService::send($data, $settings['mp_template'], $order['user']['mpopen_id'], $order['app_id']);
        }
        //傳送小程式訂閱訊息
        if ($settings['wx_status'] == 1 && $order['user']['open_id'] != '') {
            WxMessageService::send($data, $settings['wx_template'], $order['user']['open_id'], $order['app_id']);
        }
        //傳送簡訊訊息
        if ($settings['sms_status'] == 1 && $order['user']['mobile'] != '') {
            SmsMessageService::send($data, $settings['sms_template'], $order['user']['mobile'], $order['app_id']);
        }
    }

    /**
     * 後臺售後單狀態通知
     * $sence場景，audit 稽核  receipt 確認退款
     */
    public function refund($refund, $order_no, $sence = 'audit')
    {
        $message = MessageModel::detailByEname('order_refund_user');
        $settings = MessageSettingsModel::detailByMessageId($message['message_id']);
        if (!$settings) {
            return;
        }
        $data = [
            // 訂單編號
            'order_no' => $order_no,
            // 商品名稱
            'product_name' => $refund['order_product']['product_name'],
            // 售後型別
            'type' => $refund['type']['text'],
            // 處理結果
            'status' => $sence == 'audit' ? $refund['is_agree']['text'] : $refund['status']['text'],
            // 處理時間
            'process_time' => date('Y-m-d H:i:s', time()),
            // 拒絕原因
            'refuse_desc' => $refund['refuse_desc'] ?: '無',
        ];

        //傳送公眾號訊息
        if ($settings['mp_status'] == 1 && $refund['user']['mpopen_id'] != '') {
            MpMessageService::send($data, $settings['mp_template'], $refund['user']['mpopen_id'], $refund['app_id']);
        }
        //傳送小程式訂閱訊息
        if ($settings['wx_status'] == 1 && $refund['user']['open_id'] != '') {
            WxMessageService::send($data, $settings['wx_template'], $refund['user']['open_id'], $refund['app_id']);
        }
        //傳送簡訊訊息
        if ($settings['sms_status'] == 1 && $refund['user']['mobile'] != '') {
            SmsMessageService::send($data, $settings['sms_template'], $refund['user']['mobile'], $refund['app_id']);
        }
    }


    /**
     * 分銷商入駐稽核通知
     */
    public function agent($agent)
    {
        $message = MessageModel::detailByEname('agent_apply_user');
        $settings = MessageSettingsModel::detailByMessageId($message['message_id']);
        if (!$settings) {
            return;
        }

        // 傳送模板訊息
        $reason = '';
        if ($agent['apply_status'] == 30) {
            $reason = "駁回原因：" . $agent['reject_reason'];
        }

        $data = [
            // 申請時間
            'apply_time' => $agent['apply_time'],
            //稽核狀態
            'apply_status' => $agent['apply_status']['text'],
            // 稽核時間
            'audit_time' => $agent['audit_time'],
            // 拒絕原因
            'reason' => $reason ?: '無',
        ];

        // 獲取使用者資訊
        $user = UserModel::detail($agent['user_id']);

        //傳送公眾號訊息
        if ($settings['mp_status'] == 1 && $user['mpopen_id'] != '') {
            MpMessageService::send($data, $settings['mp_template'], $user['mpopen_id'], $user['app_id']);
        }
        //傳送小程式訂閱訊息
        if ($settings['wx_status'] == 1 && $user['open_id'] != '') {
            WxMessageService::send($data, $settings['wx_template'], $user['open_id'], $user['app_id']);
        }
        //傳送簡訊訊息
        if ($settings['sms_status'] == 1 && $user['mobile'] != '') {
            SmsMessageService::send($data, $settings['sms_template'], $user['mobile'], $user['app_id']);
        }
    }

    /**
     * 分銷商提現稽核通知
     */
    public function cash($cash)
    {
        $message = MessageModel::detailByEname('agent_cash_user');
        $settings = MessageSettingsModel::detailByMessageId($message['message_id']);
        if (!$settings) {
            return;
        }

        // 傳送模板訊息
        $reason = '無';
        if ($cash['apply_status'] == 30) {
            $reason = $cash['reject_reason'];
        }

        $data = [
            // 提現時間
            'create_time' => $cash['create_time'],
            //提現方式
            'pay_type' => $cash['pay_type']['text'],
            // 提現金額
            'money' => $cash['money'],
            // 提現狀態
            'apply_status' => $cash['apply_status']['text'],
            // 拒絕原因
            'reason' => $reason,
        ];

        // 獲取使用者資訊
        $user = UserModel::detail($cash['user_id']);

        //傳送公眾號訊息
        if ($settings['mp_status'] == 1 && $user['mpopen_id'] != '') {
            MpMessageService::send($data, $settings['mp_template'], $user['mpopen_id'], $user['app_id']);
        }
        //傳送小程式訂閱訊息
        if ($settings['wx_status'] == 1 && $user['open_id'] != '') {
            WxMessageService::send($data, $settings['wx_template'], $user['open_id'], $user['app_id']);
        }
        //傳送簡訊訊息
        if ($settings['sms_status'] == 1 && $user['mobile'] != '') {
            SmsMessageService::send($data, $settings['sms_template'], $user['mobile'], $user['app_id']);
        }
    }

    /**
     * 後臺發貨通知
     */
    public function codeOrder($order, $user)
    {
        $message = MessageModel::detailByEname('order_pick_user');
        $settings = MessageSettingsModel::detailByMessageId($message['message_id'], $order['app_id']);
        if (!$settings) {
            return;
        }
        $data = [
            //訂單編號
            'order_no' => $order['order_no'],
            //提貨碼
            'code_no' => $order['code']['code_no'],
            // 提貨商品
            'product_name' => $order['product_name'],
            // 物流地址
            'express_address' => implode('', $order['region']) . $order['detail'],
            // 提貨時間
            'pick_time' => $order['create_time'],
        ];

        //傳送公眾號訊息
        if ($settings['mp_status'] == 1 && $user['mpopen_id'] != '') {
            MpMessageService::send($data, $settings['mp_template'], $user['mpopen_id'], $order['app_id']);
        }
        //傳送小程式訂閱訊息
        if ($settings['wx_status'] == 1 && $user['open_id'] != '') {
            WxMessageService::send($data, $settings['wx_template'], $user['open_id'], $order['app_id']);
        }
        //傳送簡訊訊息
        if ($settings['sms_status'] == 1 && $user['mobile'] != '') {
            SmsMessageService::send($data, $settings['sms_template'], $user['mobile'], $order['app_id']);
        }
    }

    /**
     * 格式化商品名稱
     */
    private function formatProductName($productData)
    {
        $str = '';
        foreach ($productData as $product) {
            $str .= $product['product_name'] . ' ';
        }
        return $str;
    }

}