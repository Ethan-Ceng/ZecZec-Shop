<?php

namespace app\shop\service\statistics;

use app\common\library\helper;
use app\shop\model\order\Order as OrderModel;
use app\shop\model\order\OrderRefund as OrderRefundModel;
use app\shop\model\product\Product as ProductModel;
use app\shop\model\order\OrderProduct as OrderProductModel;

/**
 * 訂單資料概況
 */
class OrderService
{
    /**
     * 獲取資料概況
     */
    public function getData()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $data = [
            // 成交額(元)
            'order_total_price' => [
                'today' => helper::number2($this->getOrderData($today, null, 'order_total_price')),
                'yesterday' => helper::number2($this->getOrderData($yesterday, null, 'order_total_price'))
            ],
            // 支付訂單數
            'order_total' => [
                'today' => number_format($this->getOrderData($today, null, 'order_total')),
                'yesterday' => number_format($this->getOrderData($yesterday, null, 'order_total'))
            ],
            // 下單使用者數
            'order_user_total' => [
                'today' => number_format($this->getOrderData($today, null, 'order_user_total')),
                'yesterday' => number_format($this->getOrderData($yesterday, null, 'order_user_total'))
            ],
            // 退款成功總金額
            'order_refund_money' => [
                'today' => $this->getOrderRefundData($today, null, 'order_refund_money'),
                'yesterday' => $this->getOrderRefundData($yesterday, null, 'order_refund_money')
            ],
            // 退款成功訂單數
            'order_refund_total' => [
                'today' => $this->getOrderRefundData($today, null, 'order_refund_total'),
                'yesterday' => $this->getOrderRefundData($yesterday, null, 'order_refund_total')
            ],
        ];
        // 客單價
        $data['order_per_price'] = [
            'today' => $data['order_user_total']['today'] == 0?0:helper::number2($data['order_total_price']['today']/$data['order_user_total']['today']),
            'yesterday' => $data['order_user_total']['yesterday'] == 0?0:helper::number2($data['order_total_price']['yesterday']/$data['order_user_total']['yesterday'])
        ];
        return $data;
    }

    /**
     * 透過時間段查詢訂單資料
     */
    public function getDataByDate($days)
    {
        $data = [];
        foreach ($days as $day) {
            $data[] = [
                'day' => $day,
                'total_money' => $this->getOrderData($day, null, 'order_total_price'),
                'total_num' => $this->getOrderData($day, null, 'order_total')
            ];
        }
        return $data;
    }

    /**
     * 透過時間段查詢退款訂單資料
     */
    public function getRefundByDate($days)
    {
        $data = [];
        foreach ($days as $day) {
            $data[] = [
                'day' => $day,
                'total_money' => $this->getOrderRefundData($day, null, 'order_refund_money'),
                'total_num' => $this->getOrderRefundData($day, null, 'order_refund_total')
            ];
        }
        return $data;
    }

    /**
     * 獲取訂單資料
     */
    private function getOrderData($startDate, $endDate, $type)
    {
        return (new OrderModel)->getOrderData($startDate, $endDate, $type);
    }

    /**
     * 獲取訂單退款資料
     */
    private function getOrderRefundData($startDate, $endDate, $type)
    {
        return (new OrderRefundModel)->getOrderRefundData($startDate, $endDate, $type);
    }

    /**
     * 獲取資料概況
     */
    public function getProductData()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        return [
            // 在售商品
            'sale' => [
                'today' => number_format((new ProductModel)->getProductTotal(['product_status' => 10])),
                'yesterday' => '--'
            ],
            // 未付款商品(件)
            'no_pay' => [
                'today' => number_format($this->getOrderProductData($today, null, 'no_pay')),
                'yesterday' => number_format($this->getOrderProductData($yesterday, null, 'no_pay'))
            ],
            // 已付款商品(件)
            'pay' => [
                'today' => number_format($this->getOrderProductData($today, null, 'pay')),
                'yesterday' => number_format($this->getOrderProductData($yesterday, null, 'pay'))
            ],
        ];
    }

    /**
     * 獲取訂單商品資料
     */
    private function getOrderProductData($startDate, $endDate, $type)
    {
        return (new OrderProductModel)->getProductData($startDate, $endDate, $type);
    }


    /**
     * 透過時間段查詢商品訂單資料
     */
    public function getProductDataByDate($days)
    {
        $data = [];
        foreach ($days as $day) {
            $data[] = [
                'day' => $day,
                'total_num' => $this->getOrderProductData($day, null, 'pay')
            ];
        }
        return $data;
    }
}