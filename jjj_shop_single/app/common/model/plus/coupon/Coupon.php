<?php

namespace app\common\model\plus\coupon;

use app\common\library\helper;
use app\common\model\BaseModel;

/**
 * 優惠券模型
 */
class Coupon extends BaseModel
{
    protected $name = 'coupon';
    protected $pk = 'coupon_id';
    /**
     * 追加欄位
     * @var array
     */
    protected $append = [
        'state'
    ];

    /**
     * 優惠券狀態 (是否可領取)
     * @param $value
     * @param $data
     * @return array
     */
    public function getStateAttr($value, $data)
    {
        if (isset($data['is_receive']) && $data['is_receive']) {
            return ['text' => '已領取', 'value' => 0];
        }

        if (isset($data['total_num']) && $data['total_num'] > -1 && $data['receive_num'] >= $data['total_num']) {
            return ['text' => '已搶光', 'value' => 0];
        }
        if (isset($data['expire_type']) && $data['expire_type'] == 20 && ($data['end_time'] + 86400) < time()) {
            return ['text' => '已過期', 'value' => 0];
        }
        return ['text' => '', 'value' => 1];
    }

    /**
     * 優惠券顏色
     * @param $value
     * @return array
     */
    public function getColorAttr($value)
    {
        $status = [10 => 'blue', 20 => 'red', 30 => 'violet', 40 => 'yellow'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 優惠券型別
     * @param $value
     * @return array
     */
    public function getCouponTypeAttr($value)
    {
        $status = [10 => '滿減券', 20 => '折扣券'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 折扣率
     * @param $value
     * @return float|int
     */
    public function getDiscountAttr($value)
    {
        return $value / 10;
    }

    /**
     * 有效期-開始時間
     * @param $value
     * @return array
     */
    public function getStartTimeAttr($value)
    {
        return ['text' => date('Y-m-d', $value), 'value' => $value];
    }

    /**
     * 有效期-結束時間
     * @param $value
     * @return array
     */
    public function getEndTimeAttr($value)
    {
        return ['text' => date('Y-m-d', $value), 'value' => $value];
    }

    /**
     * 折扣率
     */
    public function setDiscountAttr($value)
    {
        return helper::bcmul($value, 10, 0);
    }

    /**
     * 優惠券詳情
     * @param $coupon_id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detail($coupon_id)
    {
        return (new static())->find($coupon_id);
    }

}
