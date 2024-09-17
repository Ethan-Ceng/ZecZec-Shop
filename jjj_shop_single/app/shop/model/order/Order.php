<?php

namespace app\shop\model\order;

use app\common\model\order\Order as OrderModel;
use app\common\library\helper;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\plus\agent\Order as AgentOrderModel;
use app\common\service\message\MessageService;
use app\common\service\order\OrderRefundService;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\service\product\factory\ProductFactory;
use app\common\model\plus\coupon\UserCoupon as UserCouponModel;
use app\common\model\user\User as UserModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\shop\model\settings\DeliverySetting as DeliverySettingModel;
use app\shop\service\order\ExportService;
use think\facade\Filesystem;
use PhpOffice\PhpSpreadsheet\IOFactory;
use app\common\model\settings\Express as ExpressModel;
use app\common\service\order\OrderCompleteService;
use app\common\enum\order\OrderSourceEnum;
use app\shop\model\settings\Setting as SettingModel;
use app\common\model\order\OrderDelivery as OrderDeliveryModel;
use app\common\library\printer\PrintApi;

/**
 * 訂單模型
 */
class Order extends OrderModel
{
    /**
     * 訂單列表
     */
    public function getList($dataType, $data = null)
    {
        $model = $this;
        // 檢索查詢條件
        $model = $model->setWhere($model, $data);
        // 獲取資料列表
        return $model->alias('order')
            ->with(['product' => ['image', 'refund'], 'user', 'advance'])
            ->join('user', 'user.user_id = order.user_id')
            ->order(['order.create_time' => 'desc'])
            ->field('order.*')
            ->where($this->transferDataType($dataType))
            ->paginate($data);
    }

