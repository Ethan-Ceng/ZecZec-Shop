<?php

namespace app\api\service\bargain;

use app\common\exception\BaseException;

class Amount
{
    /**
     * 砍價金額
     */
    protected $amount;

    /**
     * 砍價人數
     */
    protected $num;

    /**
     * 砍價的最小金額
     */
    protected $coupon_min;

    /**
     * 砍價分配結果
     */
    protected $items = [];

    /**
     * 初始化
     * @param float $amount 砍價金額（單位：元）最多保留2位小數
     * @param int $num 砍價個數
     * @param float $coupon_min 每個至少領取的砍價金額
     */
    public function __construct($amount, $num = 1, $coupon_min = 0.01)
    {
        $this->amount = $amount;
        $this->num = $num;
        $this->coupon_min = $coupon_min;
    }

    /**
     * 處理返回
     */
    public function handle()
    {
        // 驗證
        if ($this->amount < $validAmount = $this->coupon_min * $this->num) {
            throw new BaseException(['msg' => '砍價總金額必須≥' . $validAmount . '元']);
        }
        // 分配砍價
        $this->apportion();
        return [
            'items' => $this->items,
        ];
    }

    /**
     * 分配砍價
     */
    protected function apportion()
    {
        $num = $this->num;  // 剩餘可分配的砍價個數
        $amount = $this->amount;  //剩餘可領取的砍價金額
        while ($num >= 1) {
            // 剩餘一個的時候，直接取剩餘砍價
            if ($num == 1) {
                $coupon_amount = $this->decimal_number($amount);
            } else {
                $avg_amount = $this->decimal_number($amount / $num);  // 剩餘的砍價的平均金額
                $coupon_amount = $this->decimal_number(
                    $this->calcCouponAmount($avg_amount, $amount, $num)
                );
            }
            $this->items[] = $coupon_amount; // 追加分配
            $amount -= $coupon_amount;
            --$num;
        }
        shuffle($this->items);  // 隨機打亂
    }

    /**
     * 計算分配的砍價金額
     * @param float $avg_amount 每次計算的平均金額
     * @param float $amount 剩餘可領取金額
     * @param int $num 剩餘可領取的砍價個數
     *
     * @return float
     */
    protected function calcCouponAmount($avg_amount, $amount, $num)
    {
        // 如果平均金額小於等於最低金額，則直接返回最低金額
        if ($avg_amount <= $this->coupon_min) {
            return $this->coupon_min;
        }
        // 浮動計算
        $coupon_amount = $this->decimal_number($avg_amount * (1 + $this->apportionRandRatio()));
        // 如果低於最低金額或超過可領取的最大金額，則重新獲取
        if ($coupon_amount < $this->coupon_min
            || $coupon_amount > $this->calcCouponAmountMax($amount, $num)
        ) {
            return $this->calcCouponAmount($avg_amount, $amount, $num);
        }
        return $coupon_amount;
    }

    /**
     * 計算分配的砍價金額-可領取的最大金額
     */
    protected function calcCouponAmountMax($amount, $num)
    {
        return $this->coupon_min + $amount - $num * $this->coupon_min;
    }

    /**
     * 砍價金額浮動比例
     */
    protected function apportionRandRatio()
    {
        // 60%機率獲取剩餘平均值的大幅度砍價（可能正數、可能負數）
        if (rand(1, 100) <= 60) {
            return rand(-70, 70) / 100; // 上下幅度70%
        }
        return rand(-30, 30) / 100; // 其他情況，上下浮動30%；
    }

    /**
     * 格式化金額，保留2位
     */
    protected function decimal_number($amount)
    {
        return sprintf('%01.2f', round($amount, 2));
    }
}