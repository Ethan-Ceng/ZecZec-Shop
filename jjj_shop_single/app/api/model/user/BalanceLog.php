<?php

namespace app\api\model\user;

use app\common\model\user\BalanceLog as BalanceLogModel;

/**
 * 使用者餘額變動明細模型
 */
class BalanceLog extends BalanceLogModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'app_id',
    ];

    /**
     * 獲取賬單明細列表
     */
    public function getTop10($userId)
    {
        // 獲取列表資料
        return $this->where('user_id', '=', $userId)
            ->order(['create_time' => 'desc'])
            ->limit(10)
            ->select();
    }

    /**
     * 獲取賬單明細列表
     */
    public function getList($userId, $type)
    {
        $model = $this;
        if($type == 'rechange'){
            $model = $model->where('scene', '=', 10);
        }
        // 獲取列表資料
        return $model->where('user_id', '=', $userId)
            ->order(['create_time' => 'desc'])
            ->paginate(30);
    }

}