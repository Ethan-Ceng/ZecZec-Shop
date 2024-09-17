<?php

namespace app\job\event;

use app\job\model\user\Grade as GradeModel;
use app\job\model\user\User as UserModel;

/**
 * 使用者等級事件管理
 */
class UserGrade
{

    /**
     * 執行函式
     */
    public function handle($userId)
    {
        // 設定使用者的會員等級
        $this->setUserGrade($userId);
        return true;
    }

    /**
     * 設定使用者的會員等級
     */
    private function setUserGrade($userId)
    {
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
            $is_upgrade = $this->checkCanUpdate($user, $grade);
            if ($is_upgrade) {
                $upgradeGrade = $grade;
                continue;
            } else {
                break;
            }
        }
        if ($upgradeGrade && $upgradeGrade['grade_id'] != $user['grade_id']) {
            $this->dologs('setUserGrade', [
                'user_id' => $user['user_id'],
                'grade_id' => $upgradeGrade['grade_id'],
            ]);
            // 修改會員的等級
            (new UserModel())->upgradeGrade($user, $upgradeGrade);
            // 贈送積分
            if ($upgradeGrade['give_points'] > 0) {
                $user->setIncPoints($upgradeGrade['give_points'], '使用者升級贈送積分', false);
            }
        }
        return false;
    }

    /**
     * 查詢滿足會員等級升級條件的使用者列表
     */
    public function checkCanUpdate($user, $grade)
    {
        // 按消費升級
        if ($grade['open_money'] == 1 && $user['expend_money'] >= $grade['upgrade_money']) {
            return true;
        }
        // 按積分升級
        if ($grade['open_points'] == 1 && $user['total_points'] >= $grade['upgrade_points']) {
            return true;
        }
        // 按消費升級
        if ($grade['open_invite'] == 1 && $user['total_invite'] >= $grade['upgrade_invite']) {
            return true;
        }
        return false;
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
