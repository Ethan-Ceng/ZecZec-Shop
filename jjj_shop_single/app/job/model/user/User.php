<?php

namespace app\job\model\user;

use app\common\model\user\User as UserModel;
use app\job\model\user\GradeLog as GradeLogModel;
use app\common\enum\user\grade\ChangeTypeEnum;

/**
 * 使用者模型
 */
class User extends UserModel
{
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

}
