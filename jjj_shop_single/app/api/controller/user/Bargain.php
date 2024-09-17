<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\plus\bargain\Task;

/**
 * 個人砍價控制器
 */
class Bargain extends Controller
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
     *個人砍價列表
     */
    public function lists()
    {
        $model = new Task();
        $list = $model->getList($this->user['user_id'], $this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}