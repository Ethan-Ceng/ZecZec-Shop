<?php

namespace app\shop\controller\product;

use app\shop\controller\Controller;
use app\shop\model\product\Spec as SpecModel;
use app\shop\model\product\SpecValue as SpecValueModel;

/**
 * 商品規格控制器
 */
class Spec extends Controller
{

    /**
     * 新增規則組
     */
    public function addSpec($spec_name, $spec_value)
    {
        $specModel = new SpecModel();
        $specValueModel = new SpecValueModel();
        // 判斷規格組是否存在
        if (!$specId = $specModel->getSpecIdByName($spec_name)) {
            // 新增規格組and規則值
            if ($specModel->add($spec_name)
                && $specValueModel->add($specModel['spec_id'], $spec_value))
                return $this->renderSuccess('', [
                    'spec_id' => (int)$specModel['spec_id'],
                    'spec_value_id' => (int)$specValueModel['spec_value_id'],
                ]);
            return $this->renderError();
        }
        // 判斷規格值是否存在
        if ($specValueId = $specValueModel->getSpecValueIdByName($specId, $spec_value)) {
            return $this->renderSuccess('',  [
                'spec_id' => (int)$specId,
                'spec_value_id' => (int)$specValueId,
            ]);
        }
        // 新增規則值
        if ($specValueModel->add($specId, $spec_value)){
            return $this->renderSuccess('', [
                'spec_id' => (int)$specId,
                'spec_value_id' => (int)$specValueModel['spec_value_id'],
            ]);
        }

        return $this->renderError();
    }

    /**
     * 新增規格值
     */
    public function addSpecValue($spec_id, $spec_value)
    {
        $specValueModel = new SpecValueModel();
        // 判斷規格值是否存在
        if ($specValueId = $specValueModel->getSpecValueIdByName($spec_id, $spec_value)) {
            return $this->renderSuccess('',  [
                'spec_value_id' => (int)$specValueId,
            ]);
        }
        // 新增規則值
        if ($specValueModel->add($spec_id, $spec_value))
            return $this->renderSuccess('', [
                'spec_value_id' => (int)$specValueModel['spec_value_id'],
            ]);
        return $this->renderError();
    }

}
