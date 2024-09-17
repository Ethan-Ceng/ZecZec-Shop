<?php

namespace app\api\model\user;

use app\common\model\user\PointsLog as PointsLogModel;

/**
 * 積分變動明細模型
 */
class PointsLog extends PointsLogModel
{
    /**
     * 獲取日誌明細列表
     */
    public function getList($userId,$limit)
    {
        // 獲取列表資料
        return $this->where('user_id', '=', $userId)
            ->order(['create_time' => 'desc'])
            ->paginate($limit);
    }

}