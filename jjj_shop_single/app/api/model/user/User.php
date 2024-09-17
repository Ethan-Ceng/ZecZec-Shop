<?php

namespace app\api\model\user;

use app\common\library\helper;
use app\common\library\wechat\WxBizDataCrypt;
use think\facade\Cache;
use app\common\exception\BaseException;
use app\common\model\user\User as UserModel;
use app\api\model\plus\agent\Referee as RefereeModel;
use app\common\library\easywechat\AppWx;
use app\common\model\user\Grade as GradeModel;
use app\api\model\plus\invitationgift\Partake;
use app\common\model\page\CenterMenu as CenterMenuModel;
use app\common\model\settings\Setting as SettingModel;
use app\common\model\user\BalanceLog as BalanceLogModel;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;

/**
 * 使用者模型類
 */
class User extends UserModel
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
     * 獲取使用者資訊
     */
    public static function getUser($token)
    {
        $userId = Cache::get($token);
        $user = (new static())->where(['user_id' => $userId])->with(['address', 'addressDefault', 'grade'])->order('user_id desc')->find();
        if ($user) {
            $user->hidden(['password']);
        }
        return $user;
    }

    /**
     * 使用者登入
     */
    public function login($post)
    {
        // 微信登入 獲取session_key
        $app = AppWx::getApp();
        $utils = $app->getUtils();
        $session = $utils->codeToSession($post['code']);
        // 自動註冊使用者
        $refereeId = isset($post['referee_id']) && $post['referee_id'] ? $post['referee_id'] : 0;
        //邀請好友
        $invitation_id = isset($post['invitation_id']) ? $post['invitation_id'] : 0;
        $userInfo = $this->register($session, $refereeId, $invitation_id);
        return $userInfo;
    }

    /**
     * 使用者登入
     */
    public function userLogin($code)
    {
        // 微信登入 獲取session_key
        $app = AppWx::getApp();
        $utils = $app->getUtils();
        $session = $utils->codeToSession($code);
        $userInfo = "";
        if (isset($session['unionid']) && !empty($session['unionid'])) {
            $userInfo = self::detailByUnionid($session['unionid']);
        }
        if (!$userInfo) {
            $userInfo = $this->where('open_id', '=', $session['openid'])
                ->where('is_delete', '=', 0)
                ->find();
        }
        if (!$userInfo) {
            $this->error = '使用者不存在，請重新登入';
            return false;
        }
        $this->token = $this->token($session['openid']);
        // 記錄快取, 7天
        Cache::tag('cache')->set($this->token, $userInfo['user_id'], 86400 * 7);
        return $userInfo['user_id'];
    }

    /**
     * 使用者登入
     */
    public function bindMobile($post)
    {
        try {
            $user_id = $post['user_id'];
            $user = self::detail($user_id);
            if (!$user) {
                $this->error = '授權失敗，請重新授權';
                return false;
            }
            if ($user['mobile']) {
                // 生成token (session3rd)
                $this->token = $this->token($user_id);
                // 記錄快取, 7天
                Cache::tag('cache')->set($this->token, $user_id, 86400 * 7);
                return $user_id;
            }
            // 微信登入 獲取session_key
            $app = AppWx::getApp();
            $session = AppWx::sessionKey($app, $post['code']);
            if (!$session) {
                $this->error = '授權失敗，請重新授權';
                return false;
            }
            $iv = $post['iv'];
            $encrypted_data = $post['encrypted_data'];
            $utils = $app->getUtils();
            $result = $utils->decryptSession($session['session_key'], $iv, $encrypted_data);
            if (isset($result['phoneNumber']) && $result['phoneNumber']) {
                $this->startTrans();
                $this->where('user_id', '=', $user_id)
                    ->update([
                        'mobile' => $result['phoneNumber'],
                    ]);
                // 生成token (session3rd)
                $this->token = $this->token($user_id);
                // 記錄快取, 7天
                Cache::tag('cache')->set($this->token, $user_id, 86400 * 7);
                $this->commit();
                return $user_id;
            } else {
                $this->error = '登入失敗';
                return false;
            }
        } catch (\Exception $e) {
            $this->rollback();
            $this->error = '獲取手機號失敗，請重試';
            return false;
        }
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
    private function register($decryptedData, $refereeId, $invitation_id)
    {
        //透過unionid查詢使用者是否存在
        $user = null;
        if (isset($decryptedData['unionid']) && !empty($decryptedData['unionid'])) {
            $data['union_id'] = $decryptedData['unionid'];
            $user = self::detailByUnionid($decryptedData['unionid']);
        }
        if (!$user) {
            // 透過open_id查詢使用者是否已存在
            $user = self::detail(['open_id' => $decryptedData['openid']]);
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
            $data['referee_id'] = $refereeId;
            $data['reg_source'] = 'wx';
            //預設等級
            $data['grade_id'] = GradeModel::getDefaultGradeId();
        }
        $this->startTrans();
        try {
            // 儲存/更新使用者記錄
            if (!$model->save(array_merge($data, [
                'open_id' => $decryptedData['openid'],
                'app_id' => self::$app_id
            ]))
            ) {
                throw new BaseException(['msg' => '使用者註冊失敗']);
            }
            if (!$user) {
                $setting = SettingModel::getItem('store');
                //預設暱稱
                $model->save(['nickName' => $setting['user_name'] . $model['user_id']]);
                //註冊之後關係繫結
                $this->saveRelation($model, $refereeId, $invitation_id);
            }
            $this->commit();
            return $model;
        } catch (\Exception $e) {
            $this->rollback();
            throw new BaseException(['msg' => $e->getMessage()]);
        }
    }

    /**
     *統計被邀請人數
     */
    public function getCountInv($user_id)
    {
        return $this->where('referee_id', '=', $user_id)->count('user_id');
    }

    /**
     * 簽到更新使用者積分
     */
    public function setPoints($user_id, $days, $sign_conf, $sign_date)
    {
        $rank = $sign_conf['ever_sign'];
        if ($sign_conf['is_increase'] == 'true') {
            if ($days >= $sign_conf['no_increase']) {
                $days = $sign_conf['no_increase'] - 1;
            }
            $rank = ($days - 1) * $sign_conf['increase_reward'] + $rank;
        }
        //是否獎勵
        if (isset($sign_conf['reward_data'])) {
            $arr = array_column($sign_conf['reward_data'], 'day');
            if (in_array($days, $arr)) {
                $key = array_search($days, $arr);
                if ($sign_conf['reward_data'][$key]['is_point'] == 'true') {
                    $rank = $sign_conf['reward_data'][$key]['point'] + $rank;
                }
            }
        }
        // 新增積分變動明細
        $this->setIncPoints($rank, '使用者簽到：簽到日期' . $sign_date);
        return $rank;
    }

    /**
     * 個人中心選單列表
     */
    public static function getMenus($source, $user_id)
    {
        // 系統選單
        $sys_menus = CenterMenuModel::getSysMenu();
        // 查詢使用者選單
        $model = new CenterMenuModel();
        $user_menus = $model->getAll();
        $user_menu_tags = [];
        foreach ($user_menus as $menu) {
            $menu['sys_tag'] != '' && array_push($user_menu_tags, $menu['sys_tag']);
        }
        $save_data = [];
        foreach ($sys_menus as $menu) {
            if ($menu['sys_tag'] != '' && !in_array($menu['sys_tag'], $user_menu_tags)) {
                $save_data[] = array_merge($sys_menus[$menu['sys_tag']], [
                    'sort' => 100,
                    'app_id' => self::$app_id
                ]);
            }
        }
        if (count($save_data) > 0) {
            $model->saveAll($save_data);
            Cache::delete('center_menu_' . self::$app_id);
            $user_menus = $model->getAll();
        }
        $sign_conf = SettingModel::getItem('sign');
        $show_menus = [];
        foreach ($user_menus as $key => $menus) {
            if ($menus['sys_tag'] == "sign" && !$sign_conf['is_open']) {
                continue;
            }
            if (strpos($menus['icon'], 'http') !== 0) {
                $menus['icon'] = self::$base_url . $menus['icon'];
            }
            if ($menus['is_show'] == 1) {
                array_push($show_menus, $menus);
            }
        }
        return $show_menus;
    }

    /**
     * 修改會員資訊
     */
    public function edit($data)
    {
        $this->startTrans();
        try {
            //完成成長任務
            if ($this['nickName'] != $data['nickName']) {
                $data['task_type'] = "base";
                $data['user_id'] = $this['user_id'];
                event('UserTask', $data);
            } elseif ($this['avatarUrl'] != $data['avatarUrl']) {
                $data['task_type'] = "image";
                $data['user_id'] = $this['user_id'];
                event('UserTask', $data);
            }
            unset($data['points']);
            $this->allowField(['avatarUrl', 'nickName', 'gender', 'real_name', 'birthday', 'intro'])->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 積分轉換餘額
     */
    public function transPoints($points)
    {
        $setting = SettingModel::getItem('points');
        $ratio = $setting['discount']['discount_ratio'];
        if (!$setting['is_trans_balance']) {
            $this->error = "暫未開啟積分轉換餘額";
            return false;
        }
        if ($points <= 0) {
            $this->error = "轉換積分不能小於0";
            return false;
        }
        if ($this['points'] < $points) {
            $this->error = "不能大於當前積分";
            return false;
        }
        $this->startTrans();
        try {
            $balance = round($ratio * $points, 2);
            //新增積分記錄
            $describe = "積分轉換餘額";
            $this->setIncPoints(-$points, $describe);
            //新增餘額記錄
            $balance > 0 && BalanceLogModel::add(BalanceLogSceneEnum::POINTS, [
                'user_id' => $this['user_id'],
                'money' => $balance,
                'app_id' => self::$app_id
            ], '');
            $this->save(['balance' => $this['balance'] + $balance]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 資金凍結
     */
    public function freezeMoney($money)
    {
        return $this->save([
            'balance' => $this['balance'] - $money,
            'freeze_money' => $this['freeze_money'] + $money,
        ]);
    }

    public function setDelete($user)
    {
        return $user->save([
            'is_delete' => 1
        ]);
    }

    /**
     * 退出登入
     */
    public function logOut($token)
    {
        Cache::delete($token);
        return true;
    }
}
