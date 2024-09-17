<?php

namespace app\common\model\order;

use app\common\enum\order\OrderSourceEnum;
use app\common\exception\BaseException;
use app\common\library\easywechat\AppWx;
use app\common\library\easywechat\wx\WxOrder;
use app\common\model\BaseModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\library\helper;
use app\common\model\settings\Express as ExpressModel;
use app\common\service\order\OrderService;
use app\common\service\order\OrderCompleteService;
use app\common\model\store\Order as StoreOrderModel;
use app\common\model\settings\Setting as SettingModel;
use think\db\Where;

/**
 * 訂單模型模型
 */
class Order extends BaseModel
{
    protected $pk = 'order_id';
    protected $name = 'order';

    /**
     * 追加欄位
     * @var string[]
     */
    protected $append = [
        'state_text',
        'order_source_text',
        'pay_end_time_text',
        'orderDeliverList'
    ];

    /**
     * 多包裹資訊
     */
    public function getOrderDeliverListAttr($value, $data)
    {
        $list = (new OrderDelivery())->where('order_id', '=', $data['order_id'])->select();
        if (count($list)) {
            foreach ($list as $item) {
                $productList = [];
                if ($item['delivery_data']) {
                    $deliveryData = $item['delivery_data'];
                    foreach ($deliveryData as $value) {
                        $product = (new OrderProduct())->where('order_product_id', '=', $value['order_product_id'])->with(['image'])->find();
                        $value['image_path'] = $product['image']['file_path'];
                        $productList[] = $value;
                    }
                }
                $express = ExpressModel::detail($item['express_id']);
                $item['express_name'] = $express ? $express['express_name'] : '';
                $item['product_list'] = $productList;
            }
        }
        return $list;
    }

