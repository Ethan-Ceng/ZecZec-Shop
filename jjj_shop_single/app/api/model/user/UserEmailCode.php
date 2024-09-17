<?php

namespace app\api\model\user;


use app\api\model\plus\agent\Apply as AgentApplyModel;
use app\common\model\user\EmailCode as EmailCodeModel;

 use app\common\model\user\User as UserModel;
use think\facade\Request;

/**
 * 公眾號使用者模型類
 */
class UserEmailCode extends EmailCodeModel
{
    private $token;

    /**
     * 隱藏欄位
     */
    protected $hidden = [

    ];

    public function sendPasswordCode($data)
    {
        $randomNumber = md5($data['email'] . $data['password']);
        $title = "重設密碼";
        $body = "點選或者複製此連結在瀏覽器開啟，進行重設密碼：<br><br>" .
            "<a href='" . Request::host() . "/reset_password?app_id=10001&email=" . $data['email'] . '&code=' . $randomNumber . "'>" .
            Request::host() . "/reset_password?app_id=10001&email=" . $data['email'] . '&code=' . $randomNumber .
            "</a><br><br>或者點選此連結，重設密碼";
        $send_res = sendEmail($data["email"], $title, $body);
        if ($send_res) {
            $useremaildata['email'] = $data['email'];
            $useremaildata['code'] = $randomNumber;
            $useremaildata['status'] = 0;
            $useremaildata['app_id'] = 10001;
            $this->save($useremaildata);
            return true;
        }
        return false;
    }


    /*
     * 驗證郵箱註冊
     */
    public function emailCheck($data)
    {
        $emailCode = (new static())->where(['email' => $data['email']])->order("id desc")->find();

        if (!$emailCode) {
            $this->error = '郵箱暫未註冊！';
            return false;
        }

        if ($emailCode['code'] != $data['code']) {
            $this->error = '驗證失敗，請核對驗證碼！';
            return false;
        }

        if ($emailCode['status'] == 1) {
            $this->error = '該賬號已經認證過，請直接登入！';
            return false;
        }
        // 校驗時間 7天 有效期
        $emailCode['status'] = 1;
        $emailCode->save();
        // update user status
        $userRes = (new UserModel)->where('email', $data['email'])->update(['status' => 1]);
        $user = null;
        // 检查是否更新成功
        if ($userRes) {
            // 获取更新后的用户数据
            $user = (new UserModel)->where('email', $data['email'])->find();
        } else {
            // 更新失败，返回错误信息
            return ['error' => '更新失败'];
        }
//        $userId = Db::table('jjjshop_user')
//            ->where('email', $data['email'])
//            ->update(['status' => 1]);
        // apply agent
        $model = new AgentApplyModel;
        $data['referee_id'] = 0;
        if ($model->submit($user, $data)) {
            return true;
        } else {
            $this->error = '該賬號已經認證過，請直接登入！';
        }
        return true;
//        Db::table('jjjshop_agent_user')
//            ->where('user_id', $user['user_id'])
//            ->update(['is_delete' => 0]);

    }

}
