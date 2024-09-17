<?php

namespace app\api\model\user;

use app\api\model\plus\agent\Referee as RefereeModel;
use app\api\model\plus\invitationgift\Partake;
use app\api\model\settings\Setting as SettingModel;
use think\facade\Cache;
use app\common\exception\BaseException;
use app\common\model\user\User as UserModel;
use app\common\model\user\Sms as SmsModel;
use app\common\model\user\Grade as GradeModel;
use think\facade\Request;
use think\facade\Db;
use think\facade\Env;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * 公眾號使用者模型類
 */
class UserOpen extends UserModel
{
    private $token;

    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'open_id',
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * 使用者登入
     */
    public function login($userInfo, $referee_id)
    {
        // 自動註冊使用者
        $user_id = $this->register($userInfo, $referee_id);
        // 生成token (session3rd)
        $this->token = $this->token($userInfo['openid']);
        // 記錄快取, 7天
        Cache::set($this->token, $user_id, 86400 * 7);
        return $user_id;
    }

    /**
     * 獲取token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * 生成使用者認證的token
     */
    private function token($openid)
    {
        return md5($openid . 'token_salt');
    }

    /**
     * 自動註冊使用者
     */
    private function register($userInfo, $referee_id = 0)
    {
        $data = [];
        //透過unionid查詢使用者是否存在
        $user = null;
        if (isset($userInfo['unionid']) && !empty($userInfo['unionid'])) {
            $data['union_id'] = $userInfo['unionid'];
            $user = self::detailByUnionid($userInfo['unionid']);
        }
        // 查詢使用者是否已存在
        if (!$user) {
            $user = self::detail(['appopen_id' => $userInfo['openid']]);
        }
        if ($user) {
            $model = $user;
            // 只修改union_id
            if (isset($data['union_id'])) {
                $data = [
                    'union_id' => $data['union_id'],
                ];
            } else {
                return $user['user_id'];
            }
        } else {
            $model = $this;
            $data['referee_id'] = $referee_id;
            $data['appopen_id'] = $userInfo['openid'];
            // 使用者資訊
            $data['nickName'] = preg_replace('/[\xf0-\xf7].{3}/', '', $userInfo['nickname']);
            $data['avatarUrl'] = $userInfo['headimgurl'];
            $data['gender'] = $userInfo['sex'];
            $data['province'] = $userInfo['province'];
            $data['country'] = $userInfo['country'];
            $data['city'] = $userInfo['city'];
            $data['reg_source'] = 'app';
            //預設等級
            $data['grade_id'] = GradeModel::getDefaultGradeId();
        }
        try {
            $this->startTrans();
            // 儲存/更新使用者記錄
            if (!$model->save(array_merge($data, [
                'app_id' => self::$app_id
            ]))
            ) {
                throw new BaseException(['msg' => '使用者註冊失敗']);
            }
            if (!$user) {
                //註冊之後關係繫結
                $this->saveRelation($model, $referee_id, 0);
            }
            $this->commit();
            return $model['user_id'];
        } catch (\Exception $e) {
            $this->rollback();
            throw new BaseException(['msg' => $e->getMessage()]);
        }
    }

    /**
     * 手機號密碼使用者登入
     */
    public function phoneLogin($data)
    {
        // ->where('reg_source', 'in', ['h5', 'app'])
        $user = $this->where('mobile', '=', $data['mobile'])
            ->where('password', '=', md5($data['password']))
            ->order('user_id desc')
            ->find();
        if (!$user) {
            $this->error = '手機號或密碼錯誤';
            return false;
        } else {
            if ($user['is_delete'] == 1) {
                $this->error = '手機號被禁止或刪除，請聯絡客服';
                return false;
            }
            $user_id = $user['user_id'];
            $mobile = $user['mobile'];
        }
        // 生成token (session3rd)
        $this->token = $this->token($mobile);
        // 記錄快取, 30天
        Cache::tag('cache')->set($this->token, $user_id, 86400 * 7);
        return $user_id;
    }

