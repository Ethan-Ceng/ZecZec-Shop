<?php

namespace app\common\model\plus\live;

use app\common\model\BaseModel;

/**
 * 直播間商品模型
 */
class WxLiveProduct extends BaseModel
{
    protected $name = 'app_wx_live_product';
    protected $pk = 'live_product_id';

    /**
     * 關聯商品
     */
    public function product()
    {
        return $this->BelongsTo('app\\common\\model\\plus\\live\\WxProduct', 'goods_id', 'goods_id');
    }

    /**
     * 詳情
     */
    public static function detail($live_product_id)
    {
        return (new static())->find($live_product_id);
    }

}
