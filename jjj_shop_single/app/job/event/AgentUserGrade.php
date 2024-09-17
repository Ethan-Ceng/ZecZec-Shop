<?php

namespace app\job\event;

use app\common\model\plus\agent\Grade as GradeModel;
use app\common\model\plus\agent\User as UserModel;

/**
 * 使用者等級事件管理
 */
class AgentUserGrade
{
    /**
     * 執行函式
     */
    public function handle($userId)
    {
        if ($userId == 0) {
            return false;
        }
        // 設定使用者的會員等級
        $this->setGrade($userId);
        return true;
    }

    /**
     * 設定等級
     */
    private function setGrade($userId)
    {
        log_write('分銷商升級$user_id=' . $userId);
        // 使用者模型
        $user = UserModel::detail($userId);
        if (!$user) {
            return false;
        }
        // 獲取所有等級
        $list = GradeModel::getUsableList($user['app_id']);
        if ($list->isEmpty()) {
            return false;
        }
        // 遍歷等級，根據升級條件 查詢滿足消費金額的使用者列表，並且他的等級小於該等級
        $upgradeGrade = null;
        foreach ($list as $grade) {
            if ($grade['is_default'] == 1) {
                continue;
            }
            // 不自動升級
            if ($grade['auto_upgrade'] == 0) {
                continue;
            }
            $is_upgrade = $this->checkCanUpdate($user, $grade);
            if ($is_upgrade) {
                $upgradeGrade = $grade;
                continue;
            } else {
                break;
            }
        }
        if ($upgradeGrade) {
            $this->dologs('setAgentUserGrade', [
                'user_id' => $user['user_id'],
                'grade_id' => $upgradeGrade['grade_id'],
            ]);
            // 修改會員的等級
            (new UserModel())->upgradeGrade($user, $upgradeGrade);
        }
    }

    /**
     * 查詢滿足會員等級升級條件的使用者列表
     */
    public function checkCanUpdate($user, $grade)
    {
        $agent_money = false;
        // 按推廣金額升級
        if ($grade['open_agent_money'] == 1 && ($user['money'] + $user['freeze_money'] + $user['total_money']) >= $grade['agent_money']) {
            $agent_money = true;
        }
        $agent_user = false;
        // 按直推人數升級
        if ($grade['open_agent_user'] == 1 && UserModel::agentCount($user['user_id']) >= $grade['agent_user']) {
            $agent_user = true;
        }
        if ($grade['open_agent_money'] == 0 && $grade['open_agent_user'] == 0) {
            $agent_money = true;
            $agent_user = true;
        }
        if ($grade['condition_type'] == 'and') {
            return $agent_money && $agent_user;
        } else {
            return $agent_money || $agent_user;
        }
    }

    /**
     * 記錄日誌
     */
    private function dologs($method, $params = [])
    {
        $value = 'UserGrade --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value, 'task');
    }
}
