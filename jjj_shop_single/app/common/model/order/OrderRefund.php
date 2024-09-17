<?php

namespace app\common\model\order;

use app\common\model\BaseModel;

/**
 * 售後管理模型
 */
class OrderRefund extends BaseModel
{
    protected $name = 'order_refund';
    protected $pk = 'order_refund_id';

    /**
     * 關聯使用者表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User');
    }

    /**
     * 關聯訂單主表
     */
    public function orderMaster()
    {
        return $this->belongsTo('app\\common\\model\\order\\Order');
    }

    /**
     * 關聯訂單商品表
     */
    public function orderproduct()
    {
        return $this->belongsTo('app\\common\\model\\order\\OrderProduct', 'order_product_id', 'order_product_id');
    }

    /**
     * 關聯圖片記錄表
     */
    public function image()
    {
        return $this->hasMany('app\\common\\model\\order\\OrderRefundImage');
    }

    /**
     * 關聯物流公司表
     */
    public function express()
    {
        return $this->belongsTo('app\\api\\model\\settings\\Express');
    }

    /**
     * 關聯物流公司表
     */
    public function sendexpress()
    {
        return $this->belongsTo('app\\api\\model\\settings\\Express', 'send_express_id', 'express_id');
    }

    /**
     * 關聯使用者表
     */
    public function address()
    {
        return $this->hasOne('app\\api\\model\\order\\OrderRefundAddress');
    }

    /**
     * 售後型別
     */
    public function getTypeAttr($value)
    {
        $status = [10 => '退貨退款', 20 => '換貨', 30 => '僅退款'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 商家是否同意售後
     */
    public function getIsAgreeAttr($value)
    {
        $status = [0 => '待稽核', 10 => '已同意', 20 => '已拒絕'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 售後單狀態
     */
    public function getStatusAttr($value)
    {
        $status = [0 => '進行中', 10 => '已拒絕', 20 => '已完成', 30 => '已取消'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 售後單詳情
     */
    public static function detail($where)
    {
        is_array($where) ? $filter = $where : $filter['order_refund_id'] = (int)$where;
        return (new static())->with(['order_master.advance', 'image.file', 'orderproduct.image', 'express', 'address', 'user', 'sendexpress'])->where($filter)->find();
    }

}