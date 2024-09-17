<?php

namespace app\api\model\order;

use app\common\model\order\OrderRefund as OrderRefundModel;

/**
 * 售後單模型
 */
class OrderRefund extends OrderRefundModel
{
    /**
     * 隱藏欄位
     * @var array
     */
    protected $hidden = [
        'app_id',
        'update_time'
    ];

    /**
     * 追加欄位
     * @var array
     */
    protected $append = [
        'state_text',   // 售後單狀態文字描述
    ];

    /**
     * 售後單狀態文字描述
     */
    public function getStateTextAttr($value, $data)
    {
        // 已完成
        if ($data['status'] == 20) {
            $text = [10 => '已同意退貨並已退款', 20 => '已同意換貨並已收貨', '30' => '僅退款並已退款'];
            return $text[$data['type']];
        }
        // 已取消
        if ($data['status'] == 30) {
            return '已取消';
        }
        // 已拒絕
        if ($data['status'] == 10) {
            $text = [10 => '已拒絕退貨退款', 20 => '已拒絕換貨', '30' => '已拒絕退款'];
            return $text[$data['type']];
        }
        // 進行中
        if ($data['status'] == 0) {
            if ($data['is_agree'] == 0) {
                return '等待稽核中';
            }
            if ($data['type'] == 10) {
                return $data['is_user_send'] ? '已發貨，待平臺確認' : '已同意退貨，請及時發貨';
            }
            if ($data['type'] == 20) {
                return $data['is_user_send'] ? '已發貨，待平臺確認' : '已同意換貨，請及時發貨';
            }
        }
        return $value;
    }

    /**
     * 獲取使用者售後單列表
     */
    public function getList($user_id, $state, $limit)
    {
        $model = $this;
        $state > -1 && $model = $this->where('status', '=', $state);

        $list = $model->with(['order_master.advance', 'orderproduct.image'])
            ->where('user_id', '=', $user_id)
            ->order(['create_time' => 'desc'])
            ->paginate($limit);
        foreach ($list as &$item) {
            if ($item['orderMaster']['order_source'] == 80) {
                $item['orderproduct']['total_pay_price'] = round($item['orderproduct']['total_pay_price'] + $item['order_master']['advance']['pay_price'], 2);
            }
        }
        return $list;
    }

    /**
     * 使用者發貨
     */
    public function delivery($data)
    {
        if ($this['is_agree']['value'] != 10 || $this['is_user_send'] != 0) {
            $this->error = '當前售後單不合法，不允許該操作';
            return false;
        }
        if ($data['express_id'] <= 0) {
            $this->error = '請選擇物流公司';
            return false;
        }
        if (empty($data['express_no'])) {
            $this->error = '請填寫物流單號';
            return false;
        }
        return $this->save([
            'is_user_send' => 1,
            'send_time' => time(),
            'express_id' => (int)$data['express_id'],
            'express_no' => $data['express_no'],
        ]);
    }

    /**
     * 新增售後單記錄
     */
    public function apply($user, $product, $data)
    {
        $this->startTrans();
        try {
            // 新增售後單記錄
            $this->save([
                'order_product_id' => $data['order_product_id'],
                'order_id' => $product['order_id'],
                'user_id' => $user['user_id'],
                'type' => $data['type'],
                'apply_desc' => $data['content'],
                'is_agree' => 0,
                'status' => 0,
                'app_id' => self::$app_id,
            ]);
            // 記錄憑證圖片關係
            if (isset($data['images']) && !empty($data['images'])) {
                $this->saveImages($this['order_refund_id'], $data['images']);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 記錄售後單圖片
     */
    private function saveImages($order_refund_id, $images)
    {
        $images_ids = [];
        foreach (json_decode($images, true) as $val) {
            $images_ids[] = $val['file_id'];
        }
        // 生成評價圖片資料
        $data = [];
        foreach ($images_ids as $image_id) {
            $data[] = [
                'order_refund_id' => $order_refund_id,
                'image_id' => $image_id,
                'app_id' => self::$app_id
            ];
        }
        return !empty($data) && (new OrderRefundImage)->saveAll($data);
    }

    /**
     * 是否允許售後
     */
    public function allowRefund($order_id, $order_product_id)
    {
        return $this->where('order_id', '=', $order_id)
            ->where('is_agree', 'in', '0,10')
            ->where('order_product_id', '=', $order_product_id)
            ->find();
    }

}