    /**
     * 手機號密碼使用者登入
     */
    public function smslogin($data)
    {
        $setting = SettingModel::getItem('store');
        if ($setting['sms_open']) {
            if (!$this->check($data)) {
                return false;
            }
        }
        // ->where('reg_source', 'in', ['h5', 'app'])
        $user = $this->where('mobile', '=', $data['mobile'])
            ->where('is_delete', '=', 0)
            ->order('user_id desc')
            ->find();
        if (!$user) {
            try {
                $this->startTrans();
                $data['referee_id'] = isset($data['referee_id']) && $data['referee_id'] ? $data['referee_id'] : 0;
                $data['invitation_id'] = isset($data['invitation_id']) && $data['invitation_id'] ? $data['invitation_id'] : 0;
                $data['reg_source'] = isset($data['reg_source']) && $data['reg_source'] ? $data['reg_source'] : 'h5';
                $this->save([
                    'mobile' => $data['mobile'],
                    'reg_source' => $data['reg_source'],
                    //預設等級
                    'grade_id' => GradeModel::getDefaultGradeId(),
                    'app_id' => self::$app_id,
                    'password' => md5(substr(md5(time()), 0, 8)),
                    'referee_id' => $data['referee_id']
                ]);
                //預設暱稱
                $this->save(['nickName' => $setting['user_name'] . $this['user_id']]);
                //註冊之後關係繫結
                $this->saveRelation($this, $data['referee_id'], $data['invitation_id']);
                $this->commit();
                $user_id = $this['user_id'];
                $mobile = $data['mobile'];
            } catch (\Exception $e) {
                $this->rollback();
                throw new BaseException(['msg' => $e->getMessage()]);
            }
        } else {
            $user_id = $user['user_id'];
            $mobile = $user['mobile'];
        }
        // 生成token (session3rd)
        $this->token = $this->token($mobile);
        // 記錄快取, 30天
        Cache::tag('cache')->set($this->token, $user_id, 86400 * 7);
        return $user_id;
    }

    /*
    *重置密碼
    */
    public function resetpassword($data)
    {
        if (!$this->check($data)) {
            return false;
        }
        // ->where('reg_source', 'in', ['h5', 'app'])
        $user = $this->where('mobile', '=', $data['mobile'])
            ->order('user_id desc')->find();
        if ($user) {
            if ($user['is_delete'] == 1) {
                $this->error = '手機號被禁止或刪除，請聯絡客服';
                return false;
            }
            return $user->save([
                'password' => md5($data['password'])
            ]);
        } else {
            $this->error = '手機號不存在';
            return false;
        }

    }

    /*
    *手機號註冊
    */
    public function phoneRegister($data)
    {
        $setting = SettingModel::getItem('store');
        if ($setting['sms_open']) {
            if (!$this->check($data)) {
                return false;
            }
        }
        // ->where('reg_source', 'in', ['h5', 'app'])
        $user = $this->where('mobile', '=', $data['mobile'])
            ->where('is_delete', '=', 0)
            ->find();
        if (!$user) {
            try {
                $this->startTrans();
                $data['referee_id'] = isset($data['referee_id']) && $data['referee_id'] ? $data['referee_id'] : 0;
                $data['invitation_id'] = isset($data['invitation_id']) && $data['invitation_id'] ? $data['invitation_id'] : 0;
                $this->save([
                    'mobile' => $data['mobile'],
                    'reg_source' => $data['reg_source'],
                    //預設等級
                    'grade_id' => GradeModel::getDefaultGradeId(),
                    'app_id' => self::$app_id,
                    'password' => md5($data['password']),
                    'referee_id' => $data['referee_id']
                ]);
                //預設暱稱
                $this->save(['nickName' => $setting['user_name'] . $this['user_id']]);
                //註冊之後關係繫結
                $this->saveRelation($this, $data['referee_id'], $data['invitation_id']);
                $this->commit();
                return true;
            } catch (\Exception $e) {
                $this->rollback();
                throw new BaseException(['msg' => $e->getMessage()]);
            }
        } else {
            $this->error = '手機號已存在';
            return false;
        }

    }

    /**
     *修改密碼
     */
    public function changePassword($data, $user)
    {
//        $setting = SettingModel::getItem('store');
//        if ($setting['sms_open']) {
//            $data['mobile'] = $user['mobile'];
//            if (!$this->check($data)) {
//                return false;
//            }
//        }
        return $user->save([
            'password' => md5($data['password'])
        ]);
    }

    /**
     *修改手機號
     */
    public function changeMobile($data, $user)
    {
        if ($user['mobile'] == $data['mobile']) {
            $this->error = '新手機號不能和原手機號一樣';
            return false;
        }
        if ($user['reg_source'] == 'h5' || $user['reg_source'] == 'app') {
            $reg_source = ['h5', 'app'];
        } else {
            $reg_source = [$user['reg_source']];
        }
        //判斷新手機號是否存在
        $isExist = $this->where('mobile', '=', $data['mobile'])
            ->where('reg_source', 'in', $reg_source)
            ->where('is_delete', '=', 0)
            ->find();
        if ($isExist) {
            $this->error = '新手機號已存在';
            return false;
        }
        $setting = SettingModel::getItem('store');
        if ($setting['sms_open']) {
            if (!$this->check($data)) {
                return false;
            }
        }
        return $user->save([
            'mobile' => $data['mobile']
        ]);
    }

    /**
     * 驗證
     */
    private function check($data)
    {
        //判斷驗證碼是否過期、是否正確
        $sms_model = new SmsModel();
        $sms_record_list = $sms_model
            ->where('mobile', '=', $data['mobile'])
            ->order(['create_time' => 'desc'])
            ->limit(1)->select();

        if (count($sms_record_list) == 0) {
            $this->error = '未查到簡訊傳送記錄';
            return false;
        }
        $sms_model = $sms_record_list[0];
        if ((time() - strtotime($sms_model['create_time'])) / 60 > 30) {
            $this->error = '簡訊驗證碼超時';
            return false;
        }
        if ($sms_model['code'] != $data['code']) {
            $this->error = '驗證碼不正確';
            return false;
        }
        return true;
    }

