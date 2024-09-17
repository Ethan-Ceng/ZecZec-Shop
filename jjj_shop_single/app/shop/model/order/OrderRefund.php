<?php

namespace app\shop\model\order;

use app\common\model\order\OrderRefund as OrderRefundModel;
use app\shop\model\user\User as UserModel;
use app\common\service\order\OrderRefundService;
use app\common\service\message\MessageService;

/**
 * 售後管理模型
 */
class OrderRefund extends OrderRefundModel
{

    /**
     * 獲取售後單列表
     */
    public function getList($query = [])
    {

        $model = $this;
        // 查詢條件：訂單號
        if (isset($query['order_no']) && !empty($query['order_no'])) {
            $model = $model->where('order.order_no', 'like', "%{$query['order_no']}%");
        }
        // 查詢條件：起始日期
        if (isset($query['create_time']) && !empty($query['create_time'])) {
            $sta_time = array_shift($query['create_time']);
            $end_time = array_pop($query['create_time']);
            $model = $model->whereBetweenTime('m.create_time', $sta_time, date('Y-m-d 23:59:59', strtotime($end_time)));
        }
        // 售後型別
        if (isset($query['type']) && $query['type'] > 0) {
            $model = $model->where('m.type', '=', $query['type']);
        }

        // 售後單狀態(選項卡)
        if (isset($query['status']) && $query['status'] >= 0) {
            $model = $model->where('m.status', '=', $query['status']);
        }

        // 獲取列表資料
        $list = $model->alias('m')
            ->field('m.*, order.order_no')
            ->with(['orderproduct.image', 'orderMaster.advance', 'user'])
            ->join('order', 'order.order_id = m.order_id')
            ->order(['m.create_time' => 'desc'])
            ->paginate($query);
        foreach ($list as &$item) {
            if ($item['orderMaster']['order_source'] == 80) {
                $item['orderproduct']['total_pay_price'] = round($item['orderproduct']['total_pay_price'] + $item['orderMaster']['advance']['pay_price'], 2);
            }
        }
        return $list;
    }

    public function groupCount($query)
    {
        $model = $this;
        // 查詢條件：訂單號
        if (isset($query['order_no']) && !empty($query['order_no'])) {
            $model = $model->where('order.order_no', 'like', "%{$query['order_no']}%");
        }
        // 查詢條件：起始日期
        if (isset($query['create_time']) && !empty($query['create_time'])) {
            $sta_time = array_shift($query['create_time']);
            $end_time = array_pop($query['create_time']);
            $model = $model->whereBetweenTime('m.create_time', $sta_time, $end_time);
        }
        // 售後型別
        if (isset($query['type']) && $query['type'] > 0) {
            $model = $model->where('m.type', '=', $query['type']);
        }

        // 獲取列表資料
        return $model->alias('m')
            ->field('m.status,COUNT(*) as total')
            ->join('order', 'order.order_id = m.order_id')
            ->group('m.status')->select()->toArray();
    }

