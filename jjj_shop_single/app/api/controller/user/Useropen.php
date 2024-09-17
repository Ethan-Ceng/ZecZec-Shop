<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\Order as OrderModel;
use app\api\model\settings\Setting as SettingModel;
use app\api\model\user\UserOpen as UserOpenModel;
use app\api\model\user\User as UserModel;
use app\api\model\user\UserMp as UserMpModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\library\easywechat\AppMp;
use app\common\model\app\AppOpen as AppOpenModel;
use app\common\model\user\Sms as SmsModel;

use app\api\model\user\UserEmailCode as UserEmailCodeModel;
use think\Request;
use think\facade\Db;

/**
 * app使用者管理
 */
class Useropen extends Controller
{
    /**
     * 使用者自動登入
     */
    public function login($code)
    {
        $referee_id = isset($this->request->param()['referee_id']) ? $this->request->param()['referee_id'] : 0;
        $wxConfig = AppOpenModel::getAppOpenCache($this->app_id);
        $appId = $wxConfig['openapp_id'];
        $appSecret = $wxConfig['openapp_secret'];
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appId . '&secret=' . $appSecret . '&code=' . $code . '&grant_type=authorization_code';
        $stream_opts = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ]
        ];
        //獲取token，為了獲取access_token 如果沒有就彈出錯誤
        $token = json_decode(file_get_contents($token_url, false, stream_context_create($stream_opts)));
        if (isset($token->errcode)) {
            return $this->renderError($token->errmsg);
        }
        $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $appId . '&grant_type=refresh_token&refresh_token=' . $token->refresh_token;
        //獲取access_token ，為了獲取微信的個人資訊，如果沒有就彈出錯誤
        $access_token = json_decode(file_get_contents($access_token_url, false, stream_context_create($stream_opts)));
        if (isset($access_token->errcode)) {
            return $this->renderError($access_token->errmsg);
        }
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token->access_token . '&openid=' . $access_token->openid . '&lang=zh_CN';
        //獲取使用者資訊
        $user_info = json_decode(file_get_contents($user_info_url, false, stream_context_create($stream_opts)));
        if (isset($user_info->errcode)) {
            log_write($user_info->errcode);
            log_write($user_info->errmsg);
            return $this->renderError($user_info->errmsg);
        }
        $model = new UserOpenModel;
        $user_id = $model->login((array)$user_info, $referee_id);
        return $this->renderSuccess('', [
            'user_id' => $user_id,
            'token' => $model->getToken()
        ]);
    }

    public function payWx($order_id)
    {
        $user = $this->getUser();
        // 訂單詳情
        $model = OrderModel::getUserOrderDetail($order_id, $user['user_id']);
        // 構建支付請求
        $payment = OrderModel::onOrderPayment($user, [$model], OrderPayTypeEnum::WECHAT, 'payApp');

        return $this->renderSuccess('', [
            'order' => $model,  // 訂單詳情
            'payment' => $payment
        ]);
    }

    public function invite($referee_id = '')
    {
        $redirect_uri = "index.php/api/user.useropen/invite_callback?app_id={$this->app_id}&referee_id={$referee_id}";
        $app = AppMp::getApp($this->app_id, $redirect_uri);
        $oauth = $app->getOAuth();
        //生成完整的授權URL
        $redirectUrl = $oauth->scopes(['snsapi_userinfo'])->redirect();
        return redirect($redirectUrl);
    }

    /**
     * 使用者自動登入
     */
    public function invite_callback()
    {
        $app = AppMp::getApp($this->app_id);
        $oauth = $app->getOauth();
        // 獲取 OAuth 授權使用者資訊
        $user = $oauth->userFromCode($this->request->param('code'));
        $userInfo = $user->toArray();
        // 繫結關係,儲存資料庫
        $model = new UserMpModel;
        $referee_id = $this->request->param('referee_id');
        $invitation_id = $this->request->param('invitation_id');
        $model->login($userInfo, $referee_id, $invitation_id);
        //跳轉到app下載頁
        $appshare = SettingModel::getItem('appshare');
        $down_url = $appshare['down_url'];
        return redirect($down_url);
    }

    /**
     * 手機號碼登入
     */
    public function phonelogin()
    {
        $data = $this->postData();
        $model = new UserOpenModel;
        $user_id = $model->phoneLogin($data);
        if ($user_id) {
            return $this->renderSuccess('', [
                'user_id' => $user_id,
                'token' => $model->getToken()
            ]);
        }
        return $this->renderError($model->getError() ?: '登入失敗');
    }

    /**
     * 簡訊登入
     */
    public function smslogin()
    {
        $data = $this->postData();
        $model = new UserOpenModel;
        $user_id = $model->smslogin($data);
        if ($user_id) {
            return $this->renderSuccess('', [
                'user_id' => $user_id,
                'token' => $model->getToken()
            ]);
        }
        return $this->renderError($model->getError() ?: '登入失敗');
    }

    /**
     * 忘記密碼
     */
    public function resetpassword()
    {
        $data = $this->postData();
        $model = new UserOpenModel;
        if ($model->resetpassword($data)) {
            return $this->renderSuccess('設定成功');
        }
        return $this->renderError($model->getError() ?: '設定失敗');
    }

    /**
     * 修改密碼
     */
    public function changePassword()
    {
        $model = new UserOpenModel;
        if ($model->changePassword($this->postData(), $this->getUser())) {
            return $this->renderSuccess('設定成功');
        }
        return $this->renderError($model->getError() ?: '設定失敗');
    }

    /**
     * 修改手機號
     */
    public function changeMobile()
    {
        $model = new UserOpenModel;
        if ($model->changeMobile($this->postData(), $this->getUser())) {
            return $this->renderSuccess('設定成功');
        }
        return $this->renderError($model->getError() ?: '設定失敗');
    }

    /**
     * 手機號碼註冊
     */
    public function register()
    {
        $data = $this->postData();
        $model = new UserOpenModel;
        if ($model->phoneRegister($data)) {
            return $this->renderSuccess('註冊成功');
        }
        return $this->renderError($model->getError() ?: '註冊失敗');

    }

    /**
     * 傳送簡訊
     */
    public function sendCode($mobile, $type)
    {
        $model = new SmsModel();
        if ($model->send($mobile, $type)) {
            return $this->renderSuccess();
        }
        return $this->renderError($model->getError() ?: '傳送失敗');
    }

    /**
     *  郵箱登入
     */
    public function loginByEmail()
    {
        $data = $this->postData();
        $model = new UserOpenModel;
        $user_id = $model->emailLogin($data);
        if ($user_id) {
            return $this->renderSuccess('', [
                'user_id' => $user_id,
                'token' => $model->getToken()
            ]);
        }
        return $this->renderError($model->getError() ?: '登入失敗');
    }

    /**
     * 郵箱註冊
     */
    public function registerByEmail()
    {
        $data = $this->postData();
        $model = new UserOpenModel;
        if ($model->emailRegister($data)) {
            return $this->renderSuccess('註冊成功,請登入你的郵箱啟用賬戶！');
        }
        return $this->renderError($model->getError() ?: '註冊失敗');

    }



    /**
     *  認證註冊 郵箱確認
     */
    public function checkByEmail()
    {

        $data = $this->postData();

        $model = new UserEmailCodeModel;
        $user_id = $model->emailCheck($data);
        if ($user_id) {
            return $this->renderError($model->getError() ?: '認證成功');
        }
        return $this->renderError($model->getError() ?: '認證失敗');
    }

    /**
     *  忘記密碼
     */
    public function forgetByEmail()
    {
        $data = $this->postData();
        $user = (new UserModel)->where('email', '=', $data['email'])->where('is_delete', '!=', 0)->order('user_id desc')->find();
        if (!$user) {
            return $this->renderError('用戶信息有誤');
        }
        $model = new UserEmailCodeModel;
        $result = $model->sendPasswordCode($user);
        if ($result) {
            return $this->renderSuccess($model->getError() ?: '認證成功');
        }
        return $this->renderError($model->getError() ?: '認證失敗');
    }

    /**
     *  重設密碼
     */
    public function resetPasswordByEmail()
    {
        $data = $this->postData();
        $codeEmail = (new UserEmailCodeModel)->where('email', '=', $data['email'])->where('code', '=', $data['code'])->order('id desc')->find();
        if (!$codeEmail) {
            return $this->renderError('驗證失敗');
        }
        if ($codeEmail['stauts'] === 1) {
            return $this->renderError('驗證碼失效');
        }
        $model = new UserModel;
        $user = $model->where('email', '=', $data['email'])->order('user_id desc')->find();
        if (!$user) {
            return $this->renderError('用戶信息有誤');
        }
        $user['password'] = md5($data['password']);
        $result = $user->save();
        if ($result) {
            return $this->renderSuccess($model->getError() ?: '認證成功');
        }
        return $this->renderError($model->getError() ?: '認證失敗');
    }
}
