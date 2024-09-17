<?php

namespace app\shop\controller\statistics;

use app\shop\controller\Controller;
use app\shop\service\statistics\OrderService;
use app\shop\service\statistics\ProductRankingService;

/**
 * 銷售資料控制器
 */
class Sales extends Controller
{
    /**
     * 銷售資料統計
     */
    public function index()
    {
        return $this->renderSuccess('', [
            // 成交訂單統計
            'order' => (new OrderService())->getData(),
            // 成交商品統計
            'product' => (new OrderService())->getProductData(),
            // 銷量top10
            'productSaleRanking' => (new ProductRankingService())->getSaleRanking(),
            // 瀏覽top10
            'productViewRanking' => (new ProductRankingService())->getViewRanking(),
            // 退款top10
            'productRefundRanking' => (new ProductRankingService())->getRefundRanking(),
        ]);
    }

    /**
     * 透過時間段查詢本期上期金額
     * $type型別：order refund
     */
    public function order($search_time, $type = 'order')
    {
        $days = $this->getDays($search_time);
        $data = [];
        if($type == 'order'){
            $data = (new OrderService())->getDataByDate($days);
        }else if($type == 'refund'){
            $data = (new OrderService())->getRefundByDate($days);
        }
        return $this->renderSuccess('', [
            // 日期
            'days' => $days,
            // 資料
            'data' => $data,
        ]);
    }

    /**
     * 透過時間段查詢本期上期金額
     */
    public function product($search_time)
    {
        $days = $this->getDays($search_time);
        return $this->renderSuccess('', [
            // 日期
            'days' => $days,
            // 資料
            'data' => (new OrderService())->getProductDataByDate($days),
        ]);
    }

    /**
     * 獲取具體日期陣列
     */
    private function getDays($search_time)
    {
        //搜尋時間段
        if(!isset($search_time) || empty($search_time)){
            //沒有傳，則預設為最近7天
            $end_time = date('Y-m-d', time());
            $start_time = date('Y-m-d', strtotime('-7 day',time()));
        }else{
            $start_time = array_shift($search_time);
            $end_time = array_pop($search_time);
        }

        $dt_start = strtotime($start_time);
        $dt_end = strtotime($end_time);
        $date = [];
        $date[] = date('Y-m-d', strtotime($start_time));
        while($dt_start < $dt_end) {
            $date[] = date('Y-m-d', strtotime('+1 day',$dt_start));
            $dt_start = strtotime('+1 day',$dt_start);
        }
        return $date;
    }
}