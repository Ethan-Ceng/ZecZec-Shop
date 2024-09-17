<?php

namespace app\common\model\plus\coupon;

//use think\Hook;
use app\common\model\BaseModel;

/**
 * 使用者優惠券模型
 */
class UserCoupon extends BaseModel
{
    protected $name = 'user_coupon';
    protected $pk = 'user_coupon_id';

    /**
     * 追加欄位
     * @var array
     */
    protected $append = ['state'];

    /**
     * 關聯使用者表
     */
    public function user()
    {
        return $this->belongsTo('app\common\model\user\User');
    }
    /**
     * 關聯優惠券表
     */
    public function coupon()
    {
        return $this->belongsTo('app\common\model\plus\coupon\Coupon');
    }
    /**
     * 優惠券狀態
     */
    public function getStateAttr($value, $data)
    {
        if ($data['is_use']) {
            return ['text' => '已使用', 'value' => 0];
        }
        if ($data['is_expire']) {
            return ['text' => '已過期', 'value' => 0];
        }
        return ['text' => '', 'value' => 1];
    }

    /**
     * 優惠券顏色
     */
    public function getColorAttr($value)
    {
        $status = [10 => 'blue', 20 => 'red', 30 => 'violet', 40 => 'yellow'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 優惠券型別
     */
    public function getCouponTypeAttr($value)
    {
        $status = [10 => '滿減券', 20 => '折扣券'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 折扣率
     */
    public function getDiscountAttr($value)
    {
        return $value / 10;
    }

    /**
     * 有效期-開始時間
     */
    public function getStartTimeAttr($value)
    {
        return ['text' => date('Y/m/d', $value), 'value' => $value];
    }

    /**
     * 有效期-結束時間
     */
    public function getEndTimeAttr($value)
    {
        return ['text' => date('Y/m/d', $value), 'value' => $value];
    }

    /**
     * 優惠券詳情
     */
    public static function detail($coupon_id)
    {
        return (new static())->find($coupon_id);
    }

    /**
     * 設定優惠券使用狀態
     * @param $couponId
     * @param bool $isUse
     * @return UserCoupon
     */
    public static function setIsUse($couponId, $isUse = true)
    {
        $model = new static();
        return $model->where(['user_coupon_id' => $couponId])->update(['is_use' => (int)$isUse]);
    }

}