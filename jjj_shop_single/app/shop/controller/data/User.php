<?php

namespace app\shop\controller\data;

use app\shop\controller\Controller;
use app\shop\model\user\Grade;
use app\shop\model\user\User as UserModel;

/**
 * 使用者資料控制器
 */
class User extends Controller
{
    /**
     * 商品列表
     */
    public function lists()
    {
        $model = new UserModel();
        $params = $this->postData();
        $list = $model->getList($params['nickName'], $params['grade_id'], '',  $params);
        $GradeModel = new Grade();
        $grade = $GradeModel->getLists();
        return $this->renderSuccess('', compact('list', 'grade'));
    }

}