    /**
     * 獲取訂單總數
     */
    public function getCount($dataType, $data)
    {
        $model = $this;
        // 檢索查詢條件
        unset($data['dataType']);
        $model = $model->setWhere($model, $data);
        $filter = [];
        // 訂單資料型別
        switch ($dataType) {
            case 'all':
                break;
            case 'payment';
                $filter['order.pay_status'] = OrderPayStatusEnum::PENDING;
                $filter['order.order_status'] = 10;
                break;
            case 'delivery';
                $model = $model->where('pay_status', '=', OrderPayStatusEnum::SUCCESS)
                    ->where('delivery_status', '<>', 20)
                    ->where('order_status', '=', 10);
                break;
            case 'received';
                $filter['order.pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['order.delivery_status'] = 20;
                $filter['order.receipt_status'] = 10;
                $filter['order.order_status'] = 10;
                break;
            case 'comment';
                $filter['order.is_comment'] = 0;
                $filter['order.order_status'] = 30;
                break;
            case 'six';
                $filter['order.is_comment'] = 1;
                $filter['order.order_status'] = 30;
                break;
            case 'cancel';
                $filter['order_status'] = 21;
                break;
            case 'canceled';
                $filter['order_status'] = 20;
                break;
        }
        // 獲取資料列表
        return $model->alias('order')
            ->join('user', 'user.user_id = order.user_id')
            ->where($filter)
            ->count();
    }

    /**
     * 訂單列表(全部)
     */
    public function getListAll($dataType, $query = [])
    {
        $model = $this;
        // 檢索查詢條件
        $model = $model->setWhere($model, $query);
        // 獲取資料列表
        return $model->with(['product.image', 'address', 'user', 'extract', 'extract_store', 'advance'])
            ->alias('order')
            ->join('user', 'user.user_id = order.user_id')
            ->where($this->transferDataType($dataType))
            ->where('order.is_delete', '=', 0)
            ->field('order.*')
            ->order(['order.create_time' => 'desc'])
            ->select();
    }

    /**
     * 訂單匯出
     */
    public function exportList($dataType, $query)
    {
        // 獲取訂單列表
        $list = $this->getListAll($dataType, $query);
        // 匯出excel檔案
        (new Exportservice)->orderList($list);
    }

    /**
     * 設定檢索查詢條件
     */
    private function setWhere($model, $data)
    {
        //搜尋訂單號
        if (isset($data['order_no']) && $data['order_no'] != '') {
            $model = $model->where('order.order_no', 'like', '%' . trim($data['order_no']) . '%');
        }
        //搜尋自提門店
        if (isset($data['store_id']) && $data['store_id'] != '') {
            $model = $model->where('order.extract_store_id', '=', $data['store_id']);
        }
        //搜尋配送方式
        if (isset($data['style_id']) && $data['style_id'] != '') {
            $model = $model->where('order.delivery_type', '=', $data['style_id']);
        }
        //搜尋時間段
        if (isset($data['create_time']) && $data['create_time'] != '') {
            $model = $model->where('order.create_time', 'between', [strtotime($data['create_time'][0]), strtotime($data['create_time'][1]) + 86399]);
        }
        //搜尋使用者資訊
        if (isset($data['search']) && $data['search']) {
            $model = $model->where('user.nickName|user.mobile', 'like', '%' . $data['search'] . '%');
        }
        if (isset($data['dataType']) && $data['dataType'] == 'delivery') {
            $model = $model->where('order.pay_status', '=', OrderPayStatusEnum::SUCCESS)
                ->where('order.delivery_status', '<>', 20)
                ->where('order.order_status', '=', 10);
        }
        return $model;
    }

    /**
     * 轉義資料型別條件
     */
    private function transferDataType($dataType)
    {
        $filter = [];
        // 訂單資料型別
        switch ($dataType) {
            case 'all':
                break;
            case 'payment';
                $filter['order.pay_status'] = OrderPayStatusEnum::PENDING;
                $filter['order.order_status'] = 10;
                break;
            case 'received';
                $filter['order.pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['order.delivery_status'] = 20;
                $filter['order.receipt_status'] = 10;
                $filter['order.order_status'] = 10;
                break;
            case 'comment';
                $filter['order.is_comment'] = 0;
                $filter['order.order_status'] = 30;
                break;
            case 'six';
                $filter['order.is_comment'] = 1;
                $filter['order.order_status'] = 30;
                break;
            case 'cancel';
                $filter['order_status'] = 21;
                break;
            case 'canceled';
                $filter['order_status'] = 20;
                break;
        }
        return $filter;
    }

    /**
     * 確認發貨(單獨訂單)
     */
    public function delivery($data)
    {
        // 轉義為訂單列表
        $orderList = [$this];
        // 驗證訂單是否滿足發貨條件
        if (!$this->verifyDelivery($orderList)) {
            return false;
        }
        $this->startTrans();
        try {
            // 更新訂單發貨狀態
            if ($data['is_single']) {
                //普通物流
                if ($data['is_label'] == 0) {
                    $delivery_status = 20;
                    if (!isset($data['express_id'])) {
                        $this->error = '請選擇物流公司';
                        return false;
                    }
                    // 整理更新的資料
                    $update = [
                        'express_id' => $data['express_id'],
                        'express_no' => $data['express_no'],
                        'delivery_status' => $delivery_status,
                        'delivery_time' => time(),
                    ];
                    //電子面單發貨
                } else {
                    $api = new PrintApi(self::$app_id);
                    $result = $api->printOrder($data, $this);
                    if ($result['code'] != 200) {
                        $this->error = $result['message'];
                        return false;
                    } else {
                        $delivery_status = 20;
                        $config = SettingModel::getItem('printer');
                        $settingDetail = DeliverySettingModel::detail($data['setting_id'], ['express']);
                        $update = [
                            'express_id' => $settingDetail['express']['express_id'],
                            'express_no' => $result['data']['kuaidinum'],
                            'delivery_status' => $delivery_status,
                            'delivery_time' => time(),
                            'task_id' => $result['data']['taskId'],
                            'return_num' => isset($result['data']['returnNum']) ? $result['data']['returnNum'] : '',
                            'label' => $result['data']['label'],
                            'kd_order_num' => isset($result['data']['kdComOrderNum']) ? $result['data']['kdComOrderNum'] : '',
                            'is_label' => 1,
                            'label_print_type' => $config['label_print_type'],
                            'template_id' => $data['template_id'],
                            'setting_id' => $data['setting_id'],
                            'address_id' => $data['address_id'],
                        ];
                    }
                }

            } else {
                if (!isset($data['delivery_list']) || count($data['delivery_list']) <= 0) {
                    $this->error = '請填寫多包裹資訊';
                    return false;
                }
                $delivery_status = 30;
                // 整理更新的資料
                $update = [
                    'is_single' => 1,
                    'delivery_status' => $delivery_status,
                ];
                $deliveryData = [];
                $deliveryProductData = [];
                $delivery_num = 0;
                $total_num = 0;
                $delivery_product_num = 0;
                $error = "發貨失敗";
                foreach ($data['delivery_list'] as $item) {
                    if ($data['is_label'] == 0) {
                        if (!$item['express_id'] || !$item['express_no']) {
                            $this->error = '請選擇物流公司或物流單號';
                            return false;
                        }
                        $deliveryData[] = [
                            'order_id' => $this['order_id'],
                            'express_id' => $item['express_id'],
                            'express_no' => $item['express_no'],
                            'delivery_data' => $item['delivery_data'],
                            'remark' => isset($item['remark']) ? $item['remark'] : '',
                            'app_id' => self::$app_id
                        ];
                        $update['express_id'] = $item['express_id'];
                        $update['express_no'] = $item['express_no'];
                    } else {
                        if (!isset($item['setting_id']) || !isset($item['template_id']) || !isset($item['address_id'])) {
                            $this->error = '請選擇電子面單資訊';
                            return false;
                        }
                        $api = new PrintApi(self::$app_id);
                        $result = $api->printOrder($item, $this);
                        if ($result['code'] != 200) {
                            $error = $result['message'];
                            continue;
                        } else {
                            $config = SettingModel::getItem('printer');
                            $settingDetail = DeliverySettingModel::detail($item['setting_id'], ['express']);
                            $deliveryData[] = [
                                'order_id' => $this['order_id'],
                                'express_id' => $settingDetail['express']['express_id'],
                                'express_no' => $result['data']['kuaidinum'],
                                'delivery_data' => $item['delivery_data'],
                                'task_id' => $result['data']['taskId'],
                                'return_num' => isset($result['data']['returnNum']) ? $result['data']['returnNum'] : '',
                                'label' => $result['data']['label'],
                                'kd_order_num' => isset($result['data']['kdComOrderNum']) ? $result['data']['kdComOrderNum'] : '',
                                'is_label' => 1,
                                'label_print_type' => $config['label_print_type'],
                                'template_id' => $item['template_id'],
                                'setting_id' => $item['setting_id'],
                                'address_id' => $item['address_id'],
                                'app_id' => self::$app_id
                            ];
                            $update['express_id'] = $settingDetail['express']['express_id'];
                            $update['express_no'] = $result['data']['kuaidinum'];
                        }
                    }
                    //更新發貨數量
                    foreach ($item['delivery_data'] as $value) {
                        $delivery_num += $value['delivery_num'];
                        $deliveryProductData[] = [
                            'data' => ['delivery_num' => ['inc', $value['delivery_num']]],
                            'where' => [
                                'order_product_id' => $value['order_product_id'],
                            ],
                        ];
                    }
                }
                //查詢發貨數量
                foreach ($this['product'] as $product) {
                    $total_num += $product['total_num'];
                    $delivery_product_num += $product['delivery_num'];
                }
                if ($delivery_product_num + $delivery_num > $total_num) {
                    $this->error = '商品發貨超過總數量';
                    return false;
                }
                //發貨完成
                if ($delivery_product_num + $delivery_num == $total_num) {
                    $delivery_status = 20;
                    $update['delivery_status'] = $delivery_status;
                    $update['delivery_time'] = time();
                    if ($this['is_single'] == 0 && count($data['delivery_list']) == 1) {
                        $update['is_single'] = 0;
                    }
                }
                $deliveryData && (new OrderDeliveryModel())->saveAll($deliveryData);
                $deliveryProductData && (new OrderProduct())->updateAll($deliveryProductData);
                if (count($deliveryData) <= 0) {
                    $this->error = $error;
                    return false;
                }
            }
            $update && $this->save($update);
            $this->commit();
            // 更新訂單發貨狀態
            if ($delivery_status == 20) {
                // 傳送訊息通知
                $this->sendDeliveryMessage($orderList);
            }
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 確認發貨後傳送訊息通知
     */
    private function sendDeliveryMessage($orderList)
    {
        // 例項化訊息通知服務類
        $Service = new MessageService;
        foreach ($orderList as $item) {
            // 獲取已發貨的訂單
            $order = self::detail($item['order_id'], ['user', 'address', 'product', 'express']);
            $order->sendWxExpress($order['express_id'], $order['express_no']);
            // 傳送訊息通知
            $Service->delivery($order, OrderTypeEnum::MASTER);
        }
        return true;
    }

    /**
     * 更新訂單發貨狀態(批次)
     */
    private function updateToDelivery($orderList)
    {
        $data = [];
        foreach ($orderList as $item) {
            $data[] = [
                'data' => [
                    'express_no' => $item['express_no'],
                    'express_id' => $item['express_id'],
                    'delivery_status' => 20,
                    'delivery_time' => time(),
                ],
                'where' => [
                    'order_id' => $item['order_id']
                ],
            ];
        }
        return $this->updateAll($data);
    }

    /**
     * 驗證訂單是否滿足發貨條件
     */
    private function verifyDelivery($orderList)
    {
        foreach ($orderList as $order) {
            if (
                $order['pay_status']['value'] != 20
                || $order['delivery_type']['value'] != DeliveryTypeEnum::EXPRESS
                || $order['delivery_status']['value'] == 20
            ) {
                $this->error = "訂單號[{$order['order_no']}] 不滿足發貨條件!";
                return false;
            }
        }
        return true;
    }

    /**
     * 修改訂單價格
     */
    public function updatePrice($data)
    {
        if ($this['pay_status']['value'] != 10) {
            $this->error = '該訂單不合法';
            return false;
        }
        if ($this['order_source'] != 10) {
            $this->error = '該訂單不合法';
            return false;
        }
        // 實際付款金額
        $payPrice = bcadd($data['update_price'], $data['update_express_price'], 2);
        if ($payPrice <= 0) {
            $this->error = '訂單實付款價格不能為0.00元';
            return false;
        }
        return $this->save([
                'order_no' => $this->orderNo(), // 修改訂單號, 否則微信支付提示重複
                'order_price' => $data['update_price'],
                'pay_price' => $payPrice,
                'update_price' => helper::bcsub($data['update_price'], helper::bcsub($this['total_price'], $this['coupon_money'])),
                'express_price' => $data['update_express_price']
            ]) !== false;
    }

    /**
     * 稽核：使用者取消訂單
     */
    public function confirmCancel($data)
    {
        // 判斷訂單是否有效
        if ($this['order_status']['value'] != 21 || $this['pay_status']['value'] != 20) {
            $this->error = "訂單不允許取消";
            return false;
        }
        // 訂單取消事件
        return $this->transaction(function () use ($data) {
            if ($data['is_cancel'] == true) {
                // 執行退款操作
                (new OrderRefundService)->execute($this);
                // 回退商品庫存
                ProductFactory::getFactory($this['order_source'])->backProductStock($this['product'], true);
                // 回退使用者優惠券
                $this['coupon_id'] > 0 && UserCouponModel::setIsUse($this['coupon_id'], false);
                // 回退使用者積分
                $user = UserModel::detail($this['user_id']);
                $describe = "訂單取消：{$this['order_no']}";
                $this['points_num'] > 0 && $user->setIncPoints($this['points_num'], $describe);
                //預售訂單
                if ($this['order_source'] == OrderSourceEnum::ADVANCE) {
                    if ($this['advance']['money_return'] == 1) {//預售訂單退定金
                        if ((new OrderRefundService)->execute($this['advance'])) {
                            // 更新訂單狀態
                            $this['advance']->save([
                                'is_refund' => 1,
                                'refund_money' => $this['advance']['pay_price'],
                            ]);
                        }
                    }
                    $this['advance']->save(['order_status' => 20]);
                }
                //更新分銷訂單狀態
                (new AgentOrderModel)->where('order_id', '=', $this['order_id'])->update(['is_invalid' => 1]);
            }
            // 更新訂單狀態
            return $this->save(['order_status' => $data['is_cancel'] ? 20 : 10]);
        });
    }

    /**
     * 獲取已付款訂單總數 (可指定某天)
     */
    public function getOrderData($startDate, $endDate, $type)
    {
        $model = $this;

        !is_null($startDate) && $model = $model->where('pay_time', '>=', strtotime($startDate));

        if (is_null($endDate)) {
            !is_null($startDate) && $model = $model->where('pay_time', '<', strtotime($startDate) + 86400);
        } else {
            $model = $model->where('pay_time', '<', strtotime($endDate) + 86400);
        }

        $model = $model->where('is_delete', '=', 0)
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20);


        if ($type == 'order_total') {
            // 訂單數量
            return $model->count();
        } else if ($type == 'order_total_price') {
            // 訂單總金額
            return $model->sum('pay_price');
        } else if ($type == 'order_user_total') {
            // 支付使用者數
            return count($model->distinct(true)->column('user_id'));
        }
        return 0;
    }

    /**
     * 獲取待處理訂單
     */
    public function getReviewOrderTotal()
    {
        $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
        $filter['delivery_status'] = 10;
        $filter['order_status'] = 10;
        return $this->where($filter)->count();
    }

    /**
     * 獲取某天的總銷售額
     * 結束時間不傳則查一天
     */
    public function getOrderTotalPrice($startDate = null, $endDate = null)
    {
        $model = $this;
        $model = $model->where('pay_time', '>=', strtotime($startDate));
        if (is_null($endDate)) {
            $model = $model->where('pay_time', '<', strtotime($startDate) + 86400);
        } else {
            $model = $model->where('pay_time', '<', strtotime($endDate) + 86400);
        }
        return $model->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('is_delete', '=', 0)
            ->sum('pay_price');
    }

    /**
     * 獲取某天的客單價
     * 結束時間不傳則查一天
     */
    public function getOrderPerPrice($startDate = null, $endDate = null)
    {
        $model = $this;
        $model = $model->where('pay_time', '>=', strtotime($startDate));
        if (is_null($endDate)) {
            $model = $model->where('pay_time', '<', strtotime($startDate) + 86400);
        } else {
            $model = $model->where('pay_time', '<', strtotime($endDate) + 86400);
        }
        return $model->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('is_delete', '=', 0)
            ->avg('pay_price');
    }

    /**
     * 獲取某天的下單使用者數
     */
    public function getPayOrderUserTotal($day)
    {
        $startTime = strtotime($day);
        $userIds = $this->distinct(true)
            ->where('pay_time', '>=', $startTime)
            ->where('pay_time', '<', $startTime + 86400)
            ->where('pay_status', '=', 20)
            ->where('is_delete', '=', 0)
            ->column('user_id');
        return count($userIds);
    }

    /**
     * 獲取兌換記錄
     * @param $param array
     * @return \think\Paginator
     */
    public function getExchange($param)
    {
        $model = $this;
        if (isset($param['order_status']) && $param['order_status'] > -1) {
            $model = $model->where('order.order_status', '=', $param['order_status']);
        }
        if (isset($param['nickName']) && !empty($param['nickName'])) {
            $model = $model->where('user.nickName', 'like', '%' . trim($param['nickName']) . '%');
        }

        return $model->with(['user'])->alias('order')
            ->join('user', 'user.user_id = order.user_id')
            ->where('order.order_source', '=', 20)
            ->where('order.is_delete', '=', 0)
            ->order(['order.create_time' => 'desc'])
            ->paginate($param);
    }

    /**
     * 批次發貨
     */
    public function batchDelivery($fileInfo)
    {
        try {
            $saveName = Filesystem::disk('public')->putFile('', $fileInfo);
            $savePath = public_path() . "uploads/{$saveName}";
            //載入excel表格
            $inputFileType = IOFactory::identify($savePath); //傳入Excel路徑
            $reader = IOFactory::createReader($inputFileType);
            $PHPExcel = $reader->load($savePath);

            $sheet = $PHPExcel->getSheet(0); // 讀取第一個工作表
            // 遍歷並記錄訂單資訊
            $list = [];
            $orderList = [];
            foreach ($sheet->toArray() as $key => $val) {
                if ($key > 0) {
                    if ($val[19] && $val[20]) {
                        // 查詢發貨公司是否存在
                        $express = ExpressModel::findByName(trim($val[19]));
                        $order = self::detail(['order_no' => trim($val[0])], ['user', 'address', 'product', 'express']);
                        if ($express && $order) {
                            $list[] = [
                                'data' => [
                                    'express_no' => trim($val[20]),
                                    'express_id' => $express['express_id'],
                                    'delivery_status' => 20,
                                    'delivery_time' => time(),
                                ],
                                'where' => [
                                    'order_id' => $order['order_id']
                                ],
                            ];
                            array_push($orderList, $order);
                        }
                    }
                }
            }
            if (count($list) > 0) {
                $this->updateAll($list);
                // 傳送訊息通知
                $this->sendDeliveryMessage($orderList);
            }
            unlink($savePath);
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 取消訂單
     */
    public function orderCancel($data)
    {
        // 判斷訂單是否有效
        if ($this['delivery_status']['value'] == 20 || $this['order_status']['value'] != 10 || $this['pay_status']['value'] != 20) {
            $this->error = "訂單不允許取消";
            return false;
        }
        // 訂單取消事件
        return $this->transaction(function () use ($data) {
            // 執行退款操作
            (new OrderRefundService)->execute($this);
            // 回退商品庫存
            ProductFactory::getFactory($this['order_source'])->backProductStock($this['product'], true);
            // 回退使用者優惠券
            $this['coupon_id'] > 0 && UserCouponModel::setIsUse($this['coupon_id'], false);
            // 回退使用者積分
            $user = UserModel::detail($this['user_id']);
            $describe = "訂單取消：{$this['order_no']}";
            $this['points_num'] > 0 && $user->setIncPoints($this['points_num'], $describe);
            //預售訂單
            if ($this['order_source'] == OrderSourceEnum::ADVANCE) {
                if ($this['advance']['money_return'] == 1) {//預售訂單退定金
                    if ((new OrderRefundService)->execute($this['advance'])) {
                        // 更新訂單狀態
                        $this['advance']->save([
                            'is_refund' => 1,
                            'refund_money' => $this['advance']['pay_price'],
                        ]);
                    }
                }
                $this['advance']->save(['order_status' => 20]);
            }
            //更新分銷訂單狀態
            (new AgentOrderModel)->where('order_id', '=', $this['order_id'])->update(['is_invalid' => 1]);
            // 更新訂單狀態
            return $this->save(['order_status' => 20, 'cancel_remark' => $data['cancel_remark']]);
        });
    }

    /**
     * 確認發貨（虛擬訂單）
     * @param $extractClerkId
     * @return bool|mixed
     */
    public function virtual($data)
    {
        if (
            $this['pay_status']['value'] != 20
            || $this['delivery_type']['value'] != DeliveryTypeEnum::NO_EXPRESS
            || $this['delivery_status']['value'] == 20
            || in_array($this['order_status']['value'], [20, 21])
        ) {
            $this->error = '該訂單不滿足發貨條件';
            return false;
        }
        return $this->transaction(function () use ($data) {
            // 更新訂單狀態：已發貨、已收貨
            $status = $this->save([
                'delivery_status' => 20,
                'delivery_time' => time(),
                'receipt_status' => 20,
                'receipt_time' => time(),
                'order_status' => 30,
                'virtual_content' => $data['virtual_content'],
            ]);
            // 執行訂單完成後的操作
            $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
            $OrderCompleteService->complete([$this], $this['app_id']);
            $this->sendWxExpress('', '');
            return $status;
        });
    }

    /**
     * 微信發貨
     */
    public function wxDelivery()
    {
        if ($this['pay_source'] != 'wx' || $this['delivery_status']['value'] != 20) {
            $this->error = '訂單狀態錯誤';
            return false;
        }
        if ($this['wx_delivery_status'] != 10) {
            $this->error = '訂單已發貨';
            return false;
        }
        $setting = SettingModel::getItem('store');
        if (!$setting['is_send_wx']) {
            $this->error = '未開啟小程式發貨';
            return false;
        }
        $this->startTrans();
        try {
            // 訂單同步到微信
            $result = $this->sendWxExpress($this['express_id'], $this['express_no']);
            if (!$result) {
                return false;
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 取消電子訂單
     */
    public function labelCancel($data)
    {
        $this->startTrans();
        try {
            $detail = [];
            $order = [];
            if ($data['is_multi'] == 1) {
                $detail = OrderDeliveryModel::detail($data['order_id']);
                if (!$detail) {
                    $this->error = '資料不存在';
                    return false;
                }
                $order = OrderModel::detail($detail['order_id']);
                if (!$order) {
                    $this->error = '訂單不存在';
                    return false;
                }
                $kd_order_num = $detail['kd_order_num'];
                $express_no = $detail['express_no'];
                $settingDetail = DeliverySettingModel::detail($detail['setting_id'], ['express']);
            } else {
                $order = OrderModel::detail($data['order_id']);
                if (!$order) {
                    $this->error = '訂單不存在';
                    return false;
                }
                $kd_order_num = $order['kd_order_num'];
                $express_no = $order['express_no'];
                $settingDetail = DeliverySettingModel::detail($order['setting_id'], ['express']);
            }
            if (!$settingDetail) {
                $this->error = '資料錯誤';
                return false;
            }
            $api = new PrintApi(self::$app_id);
            $result = $api->cancelOrder($settingDetail, $kd_order_num, $express_no);
            if ($result['returnCode'] != 200) {
                $this->error = $result['message'];
                return false;
            } else {
                if ($data['is_multi'] == 1) {
                    $deliveryProductData = [];
                    foreach ($detail['delivery_data'] as $value) {
                        $deliveryProductData[] = [
                            'data' => ['delivery_num' => ['dec', $value['delivery_num']]],
                            'where' => [
                                'order_product_id' => $value['order_product_id'],
                            ],
                        ];
                    }
                    $deliveryProductData && (new OrderProduct())->updateAll($deliveryProductData);
                    $detail->delete();
                    $deliveryNum = OrderDeliveryModel::where('order_id', '=', $order['order_id'])->count();
                    if ($deliveryNum > 0) {
                        $order->save([
                            'delivery_status' => 30,
                            'delivery_time' => 0
                        ]);
                    } else {
                        $order->save([
                            'delivery_status' => 10,
                            'delivery_time' => 0,
                            'is_single' => 0
                        ]);
                    }
                } else {
                    $order->save([
                        'task_id' => '',
                        'return_num' => '',
                        'label' => '',
                        'kd_order_num' => '',
                        'is_label' => 0,
                        'label_print_type' => 0,
                        'template_id' => 0,
                        'setting_id' => 0,
                        'address_id' => 0,
                        'delivery_status' => 10,
                        'delivery_time' => 0
                    ]);
                }
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 取消電子訂單
     */
    public function printRepeate($data)
    {
        if ($data['is_multi'] == 1) {
            $detail = OrderDeliveryModel::detail($data['order_id']);
            $task_id = $detail['task_id'];
        } else {
            $order = OrderModel::detail($data['order_id']);
            $task_id = $order['task_id'];
        }
        $api = new PrintApi(self::$app_id);
        $result = $api->printOld($task_id);
        if (!$result) {
            $this->error = '列印失敗';
            return false;
        } else {
            return true;
        }
    }

}