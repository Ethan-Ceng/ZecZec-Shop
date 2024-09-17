<?php

namespace app\common\model\order;

use app\common\model\BaseModel;

/**
 * 訂單商品模型
 */
class OrderProduct extends BaseModel
{
    protected $name = 'order_product';
    protected $pk = 'order_product_id';

    /**
     * 訂單商品列表
     * @return \think\model\relation\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo('app\\common\\model\\file\\UploadFile', 'image_id', 'file_id');
    }

    /**
     * 關聯商品表
     * @return \think\model\relation\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('app\\common\\model\\product\\Product');
    }

    /**
     * 關聯商品sku表
     * @return \think\model\relation\BelongsTo
     */
    public function sku()
    {
        return $this->belongsTo('app\\common\\model\\product\\ProductSku', 'spec_sku_id', 'spec_sku_id');
    }

    /**
     * 關聯訂單主表
     * @return \think\model\relation\BelongsTo
     */
    public function orderM()
    {
        return $this->belongsTo('Order', 'order_id', 'order_id');
    }

    /**
     * 售後單記錄表
     * @return \think\model\relation\HasOne
     */
    public function refund()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderRefund');
    }

    /**
     * 關聯分銷商
     * @return \think\model\relation\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo('app\\common\\model\\agent\\Apply', 'agent_user_id', 'user_id');
    }

    /**
     * 訂單商品詳情
     * @param $where
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detail($where)
    {
        return (new static())->with(['image', 'refund', 'orderM.advance'])->find($where);
    }

}