<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\user\UserMp as UserMpModel;
use app\common\library\easywechat\AppMp;

/**
 * 公眾號使用者管理
 */
class Usermp extends Controller
{

    /**
     * 使用者自動登入
     */
    public function login()
    {
        $data = $this->postData();
        $referee_id = isset($data['referee_id']) ? $data['referee_id'] : 0;
        $invitation_id = isset($data['invitation_id']) ? $data['invitation_id'] : 0;
        $redirect_uri = "index.php/api/user.usermp/login_callback?app_id={$this->app_id}&referee_id={$referee_id}&invitation_id={$invitation_id}";
        $app = AppMp::getApp($this->app_id, $redirect_uri);
        $oauth = $app->getOAuth();
        //生成完整的授權URL
        $redirectUrl = $oauth->scopes(['snsapi_userinfo'])->redirect();
        return redirect($redirectUrl);
    }

    /**
     * 使用者自動登入
     */
    public function login_callback()
    {
        $app = AppMp::getApp($this->app_id);
        $oauth = $app->getOauth();
        // 獲取 OAuth 授權使用者資訊
        $user = $oauth->userFromCode($this->request->param('code'));
        $userInfo = $user->toArray();
        $model = new UserMpModel;
        $referee_id = $this->request->param('referee_id');
        $invitation_id = $this->request->param('invitation_id');
        $user_id = $model->login($userInfo, $referee_id, $invitation_id);
        return redirect(base_url() . 'h5/pages/login/mplogin?app_id=' . $this->app_id . '&token=' . $model->getToken() . '&user_id=' . $user_id);
    }
}