    /**
     * 關聯預售定金訂單
     * @return \think\model\relation\HasOne
     */
    public function advance()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderAdvance', 'order_id', 'order_id');
    }

    /**
     * 訂單商品列表
     * @return \think\model\relation\HasMany
     */
    public function product()
    {
        return $this->hasMany('app\\common\\model\\order\\OrderProduct', 'order_id', 'order_id')->hidden(['content']);
    }


    /**
     * 關聯訂單收貨地址表
     * @return \think\model\relation\HasOne
     */
    public function address()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderAddress');
    }

    /**
     * 關聯自提訂單聯絡方式
     * @return \think\model\relation\HasOne
     */
    public function extract()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderExtract');
    }

    /**
     * 關聯物流公司表
     * @return \think\model\relation\BelongsTo
     */
    public function express()
    {
        return $this->belongsTo('app\\common\\model\\settings\\Express', 'express_id', 'express_id');
    }

    /**
     * 關聯自提門店表
     * @return \think\model\relation\BelongsTo
     */
    public function extractStore()
    {
        return $this->belongsTo('app\\common\\model\\store\\Store', 'extract_store_id', 'store_id');
    }

    /**
     * 關聯門店店員表
     * @return \think\model\relation\BelongsTo
     */
    public function extractClerk()
    {
        return $this->belongsTo('app\\common\\model\\store\\Clerk', 'extract_clerk_id');
    }

    /**
     * 關聯使用者表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User', 'user_id', 'user_id');
    }

    /**
     * 預售結束時間
     */
    public function getPayEndTimeTextAttr($value, $data)
    {
        return (isset($data['pay_end_time']) && $data['pay_end_time'] && is_numeric($data['pay_end_time'])) ? date('Y-m-d H:i:s', $data['pay_end_time']) : '';
    }

    /**
     * 訂單狀態文字描述
     * @param $value
     * @param $data
     * @return string
     */
    public function getStateTextAttr($value, $data)
    {
        // 訂單狀態
        if (in_array($data['order_status'], [20, 30])) {
            $orderStatus = [20 => '已取消', 30 => '已完成'];
            return $orderStatus[$data['order_status']];
        }
        // 付款狀態
        if ($data['pay_status'] == 10) {
            return '待付款';
        }
        // 拼團狀態
        if ($data['order_source'] == OrderSourceEnum::ASSEMBLE) {
            $assemble_status = $this->getAssembleStatus($data);
            if ($assemble_status != '') {
                return $assemble_status;
            }
        }
        // 發貨狀態
        if ($data['delivery_status'] == 10) {
            return '待發貨';
        }
        // 發貨狀態
        if ($data['delivery_status'] == 30) {
            return '部分發貨';
        }
        if ($data['receipt_status'] == 10) {
            return '待收貨';
        }
        return $value;
    }

    /**
     *  拼團訂單狀態
     */
    private function getAssembleStatus($data)
    {
        // 發貨狀態
        if ($data['assemble_status'] == 10) {
            return '已付款，未成團';
        }
        if ($data['assemble_status'] == 20 && $data['delivery_status'] == 10) {
            return '拼團成功，待發貨';
        }
        if ($data['assemble_status'] == 30) {
            return '拼團失敗';
        }
        return '';
    }

    /**
     * 付款狀態
     * @param $value
     * @return array
     */
    public function getPayTypeAttr($value)
    {
        return ['text' => OrderPayTypeEnum::data()[$value]['name'], 'value' => $value];
    }

    /**
     * 訂單來源
     * @param $value
     * @return array
     */
    public function getOrderSourceTextAttr($value, $data)
    {
        return OrderSourceEnum::data()[$data['order_source']]['name'];
    }

    /**
     * 付款狀態
     * @param $value
     * @return array
     */
    public function getPayStatusAttr($value)
    {
        return ['text' => OrderPayStatusEnum::data()[$value]['name'], 'value' => $value];
    }

    /**
     * 改價金額（差價）
     * @param $value
     * @return array
     */
    public function getUpdatePriceAttr($value)
    {
        return [
            'symbol' => $value < 0 ? '-' : '+',
            'value' => sprintf('%.2f', abs($value))
        ];
    }

    /**
     * 發貨狀態
     * @param $value
     * @return array
     */
    public function getDeliveryStatusAttr($value)
    {
        $status = [10 => '待發貨', 20 => '已發貨', 30 => '部分發貨'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 收貨狀態
     * @param $value
     * @return array
     */
    public function getReceiptStatusAttr($value)
    {
        $status = [10 => '待收貨', 20 => '已收貨'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 收貨狀態
     * @param $value
     * @return array
     */
    public function getOrderStatusAttr($value)
    {
        $status = [10 => '進行中', 20 => '已取消', 21 => '待取消', 30 => '已完成'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 配送方式
     * @param $value
     * @return array
     */
    public function getDeliveryTypeAttr($value)
    {
        return ['text' => DeliveryTypeEnum::data()[$value]['name'], 'value' => $value];
    }

    /**
     * 設定自定義表單資訊
     * @param $value
     * @return array
     */
    public function setCustomFormAttr($value)
    {
        return $value ? json_encode($value) : '';
    }

    /**
     * 獲取自定義表單資訊
     * @param $value
     * @return array
     */
    public function getCustomFormAttr($value)
    {
        return $value ? json_decode($value, true) : '';
    }

    /**
     * 訂單詳情
     */
    public static function detail($where, $with = ['user', 'address', 'product' => ['image', 'refund'], 'extract', 'express', 'extractStore.logo', 'extractClerk', 'advance'])
    {
        is_array($where) ? $filter = $where : $filter['order_id'] = (int)$where;
        return (new static())->with($with)->where($filter)->find();
    }

    /**
     * 訂單詳情
     */
    public static function detailByNo($order_no, $with = ['user', 'address', 'product' => ['image', 'refund'], 'extract', 'express', 'extractStore.logo', 'extractClerk'])
    {
        return (new static())->with($with)->where('order_no', '=', $order_no)->find();
    }

    /**
     * 批次獲取訂單列表
     * @param $orderIds
     * @param array $with
     * @return array
     */
    public function getListByIds($orderIds, $with = [])
    {
        $data = $this->getListByInArray('order_id', $orderIds, $with);
        return helper::arrayColumn2Key($data, 'order_id');
    }

    /**
     * 批次更新訂單
     * @param $orderIds
     * @param $data
     * @return bool
     */
    public function onBatchUpdate($orderIds, $data)
    {
        return $this->where('order_id', 'in', $orderIds)->save($data);
    }

    /**
     * 批次獲取訂單列表
     * @param $field
     * @param $data
     * @param array $with
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function getListByInArray($field, $data, $with = [])
    {
        return $this->with($with)
            ->where($field, 'in', $data)
            ->where('is_delete', '=', 0)
            ->select();
    }

    /**
     * 生成訂單號
     * @return string
     */
    public function orderNo()
    {
        return OrderService::createOrderNo();
    }

    /**
     * 確認核銷（自提訂單）
     * @param $extractClerkId
     * @return bool|mixed
     */
    public function verificationOrder($extractClerkId)
    {
        if (
            $this['pay_status']['value'] != 20
            || $this['delivery_type']['value'] != DeliveryTypeEnum::EXTRACT
            || $this['delivery_status']['value'] == 20
            || in_array($this['order_status']['value'], [20, 21])
        ) {
            $this->error = '該訂單不滿足核銷條件';
            return false;
        }
        return $this->transaction(function () use ($extractClerkId) {
            // 更新訂單狀態：已發貨、已收貨
            $status = $this->save([
                'extract_clerk_id' => $extractClerkId,  // 核銷員
                'delivery_status' => 20,
                'delivery_time' => time(),
                'receipt_status' => 20,
                'receipt_time' => time(),
                'order_status' => 30
            ]);
            // 新增訂單核銷記錄
            StoreOrderModel::add(
                $this['order_id'],
                $this['extract_store_id'],
                $this['extract_clerk_id'],
                OrderTypeEnum::MASTER
            );
            // 執行訂單完成後的操作
            $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
            $OrderCompleteService->complete([$this], static::$app_id);
            $this->sendWxExpress('', '');
            return $status;
        });
    }

    public function sendWxExpress($express_id, $express_no)
    {
        //判斷是否開啟小程式發貨
        $setting = SettingModel::getItem('store', $this['app_id']);
        if ($this['wx_delivery_status'] != 10 || !$setting['is_send_wx']) {
            return false;
        }
        // 如果是小程式微信支付，則提交
        if ($this['pay_type']['value'] != 20 || $this['pay_source'] != 'wx' || $this['transaction_id'] == '') {
            return false;
        }
        sleep(1);
        $express = null;
        if ($this['delivery_type']['value'] == 10) {
            $express = ExpressModel::detail($express_id);
            $logistics_type = 1;
            if (!$express['wx_code']) {
                $logistics_type = 4;
            }
        } elseif ($this['delivery_type']['value'] == 20) {
            $logistics_type = 4;
        } elseif ($this['delivery_type']['value'] == 30) {
            $logistics_type = 3;
        }
        // 請求引數
        $params_arr = [
            // 訂單，需要上傳物流資訊的訂單
            'order_key' => [
                // 訂單單號型別，用於確認需要上傳詳情的訂單。列舉值1，使用下單商戶號和商戶側單號；列舉值2，使用微信支付單號。
                "order_number_type" => 2,
                // 原支付交易對應的微信訂單號
                "transaction_id" => $this['transaction_id']
            ],
            // 發貨模式，發貨模式列舉值：1、UNIFIED_DELIVERY（統一發貨）2、SPLIT_DELIVERY（分拆發貨） 示例值: UNIFIED_DELIVERY
            "delivery_mode" => 1,
            // 物流模式，發貨方式列舉值：1、實體物流配送採用快遞公司進行實體物流配送形式 2、同城配送 3、虛擬商品，虛擬商品，例如話費充值，點卡等，無實體配送形式 4、使用者自提
            "logistics_type" => $logistics_type,//$this->getLogisticsType($this['delivery_type']),
            // 物流資訊列表，發貨物流單列表，支援統一發貨（單個物流單）和分拆發貨（多個物流單）兩種模式，多重性: [1, 10]
            "shipping_list" => [
                [
                    // 物流單號，物流快遞發貨時必填，示例值: 323244567777 字元位元組限制: [1, 128]
                    "tracking_no" => $this['delivery_type']['value'] == 10 ? $express_no : '',
                    // 物流公司編碼，快遞公司ID，參見「查詢物流公司編碼列表」，物流快遞發貨時必填， 示例值: DHL 字元位元組限制: [1, 128]
                    "express_company" => $this['delivery_type']['value'] == 10 ? $express['wx_code'] : '',
                    // 商品資訊，例如：微信紅包抱枕*1個，限120個字以內
                    "item_desc" => $this['product'][0]['product_name'],
                    // 聯絡方式，當發貨的物流公司為順豐時，聯絡方式為必填，收件人或寄件人聯絡方式二選一
                    "contact" => [
                        // 收件人聯絡方式，收件人聯絡方式為，採用掩碼傳輸，最後4位數字不能打掩碼 示例值: `189****1234, 021-****1234, ****1234, 0**2-***1234, 0**2-******23-10, ****123-8008` 值限制: 0 ≤ value ≤ 1024
                        "receiver_contact" => $this['delivery_type']['value'] == 10 ? $this->desensitize($this['address']['phone'], 3, 4) : ''
                    ]
                ]
            ],
            // 上傳時間，用於標識請求的先後順序 示例值: `2022-12-15T13:29:35.120+08:00`
            "upload_time" => $this->getUploadTime(),
            // 支付者，支付者資訊
            "payer" => [
                // 使用者標識，使用者在小程式appid下的唯一標識。 下單前需獲取到使用者的Openid 示例值: oUpF8uMuAJO_M2pxb1Q9zNjWeS6o 字元位元組限制: [1, 128]
                "openid" => $this['user']['open_id']
            ]
        ];
        // 小程式配置資訊
        $app = AppWx::getApp($this['app_id']);
        $model = new WxOrder($app);
        if ($model->uploadExpress($params_arr)) {
            $this->save(['wx_delivery_status' => 20]);
            return true;
        } else {
            log_write($model->getError());
            $this->error = $model->getError();
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

    /**
     * 脫敏
     *
     * @authors: Msy
     * @Created-Time: 2022/10/17 17:54
     * @param $string 需要脫敏的字元
     * @param $start  開始位置
     * @param $length 脫敏長度
     * @param $re     替換字元
     * @return string
     */
    public function desensitize($string, $start = 0, $length = 0, $re = '*')
    {
        if (empty($string) || empty($length) || empty($re)) return $string;
        $end = $start + $length;
        $strlen = mb_strlen($string);
        $str_arr = array();
        for ($i = 0; $i < $strlen; $i++) {
            if ($i >= $start && $i < $end) {
                $str_arr[] = $re;
            } else {
                $str_arr[] = mb_substr($string, $i, 1);
            }
        }
        return implode('', $str_arr);
    }

}