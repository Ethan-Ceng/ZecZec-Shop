<?php

namespace app\shop\model\plus\giftpackage;

use app\common\model\plus\giftpackage\Order as OrderModel;

/**
 * Class Ordre
 * 禮包購訂單
 * @package app\shop\model\plus\giftpackage
 */
class Order extends OrderModel
{
    /**
     * 訂單列表
     * @param $data
     */
    public function getList($data)
    {
        $model = $this;
        if ($data['search'] != '') {
            $model = $model->where('user.nickName|user.mobile', 'like', '%' . trim($data['search']) . '%');
        }
        return $model->alias('order')->field('order.*')->with(['user'])
            ->join('user', 'user.user_id = order.user_id', 'left')
            ->where('order.gift_package_id', '=', $data['id'])
            ->order('order.create_time', 'desc')
            ->paginate($data);
    }
}