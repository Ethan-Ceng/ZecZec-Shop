<?php

namespace app\shop\model\plus\card;

use app\common\model\plus\card\Order as OrderModel;
use app\common\library\helper;
use app\common\enum\order\OrderTypeEnum;
use app\common\service\message\MessageService;
use app\common\service\order\OrderRefundService;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\service\product\factory\ProductFactory;
use app\common\model\plus\coupon\UserCoupon as UserCouponModel;
use app\common\model\user\User as UserModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\shop\service\order\ExportService;
use think\facade\Filesystem;
use PhpOffice\PhpSpreadsheet\IOFactory;
use app\common\model\settings\Express as ExpressModel;
use app\common\service\order\OrderCompleteService;

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
        return $model->alias('order')->with(['user', 'card', 'code'])
            ->join('card', 'card.card_id = order.card_id', 'left')
            ->join('card_code', 'card_code.code_id = order.code_id', 'left')
            ->order(['order.create_time' => 'desc'])
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
        $model = $model->setWhere($model, $data);
        // 獲取資料列表
        return $model->alias('order')
            ->join('card', 'card.card_id = order.card_id', 'left')
            ->join('card_code', 'card_code.code_id = order.code_id', 'left')
            ->where($this->transferDataType($dataType))
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
        return $model->with(['product.image', 'address', 'user', 'extract', 'extract_store'])
            ->alias('order')
            ->field('order.*')
            ->join('user', 'user.user_id = order.user_id')
            ->where($this->transferDataType($dataType))
            ->where('order.is_delete', '=', 0)
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
        if (isset($data['code_no']) && $data['code_no'] != '') {
            $model = $model->where('card_code.code_no', '=' , trim($data['code_no']));
        }
        if (isset($data['delivery_status']) && $data['delivery_status'] != '') {
            $model = $model->where('order.delivery_status', '=', $data['delivery_status']);
        }
        if (isset($data['category_id']) && $data['category_id'] != '') {
            $model = $model->where('card.category_id', '=', $data['category_id']);
        }
        //搜尋時間段
        if (isset($data['create_time']) && $data['create_time'] != '') {
            $sta_time = array_shift($data['create_time']);
            $end_time = array_pop($data['create_time']);
            $model = $model->whereBetweenTime('order.create_time', $sta_time, $end_time);
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
            case 'wait';
                $filter['order.delivery_status'] = 0;
                break;
            case 'send';
                $filter['order.delivery_status'] = 1;
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
        // 整理更新的資料
        $updateList = [[
            'order_id' => $this['order_id'],
            'express_id' => $data['express_id'],
            'express_no' => $data['express_no']
        ]];
        // 更新訂單發貨狀態
        if ($status = $this->updateToDelivery($updateList)) {
            // 獲取已發貨的訂單
            $completed = self::detail($this['order_id'], ['user', 'express']);
            // 傳送訊息通知
            $this->sendDeliveryMessage([$completed]);
        }
        return $status;
    }

    /**
     * 確認發貨後傳送訊息通知
     */
    private function sendDeliveryMessage($orderList)
    {
        // 例項化訊息通知服務類
        $Service = new MessageService;
        foreach ($orderList as &$item) {
            $product = [];
            $item['address'] = [
                'name' => $item['name'],
                'region' => $item['region'],
                'detail' => $item['detail']
            ];
            $product[] = [
                'product_name' => $item['product_name'],
            ];
            $item['product'] = $product;
            // 傳送訊息通知
            $Service->delivery($item, OrderTypeEnum::MASTER);
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
                    'delivery_status' => 1,
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
            if ($order['delivery_status'] != 0
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
            return $status;
        });
    }
}