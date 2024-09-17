<?php

namespace app\admin\model\user;

use app\common\model\user\Grade as GradeModel;

/**
 * 使用者會員等級模型
 */
class Grade extends GradeModel
{
    /**
     * 新增記錄
     */
    public function insertDefault($app_id)
    {
        $data = [
            'name' => '普通會員',
            'is_default' => 1,
            'remark' => '新使用者即為該等級',
            'app_id' => $app_id
        ];
        return self::save($data);
    }

}