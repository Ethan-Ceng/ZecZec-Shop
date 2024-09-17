<?php


namespace app\common\model\store;

use app\common\model\BaseModel;

use app\common\enum\order\OrderTypeEnum;

/**
 * 商家門店核銷訂單記錄模型
 */
class Order extends BaseModel
{
    protected $pk = 'id';
    protected $name = 'store_order';
    protected $updateTime = false;

    /**
     * 關聯門店表
     */
    public function store()
    {
        return $this->BelongsTo("app\\common\\model\\store\\Store", 'store_id', 'store_id');
    }

    /**
     * 關聯店員表
     */
    public function clerk()
    {
        return $this->BelongsTo("app\\common\\model\\store\\Clerk", 'clerk_id', 'clerk_id');
    }

    /**
     * 關聯訂單表
     */
    public function order()
    {
        return $this->BelongsTo("app\\common\\model\\order\\Order", 'order_id', 'order_id');
    }

    /**
     * 訂單型別
     */
    public function getOrderTypeAttr($value)
    {
        if ($value == 10) {
            $text = '商城訂單';
        }
        if ($value == 20) {
            $text = '拼團訂單';
        }
        if ($value == 100) {
            $text = '餘額充值';
        }
        return ['text' => $text, 'value' => $value];
    }

    /**
     * 新增核銷記錄
     */
    public static function add(
        $order_id,
        $store_id,
        $clerk_id,
        $order_type = OrderTypeEnum::MASTER
    )
    {
        return (new static)->save([
            'order_id' => $order_id,
            'order_type' => $order_type,
            'store_id' => $store_id,
            'clerk_id' => $clerk_id,
            'app_id' => self::$app_id
        ]);
    }

}