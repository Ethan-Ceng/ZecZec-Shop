<?php

namespace app\shop\controller\user;

use app\common\library\helper;
use app\common\model\user\Tag as TagModel;
use app\common\model\user\UserTag as UserTagModel;
use app\shop\controller\Controller;
use app\shop\model\user\User as UserModel;
use app\shop\model\user\Grade;

/**
 * 使用者管理
 */
class User extends Controller
{
    /**
     * 商戶列表
     */
    public function index($nickName = '', $reg_date = '', $grade_id = null)
    {
        $list = UserModel::getList($nickName, $grade_id, $reg_date, $this->postData());
        $GradeModel = new Grade();
        $grade = $GradeModel->getLists();
        // 所有標籤
        $allTag = TagModel::getAll();
        return $this->renderSuccess('', compact('list', 'grade', 'allTag'));
    }


    /**
     * 刪除使用者
     */
    public function delete($user_id)
    {
        // 使用者詳情
        $model = UserModel::detail($user_id);
        if ($model && $model->setDelete()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?: '刪除失敗');
    }


    /**
     * 新增使用者
     */
    public function add()
    {
        $model = new UserModel;
        // 新增記錄
        if ($model->add($this->request->param())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 使用者充值
     */
    public function recharge($user_id, $source)
    {
        // 使用者詳情
        $model = UserModel::detail($user_id);

        if ($model->recharge($this->store['user']['user_name'], $source, $this->postData('params'))) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 修改使用者
     */
    public function edit($user_id)
    {
        // 使用者詳情
        $model = UserModel::detail($user_id);
        if ($this->request->isGet()) {
            return $this->renderSuccess('', compact('model'));
        }
        // 修改記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     * 等級改使用者
     */
    public function grade($user_id)
    {
        // 使用者詳情
        $model = UserModel::detail($user_id);
        // 修改記錄
        if ($model->updateGrade($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    public function tag($user_id)
    {
        if ($this->request->isGet()) {
            // 使用者詳情
            $user = UserModel::detail($user_id);
            // 標籤
            $userTag = UserTagModel::getListByUser($user_id);
            $userTag = helper::getArrayColumn($userTag, 'tag_id');
            // 所有標籤
            $allTag = TagModel::getAll();
            return $this->renderSuccess('', compact('user', 'userTag', 'allTag'));
        }
        $model = UserModel::detail($user_id);
        if ($model->editTag($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }
}
