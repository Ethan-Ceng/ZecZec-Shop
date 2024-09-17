<?php

namespace app\shop\controller\auth;

use app\shop\controller\Controller;
use app\shop\model\shop\OptLog as OptLogModel;
/**
 * 管理員操作日誌
 */
class Optlog extends Controller
{
    /**
     * 操作日誌
     */
    public function index()
    {
        $model = new OptLogModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}