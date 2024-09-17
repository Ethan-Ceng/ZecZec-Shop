<?php

namespace app\shop\controller\auth;

use app\shop\controller\Controller;
use app\shop\model\shop\LoginLog as LoginLogModel;
/**
 * 管理員登入日誌
 */
class Loginlog extends Controller
{
    /**
     * 登入日誌
     */
    public function index()
    {
        $model = new LoginLogModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}