<?php

namespace app\shop\controller\plus;

use app\shop\model\plus\plus\Category as CategoryModel;
use app\shop\controller\Controller;

/**
 * 外掛控制器
 */
class Plus extends Controller
{
    /**
     * 外掛列表
     */
    public function index()
    {
        $list = CategoryModel::getAll();
        return $this->renderSuccess('', compact('list'));
    }


}