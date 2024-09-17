<?php

namespace app\shop\service;

use app\shop\service\statistics\ProductRankingService;
use app\shop\model\order\OrderRefund;
use app\shop\model\plus\card\Order as CardOrderModel;
use app\shop\model\product\Product;
use app\shop\model\order\Order;
use app\shop\model\user\User;
use app\shop\model\product\Comment;
use app\shop\model\plus\agent\Cash as CashModel;
use app\shop\model\plus\agent\Apply as AgentApplyModel;
use app\shop\model\user\Cash as UserCashModel;

/**
 * 商城模型
 */
class ShopService
{
    // 商品模型
    private $ProductModel;

    // 訂單模型
    private $OrderModel;

    // 使用者模型
    private $UserModel;
    // 訂單退款模型
    private $OrderRefund;

    /**
     * 構造方法
     */
    public function __construct()
    {
        /* 初始化模型 */
        $this->ProductModel = new Product();
        $this->OrderModel = new Order();
        $this->UserModel = new User();
        $this->OrderRefund = new OrderRefund();
    }

    /**
     * 後臺首頁資料
     */
    public function getHomeData($param)
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        // 最近七天日期
        $lately7days = $this->getLately7days();
        $data = [
            'top_data' => [
                // 商品總量
                'product_total' => $this->getProductTotal(),
                //商品今日總量
                'product_today' => $this->getProductTotal($today),
                //商品昨日總量
                'product_yesterday' => $this->getProductTotal($yesterday),
                // 使用者總量
                'user_total' => $this->getUserTotal(),
                // 使用者今日總量
                'user_today' => $this->getUserTotal($today),
                // 使用者昨日總量
                'user_yesterday' => $this->getUserTotal($yesterday),
                // 訂單總量
                'order_total' => $this->getOrderTotal(),
                // 訂單今日總量
                'order_today' => $this->getOrderTotal($today),
                // 訂單昨日總量
                'order_yesterday' => $this->getOrderTotal($yesterday),
                // 評價總量
                'comment_total' => $this->getCommentTotal(),
                // 評價今日總量
                'comment_today' => $this->getCommentTotal($today),
                // 評價昨日總量
                'comment_yesterday' => $this->getCommentTotal($yesterday)
            ],
            'wait_data' => [
                //訂單
                'order' => [
                    'disposal' => $this->getReviewOrderTotal(),
                    'refund' => $this->getRefundOrderTotal(),
                    'card_count' => $this->getCardOrderTotal(),
                ],
                //分銷商
                'agent' => [
                    'apply' => AgentApplyModel::getApplyCount(),
                    'cash_apply' => $this->getAgentApplyTotal(10),
                    'cash_money' => $this->getAgentApplyTotal(20),
                ],
                //稽核
                'review' => [
                    'balance_apply' => $this->getBalanceCashTotal(10),
                    'balance_money' => $this->getBalanceCashTotal(20),
                    'comment' => $this->getReviewCommentTotal(),
                ],
                //庫存
                'stock' => [
                    'product' => $this->getProductStockTotal(),
                ],
            ]
        ];
        //資料升降比例
        $data['top_data']['product_rate'] = $data['top_data']['product_yesterday'] > 0 ? round(($data['top_data']['product_today'] - $data['top_data']['product_yesterday']) / $data['top_data']['product_yesterday'] * 100, 2) : round($data['top_data']['product_today'] * 100, 2);
        $data['top_data']['user_rate'] = $data['top_data']['user_yesterday'] > 0 ? round(($data['top_data']['user_today'] - $data['top_data']['user_yesterday']) / $data['top_data']['user_yesterday'] * 100, 2) : round($data['top_data']['user_today'] * 100, 2);
        $data['top_data']['order_rate'] = $data['top_data']['order_yesterday'] > 0 ? round(($data['top_data']['order_today'] - $data['top_data']['order_yesterday']) / $data['top_data']['order_yesterday'] * 100, 2) : round($data['top_data']['order_today'] * 100, 2);
        $data['top_data']['comment_rate'] = $data['top_data']['comment_yesterday'] > 0 ? round(($data['top_data']['comment_today'] - $data['top_data']['comment_yesterday']) / $data['top_data']['comment_yesterday'] * 100, 2) : round($data['top_data']['comment_today'] * 100, 2);
        //商品銷售排行
        $data['productRank'] = (new ProductRankingService())->getSaleTimeRanking($param);
        //銷售額概況
        $data['saleData'] = $this->getSaleByDate($param['sale_time']);
        //使用者資料
        $data['userData'] = $this->getUserByDate($param['user_time']);
        //資料更新時間
        $data['update_time'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * 透過時間段查詢訂單資料
     */
    private function getUserByDate($days)
    {
        $dateInfo = $this->getDays($days);
        $days = $dateInfo['date'];
        $data = [];
        foreach ($days as $day) {
            $data[] = [
                'day' => $day,
                'user_num' => $this->getUserTotal($day),//新增使用者
                'total_num' => $this->UserModel->getUserData(null, $day, 'user_total'),//累計使用者
            ];
        }
        $detail['data'] = $data;
        $detail['days'] = $dateInfo['time'];
        return $detail;
    }

    /**
     * 透過時間段查詢訂單資料
     */
    private function getSaleByDate($days)
    {
        $dateInfo = $this->getDays($days);
        $days = $dateInfo['date'];
        $data = [];
        $endTime = null;
        foreach ($days as $day) {
            $data[] = [
                'day' => $day,
                'total_money' => $this->OrderModel->getOrderData($day, null, 'order_total_price'),
            ];
            $endTime = $day;
        }
        $startTime = $days[0];
        $detail['data'] = $data;
        $detail['days'] = $dateInfo['time'];
        $detail['saleMoney'] = $this->OrderModel->getOrderData($startTime, $endTime, 'order_total_price');
        return $detail;
    }

    /**
     * 獲取具體日期陣列
     */
    private function getDays($time_type = '')
    {
        //搜尋時間段
        if (!$time_type) {
            //沒有傳，則預設為最近7天
            $end_time = date('Y-m-d', time());
            $start_time = date('Y-m-d', strtotime('-7 day', time()));
        } else {
            if ($time_type == 1) {//近7天
                $end_time = date('Y-m-d', time());
                $start_time = date('Y-m-d', strtotime('-7 day', time()));
            } elseif ($time_type == 2) {//近15天
                $end_time = date('Y-m-d', time());
                $start_time = date('Y-m-d', strtotime('-15 day', time()));
            } else {//近30天
                $end_time = date('Y-m-d', time());
                $start_time = date('Y-m-d', strtotime('-30 day', time()));
            }
        }
        $dt_start = strtotime($start_time);
        $dt_end = strtotime($end_time);
        $date = [];
        $time = [];
        $date[] = date('Y-m-d', strtotime($start_time));
        $time[] = date('m-d', strtotime($start_time));
        while ($dt_start < $dt_end) {
            $date[] = date('Y-m-d', strtotime('+1 day', $dt_start));
            $time[] = date('m-d', strtotime('+1 day', $dt_start));
            $dt_start = strtotime('+1 day', $dt_start);
        }
        $data['date'] = $date;
        $data['time'] = $time;
        return $data;
    }

    /**
     * 最近七天日期
     */
    private function getLately7days()
    {
        // 獲取當前周幾
        $date = [];
        for ($i = 0; $i < 7; $i++) {
            $date[] = date('Y-m-d', strtotime('-' . $i . ' days'));
        }
        return array_reverse($date);
    }

    /**
     * 獲取商品總量
     */
    private function getProductTotal($day = "")
    {
        return number_format($this->ProductModel->getProductTimeTotal($day));
    }

    /**
     * 獲取商品庫存告急總量
     */
    private function getProductStockTotal()
    {
        return number_format($this->ProductModel->getProductStockTotal());
    }

    /**
     * 獲取餘額提現總數量
     */
    private function getBalanceCashTotal($apply_status)
    {
        $model = new UserCashModel;
        return number_format($model->getUserApplyTotal($apply_status));
    }

    /**
     * 獲取待稽核提現總數量
     */
    private function getAgentApplyTotal($apply_status)
    {
        $model = new CashModel;
        return number_format($model->getAgentApplyTotal($apply_status));
    }

    /**
     * 獲取使用者總量
     */
    private function getUserTotal($day = null)
    {
        return number_format($this->UserModel->getUserTotal($day));
    }

    /**
     * 獲取訂單總量
     */
    private function getOrderTotal($day = null)
    {
        return number_format($this->OrderModel->getOrderData($day, null, 'order_total'));
    }

    /**
     * 獲取待處理訂單總量
     */
    private function getReviewOrderTotal()
    {
        return number_format($this->OrderModel->getReviewOrderTotal());
    }

    /**
     * 獲取售後訂單總量
     */
    private function getCardOrderTotal()
    {
        return number_format((new CardOrderModel())->where('order_status', '=', 1)
            ->where('delivery_status', '=', 0)
            ->where('is_delete', '=', 0)->count());
    }

    /**
     * 獲取售後訂單總量
     */
    private function getRefundOrderTotal()
    {
        return number_format($this->OrderRefund->getRefundOrderTotal());
    }

    /**
     * 獲取評價總量
     */
    private function getCommentTotal($day = "")
    {
        $model = new Comment;
        return number_format($model->getCommentTotal($day));
    }

    /**
     * 獲取待稽核評價總量
     */
    private function getReviewCommentTotal()
    {
        $model = new Comment;
        return number_format($model->getReviewCommentTotal());
    }
}