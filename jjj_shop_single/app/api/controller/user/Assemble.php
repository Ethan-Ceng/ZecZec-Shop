<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\Order as OrderModel;

/**
 * 拼團控制器
 */
class Assemble extends Controller
{
    // 當前使用者
    private $user;

    /**
     * 構造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->user = $this->getUser();   // 使用者資訊

    }

    /**
     *拼團列表
     */
    public function lists()
    {
        $model = new OrderModel();
        $list = $model->getAssembleList($this->user['user_id'], $this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}