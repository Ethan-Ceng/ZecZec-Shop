<?php

namespace app\admin\controller\admin;

use app\admin\controller\Controller;
use app\admin\model\admin\User as AdminUserModel;

/**
 * 超管後臺管理員控制器
 */
class User extends Controller
{
    /**
     * 更新當前管理員資訊
     */
    public function renew()
    {
        $model = AdminUserModel::detail($this->admin['admin_user_id']);
        if ($model->renew($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?:'更新失敗');
    }
}