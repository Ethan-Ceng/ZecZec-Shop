<?php

namespace app\common\model;


use app\common\model\BaseModel;
use app\common\model\product\Product;

class MessageBox extends BaseModel
{
    protected $name = 'message_box';
    protected $pk = 'message_box_id';


    /**
     * 所屬訂單
     */
    public function orderM()
    {
        return $this->belongsTo('app\\common\\model\\order\\Order');
    }

    /**
     * 訂單商品
     */
    public function OrderProduct()
    {
        return $this->belongsTo('app\\common\\model\\order\\OrderProduct');
    }

    /**
     * 商品
     */
    public function product()
    {
        return $this->belongsTo('app\\common\\model\\product\\Product', 'product_id', 'product_id');
    }

    /**
     * 關聯使用者表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User', 'user_id', 'user_id');
    }


    /**
     * 關聯訊息表
     */
    public function message_items()
    {
        return $this->hasMany('app\common\model\MessageItem', 'message_box_id', 'message_box_id')->order(['create_time' => 'asc']);
    }

    // 傳送訊息
    public function sendMessage($userId, $productId, $orderId, $remark)
    {
        $data = [
            'user_id' => $userId,
            'product_id' => $productId,
            'order_id' => $orderId,
            'remark' => $remark,
        ];
        return $this->save($data);
    }

    /**
     * 獲取評價列表
     */
    public function getList($params)
    {
        $model = $this;
        if (isset($params['name']) && !empty(trim($params['name']))) {
            $model1 = new Product();
            $res = $model1->getWhereData($params['name'])->toArray();
            $str = implode(',', array_column($res, 'product_id'));
            $model = $model->where('product_id', 'in', $str);
        }

        return $model->with(['user', 'orderM', 'product','message_items'])
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($params);
    }
}