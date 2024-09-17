<?php

namespace app\api\model\user;

use app\api\model\plus\agent\Referee as RefereeModel;
use app\api\model\plus\invitationgift\Partake;
use app\common\model\user\Grade as GradeModel;
use think\facade\Cache;
use app\common\exception\BaseException;
use app\common\model\user\User as UserModel;

/**
 * 公眾號使用者模型類
 */
class UserMp extends UserModel
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
    public function login($userInfo, $referee_id, $invitation_id)
    {
        $userInfo = $userInfo['raw'];
        // 自動註冊使用者
        $user_id = $this->register($userInfo, $referee_id, $invitation_id);
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
    private function register($userInfo, $referee_id, $invitation_id)
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
            $user = self::detail(['mpopen_id' => $userInfo['openid']]);
        }
        if ($user) {
            $model = $user;
            if (isset($data['union_id'])) {
                // 只修改union_id
                $data = [
                    'union_id' => $data['union_id'],
                ];
            } else {
                return $user['user_id'];
            }
        } else {
            if ($referee_id > 0) {
                if (UserModel::detail($referee_id) == null) {
                    $referee_id = 0;
                }
            }
            $model = $this;
            $data['referee_id'] = $referee_id;
            $data['reg_source'] = 'mp';
            //預設等級
            $data['grade_id'] = GradeModel::getDefaultGradeId();
        }

        $data['mpopen_id'] = $userInfo['openid'];

        // 使用者暱稱
        if (!$user) {
            $data['nickName'] = preg_replace('/[\xf0-\xf7].{3}/', '', $userInfo['nickname']);
            $data['avatarUrl'] = $userInfo['headimgurl'];
        }
        $data['gender'] = $userInfo['sex'];
        $data['province'] = $userInfo['province'];
        $data['country'] = $userInfo['country'];
        $data['city'] = $userInfo['city'];

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
                $this->saveRelation($model, $referee_id, $invitation_id);
            }
            $this->commit();
            return $model['user_id'];
        } catch (\Exception $e) {
            $this->rollback();
            throw new BaseException(['msg' => $e->getMessage()]);
        }
    }

}
