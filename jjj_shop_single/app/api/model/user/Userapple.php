<?php

namespace app\api\model\user;

use think\facade\Cache;
use app\common\exception\BaseException;
use app\common\model\user\User as UserModel;
use app\api\model\plus\agent\Referee as RefereeModel;
use app\common\model\user\Grade as GradeModel;
use app\api\model\plus\invitationgift\Partake;

/**
 * 使用者模型類
 */
class Userapple extends UserModel
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
    public function login($post)
    {
        // 自動註冊使用者
        $refereeId = isset($post['referee_id']) ? $post['referee_id'] : 0;
        //邀請好友
        $invitation_id = isset($post['invitation_id']) ? $post['invitation_id'] : 0;
        $user_id = $this->register($post, $refereeId, $invitation_id);
        // 生成token (session3rd)
        $this->token = $this->token($post['openId']);
        // 記錄快取, 7天
        Cache::tag('cache')->set($this->token, $user_id, 86400 * 7);
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
        $app_id = self::$app_id;
        // 生成一個不會重複的隨機字串
        $guid = \getGuidV4();
        // 當前時間戳 (精確到毫秒)
        $timeStamp = microtime(true);
        // 自定義一個鹽
        $salt = 'token_salt';
        return md5("{$app_id}_{$timeStamp}_{$openid}_{$guid}_{$salt}");
    }

    /**
     * 自動註冊使用者
     */
    private function register($data, $refereeId, $invitation_id)
    {
        //透過unionid查詢使用者是否存在
        $user = null;
        if (!$user) {
            // 透過open_id查詢使用者是否已存在
            $user = self::detail(['app_user' => $data['openId']]);
        }
        if ($user) {
            $model = $user;
        } else {
            $model = $this;
            $data['referee_id'] = $refereeId;
            $data['reg_source'] = 'apple';
            //預設等級
            $data['grade_id'] = GradeModel::getDefaultGradeId();
        }
        $this->startTrans();
        try {
            // 儲存/更新使用者記錄
            if (!$model->save(array_merge($data, [
                'app_user' => $data['openId'],
                'app_id' => self::$app_id
            ]))
            ) {
                throw new BaseException(['msg' => '使用者註冊失敗']);
            }
            if (!$user) {
                //註冊之後關係繫結
                $this->saveRelation($model, $refereeId, $invitation_id);
            }
            $this->commit();
            return $model['user_id'];
        } catch (\Exception $e) {
            $this->rollback();
            throw new BaseException(['msg' => $e->getMessage()]);
        }

    }
}
