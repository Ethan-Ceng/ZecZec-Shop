<?php

namespace app\api\controller\user;

use app\api\model\user\User as UserModel;
use app\api\controller\Controller;
use app\common\library\easywechat\AppWx;

/**
 * 使用者管理模型
 */
class User extends Controller
{
    /**
     * 使用者自動登入,預設微信小程式
     */
    public function login()
    {
        $model = new UserModel;
        $userInfo = $model->login($this->request->post());
        return $this->renderSuccess('', [
            'user_id' => $userInfo['user_id'],
            'mobile' => $userInfo['mobile']
        ]);
    }

    /**
     * 有手機號使用者登入
     */
    public function userLogin($code)
    {
        $model = new UserModel;
        $user_id = $model->userLogin($code);
        return $this->renderSuccess('', [
            'user_id' => $user_id,
            'token' => $model->getToken()
        ]);
    }

    /**
     * 當前使用者詳情
     */
    public function detail()
    {
        // 當前使用者資訊
        $userInfo = $this->getUser();
        return $this->renderSuccess('', compact('userInfo'));
    }

    public function getSession($code)
    {
        // 微信登入 獲取session_key
        $app = AppWx::getApp();
        $session_key = null;
        $session = AppWx::sessionKey($app, $code);
        if ($session != null) {
            $session_key = $session['session_key'];
        }
        return $this->renderSuccess('', compact('session_key'));
    }

    /**
     * 繫結手機號
     */
    public function bindMobile()
    {
        $model = (new UserModel());
        $user_id = $model->bindMobile($this->request->post());
        if ($user_id) {
            return $this->renderSuccess('', [
                'token' => $model->getToken(),
                'user_id' => $user_id
            ]);
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     * 修改使用者資訊
     */
    public function updateInfo()
    {
        // 當前使用者資訊
        $model = $this->getUser();
        if ($model->edit($this->request->post())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     * 積分轉換餘額
     */
    public function transPoints($points = 0)
    {
        // 當前使用者資訊
        $model = $this->getUser();
        if ($model->transPoints($points)) {
            return $this->renderSuccess('轉換成功');
        }
        return $this->renderError($model->getError() ?: '轉換失敗');
    }

    /**
     * 刪除賬號
     */
    public function deleteAccount()
    {
        $model = new UserModel();
        if ($model->setDelete($this->getUser())) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?: '刪除失敗');
    }

    /**
     * 退出登入
     */
    public function logOut($token)
    {
        $model = $this->getUser();
        if ($model->logOut($token)) {
            return $this->renderSuccess('退出成功');
        }
        return $this->renderError($model->getError() ?: '退出失敗');
    }

}