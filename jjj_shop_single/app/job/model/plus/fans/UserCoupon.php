<?php

namespace app\job\model\plus\fans;

use app\common\model\plus\coupon\UserCoupon as UserCouponModel;

/**
 * 使用者優惠券模型
 */
class UserCoupon extends UserCouponModel
{
    /**
     * 獲取已過期的優惠券ID集
     */
    public function getExpiredCouponIds()
    {
        $time = time();
        return $this->where('is_expire', '=', 0)
            ->where('is_use', '=', 0)
            ->where(
                "IF ( `expire_type` = 20,
                    (`end_time` + 86400) < {$time},
                    ( `create_time` + (`expire_day` * 86400)) < {$time} )"
            )->column('user_coupon_id');
    }

    /**
     * 設定優惠券過期狀態
     */
    public function setIsExpire($couponIds)
    {
        if (empty($couponIds)) {
            return false;
        }
        return $this->where('user_coupon_id', 'in', $couponIds)->save(['is_expire' => 1]);
    }

}
