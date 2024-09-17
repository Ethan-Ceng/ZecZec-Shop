<?php

namespace app\api\model\plus\agent;

use app\common\model\plus\agent\Order as OrderModel;
use app\common\service\order\OrderService;
use app\common\enum\order\OrderTypeEnum;

/**
 * 分銷商訂單模型
 */
class Order extends OrderModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'update_time',
    ];

    /**
     * 獲取分銷商訂單列表
     */
    public function getList($user_id, $is_settled = -1)
    {
        $model = $this;
        $is_settled > -1 && $model = $model->where('is_settled', '=', !!$is_settled);
        $data = $model->with(['user'])
            ->where('first_user_id|second_user_id|third_user_id', '=', $user_id)
            ->where('is_invalid', '=', 0)
            ->order(['create_time' => 'desc'])
            ->paginate(15);
        if ($data->isEmpty()) {
            return $data;
        }
        // 整理訂單資訊
        $with = ['product' => ['image', 'refund'], 'address', 'user'];
        return OrderService::getOrderList($data, 'order_master', $with);
    }

    /**
     * 建立分銷商訂單記錄
     */
    public static function createOrder($order, $order_type = OrderTypeEnum::MASTER)
    {
        // 分銷訂單模型
        $model = new self;
        // 分銷商基本設定
        $setting = Setting::getItem('basic', $order['app_id']);
        // 是否開啟分銷功能
        if (!$setting['is_open']) {
            return false;
        }
        // 獲取當前買家的所有上級分銷商使用者id
        $agentUser = $model->getAgentUserId($order['user_id'], $setting['level'], $setting['self_buy']);
        // 非分銷訂單
        if (!$agentUser['first_user_id']) {
            return false;
        }
        $model['first_user_id'] = $agentUser['first_user_id'];
        $model['second_user_id'] = $agentUser['second_user_id'];
        $model['third_user_id'] = $agentUser['third_user_id'];
        // 計算訂單分銷佣金
        $capital = $model->getCapitalByOrder($order, 'create');
        if (!$capital['is_record']) {
            return false;
        }
        // 儲存分銷訂單記錄
        return $model->save([
            'user_id' => $order['user_id'],
            'order_id' => $order['order_id'],
            'order_type' => $order_type,
            'order_price' => $capital['orderPrice'],
            'first_money' => $agentUser['first_user_id'] > 0 ? max($capital['first_money'], 0) : 0,
            'second_money' => $agentUser['second_user_id'] > 0 ? max($capital['second_money'], 0) : 0,
            'third_money' => $agentUser['third_user_id'] > 0 ? max($capital['third_money'], 0) : 0,
            'first_user_id' => $agentUser['first_user_id'],
            'second_user_id' => $agentUser['second_user_id'],
            'third_user_id' => $agentUser['third_user_id'],
            'is_settled' => 0,
            'app_id' => $order['app_id']
        ]);
    }

    /**
     * 獲取當前買家的所有上級分銷商使用者id
     */
    private function getAgentUserId($user_id, $level, $self_buy)
    {
        $agentUser = [
            'first_user_id' => $level >= 1 ? Referee::getRefereeUserId($user_id, 1, true) : 0,
            'second_user_id' => $level >= 2 ? Referee::getRefereeUserId($user_id, 2, true) : 0,
            'third_user_id' => $level == 3 ? Referee::getRefereeUserId($user_id, 3, true) : 0
        ];
        // 分銷商自購
        if ($self_buy && User::isAgentUser($user_id)) {
            return [
                'first_user_id' => $user_id,
                'second_user_id' => $agentUser['first_user_id'],
                'third_user_id' => $agentUser['second_user_id'],
            ];
        }
        return $agentUser;
    }

}
