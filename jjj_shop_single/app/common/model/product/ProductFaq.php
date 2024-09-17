<?php

namespace app\common\model\product;

use app\common\model\BaseModel;
/**
 * 商品問答
 */
class ProductFaq extends BaseModel
{
    protected $px = 'faq_id';
    protected $name = 'product_faq';


    /**
     * 商品
     */
    public function product()
    {
        return $this->belongsTo('app\\common\\model\\product\\Product', 'product_id', 'product_id');
    }


    /**
     * 獲取評價列表
     */
    public function getList($params)
    {
        $model = $this;
//        if (isset($params['question']) && !empty(trim($params['question']))) {
//            $model1 = new Product();
//            $res = $model1->getWhereData($params['question'])->toArray();
//            $str = implode(',', array_column($res, 'product_id'));
//            $model = $model->where('product_id', 'in', $str);
//        }
//        if (isset($params['score']) && $params['score'] > 0) {
//            $model = $model->where('score', '=', $params['score']);
//        }
//        if (isset($params['status']) && $params['status'] > -1) {
//            $model = $model->where('status', '=', $params['status']);
//        }

        return $model->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($params);
    }
}
