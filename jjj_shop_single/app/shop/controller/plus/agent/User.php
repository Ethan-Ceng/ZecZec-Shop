<?php

namespace app\shop\controller\plus\agent;

use app\shop\controller\Controller;
use app\shop\model\plus\agent\Grade as GradeModel;
use app\shop\model\plus\agent\User as UserModel;
use app\shop\model\plus\agent\Setting as SettingModel;
use app\shop\model\plus\agent\Referee as RefereeModel;

/**
 * 分銷控制器
 */
class User extends Controller
{
    /**
     * 分銷商申請列表
     */
    public function index($nick_name = '')
    {
        $model = new UserModel;
        $list = $model->getList($nick_name);

        foreach ($list as $key => $val) {
            $list[$key]['cash_total'] = sprintf('%.2f', $val['moeny'] + $val['freeze_money'] + $val['total_money']);
        }
        $basicSetting = SettingModel::getItem('basic');
        $gradeList = GradeModel::getUsableList();
        return $this->renderSuccess('', compact('list', 'basicSetting', 'gradeList'));
    }

    /**
     * 編輯分銷商
     */
    public function edit($user_id)
    {
        $model = UserModel::detail($user_id);
        if ($this->request->isGet()) {
            return $this->renderSuccess('', compact('model'));
        }
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 編輯分銷商
     */
    public function add($user_id)
    {
        $model = UserModel::detail($user_id);
        if ($model) {
            return $this->renderError('該使用者已經是分銷員');
        } else {
            $model = new UserModel();
        }
        if ($model->addAgent($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 分銷商使用者列表
     */
    public function fans($user_id, $level = -1)
    {
        $model = new RefereeModel;
        $list = $model->getList($user_id, $level);
        $basicSetting = SettingModel::getItem('basic');
        return $this->renderSuccess('', compact('list', 'basicSetting'));
    }

    /**
     * 軟刪除分銷商使用者
     */
    public function delete($user_id)
    {
        $model = UserModel::detail($user_id);
        if (!$model->setDelete()) {
            return $this->renderError('刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

}