<?php

namespace app\shop\controller\order;

use app\shop\controller\Controller;
use app\shop\model\order\Order as OrderModel;
use app\shop\model\settings\Setting as SettingModel;
use app\shop\model\store\Store as StoreModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\shop\model\settings\Express as ExpressModel;
use app\shop\model\store\Clerk as ShopClerkModel;
use app\shop\model\plus\table\Record as TableRecordModel;
use app\shop\model\settings\ReturnAddress as ReturnAddressModel;
use app\shop\model\settings\DeliverySetting as DeliverySettingModel;
use app\shop\model\settings\DeliveryTemplate as DeliveryTemplateModel;

/**
 * 訂單控制器
 */
class Order extends Controller
{
    /**
     * 訂單列表
     */
    public function index($dataType = 'all')
    {
        // 訂單列表
        $model = new OrderModel();
        $list = $model->getList($dataType, $this->postData());
        $order_count = [
            'order_count' => [
                'payment' => $model->getCount('payment', $this->postData()),
                'delivery' => $model->getCount('delivery', $this->postData()),
                'received' => $model->getCount('received', $this->postData()),
                'cancel' => $model->getCount('cancel', $this->postData()),
                'canceled' => $model->getCount('canceled', $this->postData()),
            ],];
        // 自提門店列表
        $shop_list = StoreModel::getAllList();
        $ex_style = DeliveryTypeEnum::data();
        $is_send_wx = SettingModel::getItem('store')['is_send_wx'];
        return $this->renderSuccess('', compact('list', 'ex_style', 'shop_list', 'order_count', 'is_send_wx'));
    }

    /**
     * 訂單詳情
     */
    public function detail($order_id)
    {
        // 訂單詳情
        $detail = OrderModel::detail($order_id);
        if (isset($detail['pay_time']) && $detail['pay_time'] != '') {
            $detail['pay_time'] = date('Y-m-d H:i:s', $detail['pay_time']);
        }
        if (isset($detail['delivery_time']) && $detail['delivery_time']) {
            $detail['delivery_time'] = date('Y-m-d H:i:s', $detail['delivery_time']);
        }
        if ($detail['order_source'] == 80 && isset($detail['advance'])) {
            $detail['pay_price'] = round($detail['pay_price'] + $detail['advance']['pay_price'], 2);
            $detail['order_price'] = round($detail['total_price'] + $detail['advance']['pay_price'], 2);
        }
        if (isset($detail['receipt_time']) && $detail['receipt_time']) {
            $detail['receipt_time'] = date('Y-m-d H:i:s', $detail['receipt_time']);
        }
        // 物流公司列表
        $model = new ExpressModel();
        $expressList = $model->getAll();
        // 門店店員列表
        $shopClerkList = (new ShopClerkModel)->getClerk($detail['extract_store_id']);
        // 關聯表單
        foreach ($detail['product'] as &$item) {
            $record = TableRecordModel::detail($item['table_record_id']);
            $item['tableData'] = $record ? json_decode($record['content'], true) : '';
        }
        $template_list = DeliveryTemplateModel::getAll();
        $address_list = (new ReturnAddressModel)->getAll();
        $label_list = DeliverySettingModel::getAll();
        return $this->renderSuccess('', compact('detail', 'expressList', 'shopClerkList', 'template_list', 'address_list', 'label_list'));
    }

    /**
     * 確認發貨
     */
    public function delivery($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->delivery($this->postData())) {
            return $this->renderSuccess('發貨成功');
        }
        return $this->renderError($model->getError() ?: '發貨失敗');
    }

    /**
     * 修改訂單價格
     */
    public function updatePrice($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->updatePrice($this->postData('order'))) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     * 取消訂單
     */
    public function orderCancel($order_no)
    {
        // 訂單資訊
        $model = OrderModel::detail(['order_no' => $order_no]);
        if ($model->orderCancel($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 虛擬商品發貨
     */
    public function virtual($order_id)
    {
        // 訂單資訊
        $model = OrderModel::detail($order_id);
        if ($model->virtual($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 訂單改地址
     */
    public function updateAddress($order_id)
    {
        // 訂單資訊
        $order = OrderModel::detail($order_id);
        if ($order['delivery_type'] == 10 && $order['delivery_status'] == 20) {
            return $this->renderError('訂單已發貨不允許修改');
        }
        // 獲取物流資訊
        $model = $order['address'];
        if (!$model->updateAddress($this->postData())) {
            return $this->renderError($model->getError() ?: '修改失敗');
        }
        return $this->renderSuccess('修改成功');
    }

    /**
     * 微信小程式發貨
     */
    public function wxDelivery($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->wxDelivery()) {
            return $this->renderSuccess('發貨成功');
        }
        return $this->renderError($model->getError() ?: '發貨失敗');
    }

    /**
     * 取消電子訂單
     */
    public function labelCancel()
    {
        $model = new OrderModel();
        if ($model->labelCancel($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 電子訂單復打
     */
    public function printRepeate()
    {
        $model = new OrderModel();
        if ($model->printRepeate($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取物流資訊
     */
    public function express($order_id, $express_no, $express_id)
    {
        if (!$order_id || !$express_no || !$express_id) {
            return $this->renderError('引數錯誤');
        }
        $detail = ExpressModel::detail($express_id);
        // 訂單資訊
        $order = OrderModel::detail($order_id);
        if (!$order) {
            return $this->renderError('訂單不存在');
        }
        if (!$detail) {
            return $this->renderError('沒有物流資訊');
        }
        // 獲取物流資訊
        $model = new ExpressModel();
        $express = $model->dynamic($detail['express_name'], $detail['express_code'], $express_no, $order['address']['phone']);
        if ($express === false) {
            return $this->renderError($model->getError());
        }
        return $this->renderSuccess('', compact('express'));
    }

}