<?php

namespace app\shop\model\plus\agent;

use app\common\model\plus\agent\Order as OrderModel;
use app\common\service\order\OrderService;
use app\shop\service\order\ExportService;

/**
 * 分銷商訂單模型
 */
class Order extends OrderModel
{
    /**
     * 獲取分銷商訂單列表
     */
    public function getList($param)
    {
        $model = $this->alias('agent');
        // 檢索查詢條件
        if (isset($param['user_id']) && $param['user_id'] > 1) {
            $model = $model->where('first_user_id|second_user_id|third_user_id', '=', $param['user_id']);
        }
        if (isset($param['is_settled']) && $param['is_settled'] > -1) {
            $model = $model->where('agent.is_settled', '=', $param['is_settled']);
        }
        //搜尋訂單號
        if (isset($param['order_no']) && $param['order_no']) {
            $model = $model->where('order.order_no', 'like', '%' . trim($param['order_no']) . '%');
        }
        //搜尋使用者資訊
        if (isset($param['search']) && $param['search']) {
            $model = $model->where('user.nickName|user.mobile', 'like', '%' . $param['search'] . '%');
        }
        // 獲取分銷商訂單列表
        $data = $model->with([
            'agent_first',
            'agent_second',
            'agent_third'
        ])
            ->join('user', 'user.user_id=agent.user_id')
            ->join('order', 'order.order_id=agent.order_id')
            ->field('agent.*')
            ->order(['agent.create_time' => 'desc'])
            ->paginate($param);
        if ($data->isEmpty()) {
            return $data;
        }
        // 獲取訂單的主資訊
        $with = ['product' => ['image', 'refund'], 'address', 'user'];
        return OrderService::getOrderList($data, 'order_master', $with);
    }

    /**
     * 訂單匯出
     */
    public function exportList($param)
    {
        $model = $this->alias('agent');
        // 檢索查詢條件
        if (isset($param['user_id']) && $param['user_id'] > 1) {
            $model = $model->where('first_user_id|second_user_id|third_user_id', '=', $param['user_id']);
        }
        if (isset($param['is_settled']) && $param['is_settled'] > -1) {
            $model = $model->where('agent.is_settled', '=', $param['is_settled']);
        }
        //搜尋訂單號
        if (isset($param['order_no']) && $param['order_no']) {
            $model = $model->where('order.order_no', 'like', '%' . trim($param['order_no']) . '%');
        }
        //搜尋使用者資訊
        if (isset($param['search']) && $param['search']) {
            $model = $model->where('user.nickName|user.mobile', 'like', '%' . $param['search'] . '%');
        }
        // 獲取分銷商訂單列表
        $data = $model->with([
            'agent_first',
            'agent_second',
            'agent_third'
        ])
            ->join('user', 'user.user_id=agent.user_id')
            ->join('order', 'order.order_id=agent.order_id')
            ->field('agent.*')
            ->order(['agent.create_time' => 'desc'])
            ->select();
        // 獲取訂單的主資訊
        $with = ['product' => ['image', 'refund'], 'address', 'user'];
        $list = OrderService::getOrderList($data, 'order_master', $with);
        // 匯出excel檔案
        (new Exportservice)->agentOrderList($list);
    }

}