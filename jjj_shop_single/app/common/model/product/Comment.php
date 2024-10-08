<?php

namespace app\common\model\product;

use app\common\model\BaseModel;

/**
 * 評論模型
 */
class Comment extends BaseModel
{
    protected $name = 'comment';
    protected $px = 'comment_id';

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
     * 關聯評價圖片表
     */
    public function image()
    {
        return $this->hasMany('app\\common\\model\\product\\CommentImage', 'comment_id', 'comment_id')->order(['id' => 'asc']);
    }

    /**
     * 評價詳情
     */
    public function detail($comment_id)
    {
        return $this->where('comment_id', '=', $comment_id)->with(['user','image.file', 'orderM', 'product.image.file'])->find();
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
        if (isset($params['score']) && $params['score'] > 0) {
            $model = $model->where('score', '=', $params['score']);
        }
        if (isset($params['status']) && $params['status'] > -1) {
            $model = $model->where('status', '=', $params['status']);
        }

        return $model->with(['user', 'orderM', 'product.image.file','image.file'])
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($params);
    }

    /**
     * 獲取評論數
     */
    public function getStatusNum($where)
    {
        return $this->where($where)->count();
    }

}