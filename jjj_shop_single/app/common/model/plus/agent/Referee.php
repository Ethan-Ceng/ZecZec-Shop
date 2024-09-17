<?php

namespace app\common\model\plus\agent;

use app\common\model\BaseModel;

/**
 * 分銷商推薦關係模型
 */
class Referee extends BaseModel
{
    protected $name = 'agent_referee';
    protected $pk = 'id';

    /**
     * 關聯使用者表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User');
    }

    /**
     * 關聯分銷商使用者表
     */
    public function agent()
    {
        return $this->belongsTo('app\\common\\model\\plus\\agent\\User', 'user_id', 'user_id')->where('is_delete', '=', 0);
    }

    /**
     * 關聯分銷商使用者表
     */
    public function agent1()
    {
        return $this->belongsTo('app\\common\\model\\plus\\agent\\User', 'agent_id')->where('is_delete', '=', 0);
    }

    /**
     * 獲取上級使用者id
     */
    public static function getRefereeUserId($user_id, $level, $is_agent = false)
    {
        $agent_id = (new self)->where(compact('user_id', 'level'))
            ->value('agent_id');
        if (!$agent_id) return 0;
        return $is_agent ? (User::isAgentUser($agent_id) ? $agent_id : 0) : $agent_id;
    }

    /**
     * 獲取我的團隊列表
     */
    public function getList($user_id, $level = -1)
    {
        $model = $this;
        if ($level > -1) {
            $model = $model->where('referee.level', '=', $level);
        }
        return $model->with(['agent', 'user'])
            ->alias('referee')
            ->field('referee.*')
            ->join('user', 'user.user_id = referee.user_id', 'left')
            ->where('referee.agent_id', '=', $user_id)
            ->where('user.is_delete', '=', 0)
            ->order(['referee.create_time' => 'desc'])
            ->paginate(15);
    }

    /**
     * 建立推薦關係
     */
    public static function updateRelation($user_id, $referee_id)
    {
        // 自分享
        if ($user_id == $referee_id) {
            return false;
        }
        // 新增關係記錄
        $model = new self;
        $model->add($referee_id, $user_id, 1);
        // # 記錄二級推薦關係
        // 二級分銷商id
        $referee_2_id = self::getRefereeUserId($referee_id, 1, true);
        // 新增關係記錄
        $referee_2_id > 0 && $model->add($referee_2_id, $user_id, 2);
        // # 記錄三級推薦關係
        // 三級分銷商id
        $referee_3_id = self::getRefereeUserId($referee_id, 2, true);
        // 新增關係記錄
        $referee_3_id > 0 && $model->add($referee_3_id, $user_id, 3);
        return true;
    }

    /**
     * 新增關係記錄
     */
    private function add($agent_id, $user_id, $level = 1)
    {
        // 新增推薦關係
        $app_id = self::$app_id;
        $create_time = time();
        $this->insert(compact('agent_id', 'user_id', 'level', 'app_id', 'create_time'));
        // 記錄分銷商成員數量
        User::setMemberInc($agent_id, $level);
        return true;
    }

    /**
     * 是否已存在推薦關係
     */
    private static function isExistReferee($user_id)
    {
        return !!(new static())->where(['user_id' => $user_id])->find();
    }

    /**
     * 建立推薦關係
     */
    public static function createRelation($user_id, $referee_id)
    {
        // 分銷商基本設定
        $setting = Setting::getItem('basic');
        // 是否開啟分銷功能
        if (!$setting['is_open']) {
            return false;
        }
        // 自分享
        if ($user_id == $referee_id) {
            return false;
        }
        // # 記錄一級推薦關係
        // 判斷當前使用者是否已存在推薦關係
        if (self::isExistReferee($user_id)) {
            return false;
        }
        // 判斷推薦人是否為分銷商
        if (!User::isAgentUser($referee_id)) {
            return false;
        }
        // 新增關係記錄
        $model = new self;
        $model->add($referee_id, $user_id, 1);
        // # 記錄二級推薦關係
        if ($setting['level'] >= 2) {
            // 二級分銷商id
            $referee_2_id = self::getRefereeUserId($referee_id, 1, true);
            // 新增關係記錄
            $referee_2_id > 0 && $model->add($referee_2_id, $user_id, 2);
        }
        // # 記錄三級推薦關係
        if ($setting['level'] == 3) {
            // 三級分銷商id
            $referee_3_id = self::getRefereeUserId($referee_id, 2, true);
            // 新增關係記錄
            $referee_3_id > 0 && $model->add($referee_3_id, $user_id, 3);
        }
        return true;
    }
}