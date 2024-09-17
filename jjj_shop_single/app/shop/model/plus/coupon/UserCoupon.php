<?php

namespace app\shop\model\plus\coupon;

use app\common\exception\BaseException;
use app\shop\model\user\User;
use app\common\model\plus\coupon\UserCoupon as UserCouponModel;

/**
 * 使用者優惠券模型
 */
class UserCoupon extends UserCouponModel
{
    /**
     * 獲取優惠券列表
     */
    public function getList($limit = 20)
    {
        return $this->with(['user'])
            ->order(['create_time' => 'desc'])
            ->paginate($limit);
    }

    /**
     * 傳送優惠券
     * @param int $send_type 1給所有會員 2給指定等級的使用者 3給指定使用者傳送
     * @param int $coupon_id
     * @param int $user_level
     * @param string $user_ids
     */
    public function SendCoupon($data)
    {
        $send_type = $data['send_type'];
        $coupon_id = $data['coupon_id'];
        $user_level = $data['user_level'];
        $user_ids = $data['user_ids'];
        $user = new User();
        $coupon = Coupon::detail($coupon_id);
        if (empty($coupon)) {
            throw new BaseException(['msg' => '未找到優惠券資訊']);
            return false;
        }
        if ($send_type == 1) {
            $user_arr = $user->getUsers();
            if (count($user_arr) == 0) {
                throw new BaseException(['msg' => '沒有符合條件的會員']);
                return false;
            }
            $data = $this->setData($coupon, $user_arr);
        } elseif ($send_type == 2) {
            $user_arr = $user->getUsers(['grade_id' => $user_level]);
            if (count($user_arr) == 0) {
                throw new BaseException(['msg' => '沒有符合條件的會員']);
                return false;
            }
            $data = $this->setData($coupon, $user_arr);
        } elseif ($send_type == 3) {
            if ($user_ids == '') {
                throw new BaseException(['msg' => '請選擇使用者']);
                return false;
            }
            $user_ids = explode(',', $user_ids);
            $user_arr = [];
            foreach ($user_ids as $val) {
                $user_arr[]['user_id'] = $val;
            }
            $data = $this->setData($coupon, $user_arr);
        }
        return $this->saveAll($data);
    }

    /**
     * 陣列重組
     * @param $coupon
     * @param $user_arr
     */
    public function setData($coupon, $user_arr)
    {
        $data = [];
        foreach ($user_arr as $k => $val) {
            if ($coupon['expire_type'] == 10) {
                $start_time = time();
                $end_time = $start_time + ($coupon['expire_day'] * 86400);
            } else {
                $start_time = $coupon['start_time']['value'];
                $end_time = $coupon['end_time']['value'];
            }
            $data[$k]['coupon_id'] = $coupon['coupon_id'];
            $data[$k]['name'] = $coupon['name'];
            $data[$k]['color'] = $coupon['color']['value'];
            $data[$k]['coupon_type'] = $coupon['coupon_type']['value'];
            $data[$k]['reduce_price'] = $coupon['reduce_price'];
            $data[$k]['discount'] = $coupon['discount'] * 10;
            $data[$k]['min_price'] = $coupon['min_price'];
            $data[$k]['expire_type'] = $coupon['expire_type'];
            $data[$k]['expire_day'] = $coupon['expire_day'];
            $data[$k]['start_time'] = $start_time;
            $data[$k]['end_time'] = $end_time;
            $data[$k]['apply_range'] = $coupon['apply_range'];
            $data[$k]['app_id'] = self::$app_id;
            $data[$k]['user_id'] = $val['user_id'];
            $data[$k]['max_price'] = $coupon['max_price'];
        }
        return $data;
    }
}