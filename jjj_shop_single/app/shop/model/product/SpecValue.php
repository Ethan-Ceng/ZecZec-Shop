<?php

namespace app\shop\model\product;

use app\common\model\product\SpecValue as SpecValueModel;

/**
 * 規格/屬性(值)模型
 */
class SpecValue extends SpecValueModel
{

    /**
     * 根據規格組名稱查詢規格id
     */
    public function getSpecValueIdByName($spec_id, $spec_value)
    {
        return self::where(compact('spec_id', 'spec_value'))->value('spec_value_id');
    }

    /**
     * 新增規格值
     */
    public function add($spec_id, $spec_value)
    {
        $app_id = self::$app_id;
        return $this->save(compact('spec_value', 'spec_id', 'app_id'));
    }

}
