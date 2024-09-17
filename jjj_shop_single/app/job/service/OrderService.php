<?php

namespace app\job\service;

use app\common\service\product\factory\ProductFactory;
use app\job\model\order\Order as OrderModel;
use app\common\model\plus\coupon\UserCoupon as UserCouponModel;
use app\common\library\helper;
class OrderService
{
    // 模型
    private $model;

    // 自動關閉的訂單id集
    private $closeOrderIds = [];

    /**
     * 構造方法
     * Order constructor.
     */
    public function __construct()
    {
        $this->model = new OrderModel;
    }

    /**
     * 未支付訂單自動關閉
     */
    public function close()
    {
        // 查詢截止時間未支付的訂單
        $list = $this->model->getCloseList(['product', 'user']);
        $this->closeOrderIds = helper::getArrayColumn($list, 'order_id');
        // 取消訂單事件
        if (!empty($this->closeOrderIds)) {
            foreach ($list as &$order) {
                // 回退商品庫存
                ProductFactory::getFactory($order['order_source'])->backProductStock($order['product'], false);
                // 回退使用者優惠券
                $order['coupon_id'] > 0 && UserCouponModel::setIsUse($order['coupon_id'], false);
                // 回退使用者積分
                $describe = "訂單取消：{$order['order_no']}";
                $order['points_num'] > 0 && $order->user->setIncPoints($order['points_num'], $describe);
            }
            // 批次更新訂單狀態為已取消
            return $this->model->onBatchUpdate($this->closeOrderIds, ['order_status' => 20]);
        }
        return true;
    }

    /**
     * 獲取自動關閉的訂單id集
     */
    public function getCloseOrderIds()
    {
        return $this->closeOrderIds;
    }

}