    /**
     * 商家稽核
     */
    public function audit($data)
    {
        if ($data['is_agree'] == 20 && empty($data['refuse_desc'])) {
            $this->error = '請輸入拒絕原因';
            return false;
        }
        if ($data['is_agree'] == 10 && $this['type']['value'] != 30 && empty($data['address_id'])) {
            $this->error = '請選擇退貨地址';
            return false;
        }
        $this->startTrans();
        try {
            // 拒絕申請, 標記售後單狀態為已拒絕
            $data['is_agree'] == 20 && $data['status'] = 10;
            // 同意換貨申請, 標記售後單狀態為已完成
//            $data['is_agree'] == 10 && $this['type']['value'] == 20 && $data['status'] = 20;
            // 更新退款單狀態
            $this->save($data);
            // 同意售後申請, 記錄退貨地址
            if ($data['is_agree'] == 10 && $this['type']['value'] != 30) {
                $model = new OrderRefundAddress();
                $model->add($this['order_refund_id'], $data['address_id']);
            }
            // 訂單詳情
            $order = Order::detail($this['order_id']);
            // 傳送模板訊息
            (new MessageService)->refund(self::detail($this['order_refund_id']), $order['order_no'], 'audit');
            // 如果是僅退款
            if ($data['is_agree'] == 10 && $this['type']['value'] == 30) {
                $total_refund = $this['orderproduct']['total_pay_price'];
                if ($order['order_source'] == 80 && $order['advance']['money_return'] == 1) {
                    $total_refund = round($total_refund + $order['advance']['pay_price'], 2);
                }
                if ($data['refund_money'] > $total_refund) {
                    $this->error = '退款金額不能大於商品實付款金額';
                    return false;
                }
                $this->refundMoney($order, $data);
            }
            // 事務提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 確認收貨並退款
     */
    public function receipt($data)
    {
        // 訂單詳情
        $order = Order::detail($this['order_id']);
        $total_refund = $this['orderproduct']['total_pay_price'];
        if ($order['order_source'] == 80 && $order['advance']['money_return'] == 1) {
            $total_refund = round($total_refund + $order['advance']['pay_price'], 2);

        }
        if ($data['refund_money'] > $total_refund) {
            $this->error = '退款金額不能大於商品實付款金額';
            return false;
        }
        $this->transaction(function () use ($order, $data) {
            $this->refundMoney($order, $data);
        });
        return true;
    }

    private function refundMoney($order, $data)
    {
        $advance_money = 0;//預售定金
        if ($order['order_source'] == 80 && $order['advance']['money_return'] == 1) {
            $totalMoney = round($order['pay_price'] + $order['advance']['pay_price'], 2);
            if ($data['refund_money'] > $order['pay_price']) {
                $data['refund_money'] = $order['pay_price'];
                $advance_money = round($totalMoney - $data['refund_money'], 2);
            }
        }
        $update = [
            'is_receipt' => 1,
            'status' => 20
        ];
        if ($this['type']['value'] == 20) {
            $update['send_express_id'] = $data['send_express_id'];
            $update['send_express_no'] = $data['send_express_no'];
            $update['deliver_time'] = time();
            $update['is_plate_send'] = 1;
        }
        $data['refund_money'] > 0 && $update['refund_money'] = $data['refund_money'];
        // 更新售後單狀態
        $this->save($update);
        // 消減使用者的實際消費金額
        // 條件：判斷訂單是否已結算
        if ($order['is_settled'] == true) {
            (new UserModel)->setDecUserExpend($order['user_id'], $data['refund_money']);
        }
        // 執行原路退款
        $data['refund_money'] > 0 && (new OrderRefundService)->execute($order, $data['refund_money']);
        $data['refund_money'] > 0 && $order->save(['refund_money' => $order['refund_money'] + $data['refund_money']]);
        //退預售定金
        $advance_money > 0 && (new OrderRefundService)->execute($order['advance'], $advance_money);
        $advance_money > 0 && $order['advance']->save(['refund_money' => $advance_money]);
        // 傳送模板訊息
        (new MessageService)->refund(self::detail($this['order_refund_id']), $order['order_no'], 'receipt');
    }


    /**
     * 統計售後訂單
     */
    public function getRefundOrderTotal()
    {
        $filter['is_agree'] = 0;
        return $this->where($filter)->count();
    }


    /**
     * 獲取退款訂單總數 (可指定某天)
     * 已同意的退款
     */
    public function getOrderRefundData($startDate, $endDate, $type)
    {
        $model = $this;
        $model = $model->where('create_time', '>=', strtotime($startDate));
        if (is_null($endDate)) {
            $model = $model->where('create_time', '<', strtotime($startDate) + 86400);
        } else {
            $model = $model->where('create_time', '<', strtotime($endDate) + 86400);
        }

        $model = $model->where('is_agree', '=', 10);

        if ($type == 'order_refund_money') {
            // 退款金額
            return $model->sum('refund_money');
        } else if ($type == 'order_refund_total') {
            // 退款數量
            return $model->count();
        }
        return 0;
    }
}