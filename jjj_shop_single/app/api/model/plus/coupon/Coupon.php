<?php

namespace app\api\model\plus\coupon;

use app\common\model\plus\coupon\Coupon as CouponModel;
use app\api\model\product\Category as CategoryModel;

/**
 * 優惠券模型
 */
class Coupon extends CouponModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'receive_num',
        'is_delete',
        'create_time',
        'update_time',
    ];

    /**
     * 獲取使用者優惠券總數量(可用)
     */
    public function getCount($user_id)
    {
        return $this->where('user_id', '=', $user_id)
            ->where('is_use', '=', 0)
            ->where('is_expire', '=', 0)
            ->count();
    }

    /**
     * 獲取使用者優惠券ID集
     */
    public function getUserCouponIds($user_id)
    {
        return $this->where('user_id', '=', $user_id)->column('coupon_id');
    }

    /**
     * 領取優惠券
     */
    public function receive($user, $coupon_id)
    {
        // 獲取優惠券資訊
        $coupon = Coupon::detail($coupon_id);
        // 驗證優惠券是否可領取
        if (!$this->checkReceive()) {
            return false;
        }
        // 新增領取記錄
        return $this->add($user, $coupon);
    }

    /**
     * 新增領取記錄
     */
    private function add($user, $coupon)
    {
        // 計算有效期
        if ($coupon['expire_type'] == 10) {
            $start_time = time();
            $end_time = $start_time + ($coupon['expire_day'] * 86400);
        } else {
            $start_time = $coupon['start_time']['value'];
            $end_time = $coupon['end_time']['value'];
        }
        // 整理領取記錄
        $data = [
            'coupon_id' => $coupon['coupon_id'],
            'name' => $coupon['name'],
            'color' => $coupon['color']['value'],
            'coupon_type' => $coupon['coupon_type']['value'],
            'reduce_price' => $coupon['reduce_price'],
            'discount' => $coupon->getData('discount'),
            'min_price' => $coupon['min_price'],
            'expire_type' => $coupon['expire_type'],
            'expire_day' => $coupon['expire_day'],
            'start_time' => $start_time,
            'end_time' => $end_time,
            'apply_range' => $coupon['apply_range'],
            'user_id' => $user['user_id'],
            'app_id' => self::$app_id
        ];
        return $this->transaction(function () use ($data, $coupon) {
            // 新增領取記錄
            $status = $this->save($data);
            if ($status) {
                // 更新優惠券領取數量
                $coupon->setIncReceiveNum();
            }
            return $status;
        });
    }

    /**
     * 獲取優惠券列表
     */
    public function getList($user = false, $limit = null, $only_receive = false)
    {
        $model = $this;
        // 構造查詢條件
        $model = $model->where('is_delete', '=', 0);
        // 只顯示可領取(未過期,未發完)的優惠券
        if ($only_receive) {
            $model = $model->where('	IF ( `total_num` > - 1, `receive_num` < `total_num`, 1 = 1 )')
                ->where('IF ( `expire_type` = 20, (`end_time` + 86400) >= ' . time() . ', 1 = 1 )');
        }
        if ($limit != null) {
            $model = $model->limit($limit);
        }
        // 優惠券列表
        $couponList = $model->where('show_center', '=', 1)->order(['sort' => 'asc', 'create_time' => 'desc'])->select();
        // 獲取使用者已領取的優惠券
        foreach ($couponList as $key => $item) {
            $couponList[$key]['is_receive'] = false;
            $item['category_list'] = [];
            if ($item['apply_range'] == 30) {
                $category_ids = json_decode($item['category_ids'], true);
                $first = (new CategoryModel())->getListByIds($category_ids['first']);
                $first = $first ? $first->toArray() : [];
                $second = (new CategoryModel())->getListByIds($category_ids['second']);
                $second = $second ? $second->toArray() : [];
                $item['category_list'] = array_merge($first, $second);
            }
        }
        if ($user !== false) {
            $CouponModel = new UserCoupon();
            $userCouponIds = $CouponModel->getUserCouponIds($user['user_id']);
            foreach ($couponList as $key => $item) {
                $couponList[$key]['is_receive'] = in_array($item['coupon_id'], $userCouponIds);
            }
        }

        return $couponList;
    }

    /**
     * 待領取優惠券
     */
    public function getWaitList($user = false)
    {
        $model = $this;
        // 構造查詢條件
        $model = $model->where('is_delete', '=', 0);
        /*$model = $model->where('	IF ( `total_num` > - 1, `receive_num` <= `total_num`, 1 = 1 )')
            ->where('IF ( `expire_type` = 20, `end_time` >= ' . time() . ', 1 = 1 )');*/
        $model = $model//->where('total_num', '<>', -1)
        //->where('receive_num', '<', 'total_num')
        ->whereRaw('(total_num = -1) OR (receive_num < total_num)')
            ->whereRaw('(end_time = 0) OR (end_time > unix_timestamp())');
        $CouponModel = new UserCoupon();
        $userCouponIds = 0;
        $user && $userCouponIds = $CouponModel->getUserCouponIds($user['user_id']);
        $userCouponIds && $model = $model->where('coupon_id', 'not in', implode(',', $userCouponIds));
        // 是否顯示在領券中心
        $model = $model->where('show_center', '=', 1);
        // 使用者可領取優惠券列表
        return $model->order(['sort' => 'asc', 'create_time' => 'desc'])->select();
    }

    /**
     * 驗證優惠券是否可領取
     */
    public function checkReceive()
    {
        if ($this['total_num'] != -1 && $this['receive_num'] >= $this['total_num']) {
            $this->error = '優惠券已發完';
            return false;
        }
        if ($this['expire_type'] == 20 && ($this->getData('end_time') + 86400) < time()) {
            $this->error = '優惠券已過期';
            return false;
        }
        return true;
    }

    /**
     * 累計已領取數量
     */
    public function setIncReceiveNum()
    {
        return $this->where('coupon_id', '=', $this['coupon_id'])->inc('receive_num')->update();
    }

    public function getWhereData($coupon_arr)
    {
        return $this->where('coupon_id', 'in', $coupon_arr)->select();
    }

    /**
     * 查詢指定優惠券
     */
    public function getCoupon($value)
    {
        return $this->where('coupon_id', 'in', $value)->select();
    }

    /**
     * 查詢單個優惠券資訊
     * @param $value
     */
    public function getCouponInfo($value)
    {
        return $this->where('coupon_id', '=', $value)->find();
    }
}