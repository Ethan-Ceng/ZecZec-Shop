<?php

namespace app\common\model\order;

use app\common\library\easywechat\AppWx;
use app\common\library\easywechat\wx\WxOrder;
use app\common\model\BaseModel;
use app\common\model\settings\Setting as SettingModel;
use app\common\service\order\OrderService;

/**
 * Class Order
 * 預售訂單模型
 */
class OrderAdvance extends BaseModel
{
    protected $name = 'order_advance';
    protected $pk = 'order_advance_id';
    /**
     * 追加欄位
     * @var string[]
     */
    protected $append = [
        'end_time_text'
    ];

    /**
     * 關聯主訂單表
     */
    public function orderM()
    {
        return $this->belongsTo("app\\common\\model\\order\\Order", 'order_id', 'order_id');
    }

    /**
     * 關聯使用者表
     */
    public function user()
    {
        return $this->belongsTo("app\\common\\model\\user\\User", 'user_id', 'user_id')->field('user_id,nickName');
    }

    /**
     * 生成訂單號
     */
    public function orderNo()
    {
        return OrderService::createOrderNo();
    }

    /**
     * 關聯預售商品
     */
    public function advance()
    {
        return $this->belongsTo("app\\common\\model\\plus\\advance\\Product", 'advance_product_id', 'advance_product_id');
    }

    /**
     * 支付狀態
     */
    public function getPayStatusAttr($value)
    {
        $status = [10 => '未付款', 20 => '已付款'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 支付方式
     */
    public function getPayTypeAttr($value)
    {
        $status = [10 => '餘額', 20 => '微信', 30 => '支付寶'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 預售結束時間
     */
    public function getEndTimeTextAttr($value, $data)
    {
        return isset($data['end_time']) && $data['end_time'] ? date('Y-m-d H:i:s', $data['end_time']) : '';
    }

    /**
     * 詳情
     */
    public static function detail($order_advance_id)
    {
        return (new static())->find($order_advance_id);
    }

    public function sendWxExpress($order, $user)
    {
        //判斷是否開啟小程式發貨
        $setting = SettingModel::getItem('store', $order['app_id']);
        if (!$setting['is_send_wx']) {
            return false;
        }
        // 如果是小程式微信支付，則提交
        if ($order['pay_type']['value'] != 20 || $order['pay_source'] != 'wx' || $order['transaction_id'] == '') {
            return false;
        }
        sleep(1);
        $express = null;
        $logistics_type = 3;
        // 請求引數
        $params_arr = [
            // 訂單，需要上傳物流資訊的訂單
            'order_key' => [
                // 訂單單號型別，用於確認需要上傳詳情的訂單。列舉值1，使用下單商戶號和商戶側單號；列舉值2，使用微信支付單號。
                "order_number_type" => 2,
                // 原支付交易對應的微信訂單號
                "transaction_id" => $order['transaction_id']
            ],
            // 發貨模式，發貨模式列舉值：1、UNIFIED_DELIVERY（統一發貨）2、SPLIT_DELIVERY（分拆發貨） 示例值: UNIFIED_DELIVERY
            "delivery_mode" => 1,
            // 物流模式，發貨方式列舉值：1、實體物流配送採用快遞公司進行實體物流配送形式 2、同城配送 3、虛擬商品，虛擬商品，例如話費充值，點卡等，無實體配送形式 4、使用者自提
            "logistics_type" => $logistics_type,//$this->getLogisticsType($this['delivery_type']),
            // 物流資訊列表，發貨物流單列表，支援統一發貨（單個物流單）和分拆發貨（多個物流單）兩種模式，多重性: [1, 10]
            "shipping_list" => [
                [
                    // 物流單號，物流快遞發貨時必填，示例值: 323244567777 字元位元組限制: [1, 128]
                    "tracking_no" => '',
                    // 物流公司編碼，快遞公司ID，參見「查詢物流公司編碼列表」，物流快遞發貨時必填， 示例值: DHL 字元位元組限制: [1, 128]
                    "express_company" => '',
                    // 商品資訊，例如：微信紅包抱枕*1個，限120個字以內
                    "item_desc" => "餘額充值",
                    // 聯絡方式，當發貨的物流公司為順豐時，聯絡方式為必填，收件人或寄件人聯絡方式二選一
                    "contact" => [
                        // 收件人聯絡方式，收件人聯絡方式為，採用掩碼傳輸，最後4位數字不能打掩碼 示例值: `189****1234, 021-****1234, ****1234, 0**2-***1234, 0**2-******23-10, ****123-8008` 值限制: 0 ≤ value ≤ 1024
                        "receiver_contact" => ''
                    ]
                ]
            ],
            // 上傳時間，用於標識請求的先後順序 示例值: `2022-12-15T13:29:35.120+08:00`
            "upload_time" => $this->getUploadTime(),
            // 支付者，支付者資訊
            "payer" => [
                // 使用者標識，使用者在小程式appid下的唯一標識。 下單前需獲取到使用者的Openid 示例值: oUpF8uMuAJO_M2pxb1Q9zNjWeS6o 字元位元組限制: [1, 128]
                "openid" => $user['open_id']
            ]
        ];
        // 小程式配置資訊
        $app = AppWx::getApp($order['app_id']);
        $model = new WxOrder($app);
        if ($model->uploadExpress($params_arr)) {
            return true;
        } else {
            log_write($model->getError());
            return false;
        }
    }

    private function getUploadTime()
    {
        $microtime = microtime();
        list($microSeconds, $timeSeconds) = explode(' ', $microtime);
        $milliseconds = round($microSeconds * 1000);
        return date('Y-m-d') . 'T' . date('H:i:s') . '.' . $milliseconds . '+08:00';
    }
}