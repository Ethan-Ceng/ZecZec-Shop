<?php

namespace app\shop\model\plus\agent;

use app\common\model\plus\agent\Referee as RefereeModel;

/**
 * 分銷商使用者模型
 */
class Referee extends RefereeModel
{
    /**
     * 獲取下級團隊成員ID集
     */
    public function getTeamUserIds($agentId, $level = -1)
    {
        $level > -1 && $this->where('m.level', '=', $level);
        return $this->alias('m')
            ->join('user', 'user.user_id = m.user_id')
            ->where('m.agent_id', '=', $agentId)
            ->where('user.is_delete', '=', 0)
            ->column('m.user_id');
    }


    /**
     * 獲取指定使用者的推薦人列表
     */
    public static function getRefereeList($userId)
    {
        return (new static)->with(['agent1'])->where('user_id', '=', $userId)->select();
    }

    /**
     * 清空下級成員推薦關係
     */
    public function onClearTeam($agent_id, $level = -1)
    {
        $model = $this;
        if($level > -1){
            $model = $model->where('level', '=', $level);
        }
        return $model->where('agent_id', '=', $agent_id)->delete();
    }

    /**
     * 清空上級推薦關係
     */
    public function onClearReferee($userId, $level = -1)
    {
        $model = $this;
        if($level > -1) {
            $model = $model->where('level', '=', $level);
        }
        return $model->where('user_id', '=', $userId)->delete();
    }

    /**
     * 清空2-3級推薦人的關係記錄
     */
    public function onClearTop($teamIds)
    {
        return $this->where('user_id', 'in', $teamIds)
            ->where('level', 'in', [2, 3])
            ->delete();
    }
}