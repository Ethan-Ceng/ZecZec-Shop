<?php

namespace app\common\model\plus\agent;

use app\common\enum\user\grade\ChangeTypeEnum;
use app\common\model\BaseModel;
use app\common\model\plus\agent\GradeLog as GradeLogModel;

/**
 * 分銷商使用者模型
 */
class User extends BaseModel
{
    protected $name = 'agent_user';
    protected $pk = 'user_id';

    /**
     * 關聯會員記錄表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User');
    }

    /**
     * 關聯推薦人表
     * @return \think\model\relation\BelongsTo
     */
    public function referee()
    {
        return $this->belongsTo('app\\common\\model\\user\\User', 'referee_id', 'user_id');
    }

    /**
     * 關聯等級表
     * @return \think\model\relation\BelongsTo
     */
    public function grade()
    {
        return $this->belongsTo('app\\common\\model\\plus\\agent\\Grade', 'grade_id', 'grade_id');
    }

    /**
     * 詳情
     */
    public static function detail($user_id, $with = ['user', 'referee', 'grade'])
    {
        return (new static())->where('user_id', '=', $user_id)->with($with)->find();
    }

    /**
     * 是否為分銷商
     * @param $user_id
     * @return bool
     */
    public static function isAgentUser($user_id)
    {
        $agent = self::detail($user_id);
        return !!$agent && !$agent['is_delete'];
    }

    /**
     * 新增分銷商使用者記錄
     * @param $user_id
     * @param $data
     * @return bool
     */
    public static function add($user_id, $data)
    {
        $model = static::detail($user_id);
        if (!$model) {
            $model = new static();
        }
        $model->save(array_merge([
            'user_id' => $user_id,
            'is_delete' => 0,
            'app_id' => $model::$app_id
        ], $data));
        event('AgentUserGrade', $model['referee_id']);
        return true;
    }

    /**
     * 發放分銷商佣金
     * @param $user_id
     * @param $money
     * @return bool
     */
    public static function grantMoney($user_id, $money)
    {
        // 分銷商詳情
        $model = static::detail($user_id);
        if (!$model || $model['is_delete']) {
            return false;
        }
        // 累積分銷商可提現佣金
        $model->where('user_id', '=', $user_id)->inc('money', $money)->update();
        // 記錄分銷商資金明細
        Capital::add([
            'user_id' => $user_id,
            'flow_type' => 10,
            'money' => $money,
            'describe' => '訂單佣金結算',
            'app_id' => $model['app_id'],
        ]);
        return true;
    }

    /**
     * 累計分銷商成員數量
     */
    public static function setMemberInc($agent_id, $level)
    {
        $fields = [1 => 'first_num', 2 => 'second_num', 3 => 'third_num'];
        $model = static::detail($agent_id);
        return $model->where('user_id', '=', $agent_id)->inc($fields[$level])->update();
    }

    /**
     * 批次設定會員等級
     */
    public function upgradeGrade($user, $upgradeGrade)
    {
        // 更新會員等級的資料
        $this->where('user_id', '=', $user['user_id'])
            ->update([
                'grade_id' => $upgradeGrade['grade_id']
            ]);
        (new GradeLogModel)->save([
            'old_grade_id' => $user['grade_id'],
            'new_grade_id' => $upgradeGrade['grade_id'],
            'change_type' => ChangeTypeEnum::AUTO_UPGRADE,
            'user_id' => $user['user_id'],
            'app_id' => $user['app_id']
        ]);
        return true;
    }

    /**
     * 詳情
     */
    public static function agentCount($referee_id)
    {
        return (new static())->where('referee_id', '=', $referee_id)->count();
    }

    /**
     * 分銷商詳情
     */
    public static function getAgentDetail($user_id)
    {
        return (new static())->where('user_id', '=', $user_id)->where('is_delete', '=', 0)->find();
    }
}