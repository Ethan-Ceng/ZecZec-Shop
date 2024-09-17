<?php

namespace app\shop\controller\statistics;

use app\shop\controller\Controller;
use app\shop\service\statistics\UserService;
use app\shop\service\statistics\UserRankingService;

/**
 * 會員資料控制器
 */
class User extends Controller
{
    /**
     * 會員資料統計
     */
    public function index()
    {
        return $this->renderSuccess('', [
            // 會員統計
            'user' => (new UserService())->getData(),
            // 消費排行top10
            'payRanking' => (new UserRankingService())->getUserRanking('pay'),
            // 積分排行top10
            'pointsRanking' => (new UserRankingService())->getUserRanking('points'),
            // 邀請人排行top10
            'inviteRanking' => (new UserRankingService())->getUserRanking('invite'),
        ]);
    }

    /**
     * 成交會員佔比
     */
    public function scale($day)
    {
        return $this->renderSuccess('', [
            // 成交會員佔比
            'payScale' => (new UserService())->getPayScaleData($day),
        ]);
    }

    /**
     * 新增會員
     */
    public function new_user($search_time)
    {
        $days = $this->getDays($search_time);
        return $this->renderSuccess('', [
            // 日期
            'days' => $days,
            // 資料
            'data' => (new UserService())->getNewUserByDate($days),
        ]);
    }

    /**
     * 成交會員數
     */
    public function pay_user($search_time)
    {
        $days = $this->getDays($search_time);
        return $this->renderSuccess('', [
            // 日期
            'days' => $days,
            // 資料
            'data' => (new UserService())->getPayUserByDate($days),
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