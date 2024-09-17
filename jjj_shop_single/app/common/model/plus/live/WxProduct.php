<?php

namespace app\common\model\plus\live;

use app\common\model\BaseModel;

/**
 * 直播商品模型
 */
class WxProduct extends BaseModel
{
    protected $name = 'app_wx_product';
    protected $pk = 'wx_product_id';
    //附加欄位
    protected $append = ['price_type_text', 'price_text', 'audit_status_text'];

    /**
     * 價格型別
     */
    public function getPriceTypeTextAttr($value, $data)
    {
        return $this->getPriceType()[$data['price_type']]['name'];
    }

    /**
     * 價格
     */
    public function getPriceTextAttr($value, $data)
    {
        $price = '';
        if ($data['price_type'] == 1) {
            $price = $data['price'];
        } elseif ($data['price_type'] == 2) {
            $price = $data['price'] . '-' . $data['price2'];
        } else {
            $price = '原價:' . $data['price'] . ',折扣價:' . $data['price2'];
        }
        return $price;
    }

    /**
     * 稽核狀態
     */
    public function getAuditStatusTextAttr($value, $data)
    {
        return $this->getAuditStatus()[$data['audit_status']]['name'];
    }

    /**
     * 關聯商品
     */
    public function product()
    {
        return $this->BelongsTo('app\\common\\model\\product\\Product', 'product_id', 'product_id');
    }

    /**
     * 詳情
     */
    public static function detail($wx_product_id)
    {
        return (new static())->find($wx_product_id);
    }

    /**
     * 價格型別
     */
    public function getPriceType()
    {
        $type = [
            1 => ['value' => 1, 'name' => '一口價'],
            2 => ['value' => 2, 'name' => '價格區間'],
            3 => ['value' => 3, 'name' => '顯示折扣價'],
        ];
        return $type;
    }

    /**
     * 稽核狀態 0：未稽核。1：稽核中，2：稽核透過，3：稽核駁回
     */
    public function getAuditStatus()
    {
        $status = [
            0 => ['value' => 0, 'name' => '未稽核'],
            1 => ['value' => 1, 'name' => '稽核中'],
            2 => ['value' => 2, 'name' => '稽核透過'],
            3 => ['value' => 3, 'name' => '稽核駁回'],
        ];
        return $status;
    }

}
