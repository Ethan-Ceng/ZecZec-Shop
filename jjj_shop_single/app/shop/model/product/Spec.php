<?php

namespace app\shop\model\product;

use app\common\model\product\Spec as SpecModel;

/**
 * 規格/屬性(組)模型
 */
class Spec extends SpecModel
{
    /**
     * 根據規格組名稱查詢規格id
     */
    public function getSpecIdByName($spec_name)
    {
        return self::where(compact('spec_name'))->value('spec_id');
    }

    /**
     * 新增規格組
     */
    public function add($spec_name)
    {
        $app_id = self::$app_id;
        return $this->save(compact('spec_name', 'app_id'));
    }

}
