<?php

namespace app\shop\model\plus\coupon;

use app\common\model\plus\coupon\Coupon as CouponModel;

/**
 * 優惠券模型
 */
class Coupon extends CouponModel
{
    /**
     * 獲取優惠券列表
     */
    public function getList($data)
    {
        return $this->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($data);
    }

    /**
     * 新增新記錄
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        if ($data['expire_type'] == '20') {
            $data['start_time'] = strtotime($data['active_time'][0]);
            $data['end_time'] = strtotime($data['active_time'][1]);
        }
        // 限制商品id
        if($data['apply_range'] == 20){
            $data['product_ids'] = implode(',', $data['product_ids']);
        }
        $category_first_ids = [];
        $category_second_ids = [];
        if($data['apply_range'] == 30){
            if(isset($data['category_list']['first'])){
                foreach($data['category_list']['first'] as $item){
                    array_push($category_first_ids, $item['category_id']);
                }
            }
            if(isset($data['category_list']['second'])) {
                foreach ($data['category_list']['second'] as $item) {
                    array_push($category_second_ids, $item['category_id']);
                }
            }
            $data['category_ids'] = [
                'first' => $category_first_ids,
                'second' => $category_second_ids
            ];
            $data['category_ids'] = json_encode($data['category_ids']);
        }
        return self::create($data);
    }

    /**
     * 更新記錄
     */
    public function edit($data)
    {
        if ($data['expire_type'] == '20') {
            $data['start_time'] = strtotime($data['active_time'][0]);
            $data['end_time'] = strtotime($data['active_time'][1]);
        }
        // 限制商品id
        if($data['apply_range'] == 20){
            $data['product_ids'] = implode(',', $data['product_ids']);
        }
        $category_first_ids = [];
        $category_second_ids = [];
        if($data['apply_range'] == 30){
            if(isset($data['category_list']['first'])){
                foreach($data['category_list']['first'] as $item){
                    array_push($category_first_ids, $item['category_id']);
                }
            }
            if(isset($data['category_list']['second'])) {
                foreach ($data['category_list']['second'] as $item) {
                    array_push($category_second_ids, $item['category_id']);
                }
            }
            $data['category_ids'] = [
                'first' => $category_first_ids,
                'second' => $category_second_ids
            ];
            $data['category_ids'] = json_encode($data['category_ids']);
        }
        $where['coupon_id'] = $data['coupon_id'];
        unset($data['coupon_id']);
        return self::update($data, $where);
    }

    /**
     * 刪除記錄 (軟刪除)
     */
    public function setDelete($where)
    {
        return self::update(['is_delete' => 1], $where);
    }

    /**
     * 查詢指定優惠券
     * @param $value
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
    /**
     * 查詢指定優惠券
     * @param $value
     */
    public function getCoupons($value)
    {
        $data = $this->where('coupon_id', 'in', $value)->select();
        $name = '';
        if (!empty($data)) {
            foreach ($data as $val) {
                $name .= $val['name'] . ',';
            }
        }

        return $name;
    }
}
