<?php

namespace app\shop\service\statistics;

use app\shop\model\order\OrderProduct as OrderProductModel;
use app\common\enum\order\OrderStatusEnum;
use app\common\enum\order\OrderPayStatusEnum;
use app\shop\model\product\Product as ProductModel;

/**
 * 資料統計-商品銷售榜
 */
class ProductRankingService
{
    /**
     * 商品銷售榜
     */
    public function getSaleRanking()
    {
        $OrderProduct = new OrderProductModel();
        $totalSaleSql = $OrderProduct->alias('op')
            ->field(['sum(total_num)'])
            ->join('order', 'order.order_id=op.order_id')
            ->where('product_id', '=', 'p.product_id')
            ->where('order.pay_status', '=', OrderPayStatusEnum::SUCCESS)
            ->where('order.order_status', '<>', OrderStatusEnum::CANCELLED)
            ->buildSql();
        return (new ProductModel)->alias('p')
            ->with(['image.file'])
            ->field([
                'product_id',
                'product_name',
                'sales_initial',
                'sales_actual',
                "$totalSaleSql AS total_sales_num"
            ])
            ->where('is_delete', '=', 0)
            ->order(['total_sales_num' => 'DESC'])
            ->limit(10)
            ->select();
    }

    /**
     * 商品瀏覽榜
     */
    public function getViewRanking()
    {
        return (new ProductModel)->with(['image.file'])
            ->hidden(['content'])
            ->where('view_times', '>', 0)
            ->where('is_delete', '=', 0)
            ->order(['view_times' => 'DESC'])
            ->limit(10)
            ->select();
    }

    /**
     * 商品退款榜
     */
    public function getRefundRanking()
    {
        $OrderProduct = new OrderProductModel();
        $totalRefundSql = $OrderProduct->alias('op')
            ->field(['count(order_refund_id)'])
            ->join('order_refund', 'order_refund.order_product_id=op.order_product_id')
            ->where('product_id', '=', 'p.product_id')
            ->buildSql();
        return (new ProductModel)->alias('p')
            ->with(['image.file'])
            ->field([
                'product_id',
                'product_name',
                'sales_initial',
                'sales_actual',
                "$totalRefundSql AS refund_count"
            ])
            ->where('is_delete', '=', 0)
            ->having('refund_count>0')
            ->order(['refund_count' => 'DESC'])
            ->limit(10)
            ->select();
    }

    /**
     * 商品銷售榜
     */
    public function getSaleTimeRanking($param)
    {
        $model = new OrderProductModel();
        if ($param['product_type'] == 1) {
            $order = 'total_num asc';
        } elseif ($param['product_type'] == 2) {
            $order = 'total_num desc';
        } elseif ($param['product_type'] == 3) {
            $order = 'total_price asc';
        } elseif ($param['product_type'] == 4) {
            $order = 'total_price desc';
        } else {
            $order = 'total_num desc';
        }
        if ($param['product_time'] == 1) {//30天
            $model = $model->where('op.create_time', '>=', strtotime(date('Y-m-d', strtotime('-30 day'))));
        } elseif ($param['product_time'] == 2) {//今日
            $model = $model->where('op.create_time', '>=', strtotime(date('Y-m-d')));
        } elseif ($param['product_time'] == 3) {//近7天
            $model = $model->where('op.create_time', '>=', strtotime(date('Y-m-d', strtotime('-7 day'))));
        } elseif ($param['product_time'] == 4) {//本月
            $model = $model->where('op.create_time', '>=', strtotime(date('Y-m')));
        } elseif ($param['product_time'] == 5) {//本年
            $model = $model->where('op.create_time', '>=', strtotime(date('Y-01-01')));
        }
        return $model->alias('op')
            ->join('product p', 'p.product_id=op.product_id')
            ->join('order o', 'op.order_id=o.order_id')
            ->with(['image.file'])
            ->where('o.pay_status', '=', OrderPayStatusEnum::SUCCESS)
            ->where('o.order_status', '<>', OrderStatusEnum::CANCELLED)
            ->where('p.is_delete', '=', 0)
            ->field([
                'p.product_id',
                'p.product_name',
                'sum(total_pay_price) as total_price',
                'sum(total_num) as total_num'
            ])
            ->group('op.product_id')
            ->order($order)
            ->limit(6)
            ->select();
    }
}