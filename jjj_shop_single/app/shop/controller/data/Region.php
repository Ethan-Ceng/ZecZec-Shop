<?php

namespace app\shop\controller\data;

use app\common\model\settings\Region as RegionModel;
use app\shop\controller\Controller;

/**
 * 使用者資料控制器
 */
class Region extends Controller
{
    /**
     * 商品列表
     */
    public function lists()
    {
        $regionData = RegionModel::getCacheTree();
        return $this->renderSuccess('', compact('regionData'));
    }

}
