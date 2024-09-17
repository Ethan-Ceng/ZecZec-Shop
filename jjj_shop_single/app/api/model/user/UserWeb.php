<?php

namespace app\api\model\user;

use app\api\model\plus\agent\Referee as RefereeModel;
use app\api\model\plus\invitationgift\Partake;
use app\common\exception\BaseException;
use app\common\model\user\Grade as GradeModel;
use app\common\model\user\Sms as SmsModel;
use think\facade\Cache;
use app\common\model\user\User as UserModel;

/**
 * 使用者模型類
 */
class UserWeb extends UserModel
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
    public function login($data)
    {
        if (!$this->check($data)) {
            return false;
        }
        $user = $this->where('mobile', '=', $data['mobile'])->find();
        if (!$user) {
            try {
                $this->startTrans();
                $refereeId = 0;
                if (isset($data['referee_id']) && $data['referee_id']) {
                    $refereeId = $data['referee_id'];
                }
                $this->save([
                    'mobile' => $data['mobile'],
                    'reg_source' => 'h5',
                    //預設等級
                    'grade_id' => GradeModel::getDefaultGradeId(),
                    'app_id' => self::$app_id,
                    'referee_id' => $refereeId
                ]);
                $user_id = $this['user_id'];
                $mobile = $data['mobile'];
                $invitation_id = 0;
                if (isset($data['invitation_id'])) {
                    $invitation_id = $data['invitation_id'];
                }
                //註冊之後關係繫結
                $this->saveRelation($this, $refereeId, $invitation_id);
                $this->commit();
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

    /**
     * 繫結手機
     */
    public function bindMobile($user, $data)
    {
        if (!$this->check($data)) {
            return false;
        }
        //判斷手機號是否存在
        $isExist = $this->where('mobile', '=', $data['mobile'])
            ->where('reg_source', '=', 'mp')
            ->where('is_delete', '=', 0)
            ->find();
        if ($isExist) {
            $this->error = '手機號已存在';
            return false;
        }
        return $user->save([
            'mobile' => $data['mobile']
        ]);
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
        $app_id = self::$app_id;
        // 生成一個不會重複的隨機字串
        $guid = \getGuidV4();
        // 當前時間戳 (精確到毫秒)
        $timeStamp = microtime(true);
        // 自定義一個鹽
        $salt = 'token_salt';
        return md5("{$app_id}_{$timeStamp}_{$openid}_{$guid}_{$salt}");
    }

}
