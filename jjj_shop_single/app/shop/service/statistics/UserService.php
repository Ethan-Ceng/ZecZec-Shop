<?php

namespace app\shop\service\statistics;

use app\shop\model\order\Order as OrderModel;
use app\shop\model\user\User as UserModel;

/**
 * 使用者資料概況
 */
class UserService
{
    /**
     * 獲取資料概況
     */
    public function getData()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        return [
            // 累計會員數
            'user_total' => [
                'today' => number_format($this->getUserData(null, $today, 'user_total')),
                'yesterday' => number_format($this->getUserData(null, $yesterday, 'user_total'))
            ],
            // 新增會員數
            'user_add' => [
                'today' => number_format($this->getUserData($today, null, 'user_add')),
                'yesterday' => number_format($this->getUserData($yesterday, null, 'user_add'))
            ],
            // 成交會員數
            'user_pay' => [
                'today' => number_format($this->getOrderData($today, null, 'order_user_total')),
                'yesterday' => number_format($this->getOrderData($yesterday, null, 'order_user_total'))
            ],
        ];
    }

    /**
     * 查詢成交佔比
     */
    public function getPayScaleData($day){
        $today = date('Y-m-d');
        $startDate = null;
        if($day > 0){
            $startDate = date('Y-m-d', strtotime('-' .$day.' day'));
        }
        return [
            'pay' => $this->getUserData($startDate, $today, 'user_pay'),
            'no_pay' => $this->getUserData($startDate, $today, 'user_no_pay')
        ];
    }
    /**
     * 透過時間段查詢使用者資料
     */
    public function getNewUserByDate($days)
    {
        $data = [];
        foreach ($days as $day) {
            $data[] = [
                'day' => $day,
                'total_num' => $this->getUserData($day, null, 'user_add')
            ];
        }
        return $data;
    }

    /**
     * 透過時間段查詢使用者成交資料
     */
    public function getPayUserByDate($days)
    {
        $data = [];
        foreach ($days as $day) {
            $data[] = [
                'day' => $day,
                'total_num' => number_format($this->getOrderData($day, null, 'order_user_total'))
            ];
        }
        return $data;
    }

    /**
     * 獲取使用者資料
     */
    private function getUserData($startDate, $endDate, $type)
    {
        return (new UserModel)->getUserData($startDate, $endDate, $type);
    }

    /**
     * 獲取訂單資料
     */
    private function getOrderData($startDate, $endDate, $type)
    {
        return (new OrderModel)->getOrderData($startDate, $endDate, $type);
    }
}