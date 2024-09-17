<?php

namespace app\common\service\product;


/**
 * 商品服務類,公共處理方法
 */
class BaseProductService
{
    /**
     * 商品多規格資訊
     */
    public static function getSpecData($model = null)
    {
        // 商品sku資料
        if (!is_null($model) && $model['spec_type'] == 20) {
            return $model->getManySpecData($model['spec_rel'], $model['sku']);
        }
        return false;
    }

}