    /*
    * 郵件註冊
    */
    public function emailRegister($data)
    {
//        $setting = SettingModel::getItem('store');
//        if ($setting['sms_open']) {
//            if (!$this->check($data)) {
//                return false;
//            }
//        }
        $user = $this->where('email', '=', $data['email'])
            ->where('is_delete', '=', 0)
            ->find();
        if (!$user) {
            try {
                $this->startTrans();
                $data['referee_id'] = isset($data['referee_id']) && $data['referee_id'] ? $data['referee_id'] : 0;
                $data['invitation_id'] = isset($data['invitation_id']) && $data['invitation_id'] ? $data['invitation_id'] : 0;
                $userInser = $this->save([
                    'nickName' => $data['nickName'],
                    'email' => $data['email'],
                    'reg_source' => 'pc',
                    'status' => 0,
                    //預設等級
                    'grade_id' => GradeModel::getDefaultGradeId(),
                    'app_id' => self::$app_id,
                    'password' => md5($data['password']),
                    'referee_id' => $data['referee_id']
                ]);

//                if($userInser){
//                    $emailUser = $this->where('email', '=', $data['email'])
//                        ->find();
//                    $inData['user_id'] = $emailUser['user_id'];
//                    $inData['real_name'] = $emailUser['nickName'];
//                    $inData['mobile'] = $emailUser['mobile'];
//                    $inData['referee_id'] = $emailUser['referee_id'];
//                    $inData['apply_type'] = 20;
//                    $inData['apply_time'] = time();
//                    $inData['apply_status'] = 20;
//                    $inData['audit_time'] = time();
//                    $inData['reject_reason'] = '';
//                    $inData['app_id'] = self::$app_id;
//                    $inData['create_time'] = time();
//                    $inData['update_time'] = time();
//                    Db::name('agent_apply')->insert($inData);
//                }

                $randomNumber = md5($data['email'].$data['password']);
                $title = "請點選連結完成認證";
                $body = "點選或者複製此連結在瀏覽器開啟，進行賬戶啟用：<br><br>".
                        "<a href='".Request::host()."/login?app_id=10001&email=".$data['email'].'&code='.$randomNumber."'>".
                        Request::host()."/login?app_id=10001&email=".$data['email'].'&code='.$randomNumber.
                        "</a><br><br>或者點選此連結，認證賬號";
                $send_res = sendEmail($data["email"], $title, $body);
                
                $useremaildata['email'] = $data['email'];
                $useremaildata['code'] = $randomNumber;
                $useremaildata['status'] = 0;
                $useremaildata['app_id'] = 10001;
                $useremailcodeRes = Db::name('user_emailcode')->save($useremaildata);

                // 預設暱稱
                // $this->save(['nickName' => $setting['user_name'] . $this['user_id']]);
                // 註冊之後關係繫結
                $this->saveRelation($this, $data['referee_id'], $data['invitation_id']);
                $this->commit();
                return true;
            } catch (\Exception $e) {
                $this->rollback();
                throw new BaseException(['msg' => $e->getMessage()]);
            }
        } else {
            $this->error = '郵箱已存在';
            return false;
        }

    }

    /**
     * 郵箱登入
     */
    public function emailLogin($data)
    {
        $user = $this->where('email', '=', $data['email'])
            ->where('password', '=', md5($data['password']))
            ->order('user_id desc')
            ->find();
        if (!$user) {
            $this->error = '郵箱或密碼錯誤';
            return false;
        } else {
            if ($user['is_delete'] == 1) {
                $this->error = '賬號被禁止或刪除，請聯絡客服';
                return false;
            }
            if ($user['status'] == 0) {
                $this->error = '賬號未認證，請查收註冊郵件認證或聯絡客服';
                return false;
            }
            $user_id = $user['user_id'];
            $email = $user['email'];
        }
        // 生成token (session3rd)
        $this->token = $this->token($email);
        // 記錄快取, 30天
        Cache::tag('cache')->set($this->token, $user_id, 86400 * 7);
        return $user_id;
    }


    /*
     * 驗證郵箱註冊
     */
    public function emailCheck($data){
        $user = $this->where('email', '=', $data['email'])
            ->where('password', '=', md5($data['password']))
            ->order('user_id desc')
            ->find();
        if (!$user) {
            $this->error = '郵箱或密碼錯誤';
            return false;
        } else {
            if ($user['is_delete'] == 1) {
                $this->error = '賬號被禁止或刪除，請聯絡客服';
                return false;
            }
            $user_id = $user['user_id'];
            $email = $user['email'];
        }
        // 生成token (session3rd)
        $this->token = $this->token($email);
        // 記錄快取, 30天
        Cache::tag('cache')->set($this->token, $user_id, 86400 * 7);
        return $user_id;
    }

}
