<?php

namespace app\shop\model\user;

use app\common\model\user\PointsLog as PointsLogModel;

/**
 * 使用者餘額變動明細模型
 */
class PointsLog extends PointsLogModel
{
    /**
     * 獲取積分明細列表
     */
    public function getList($query = [])
    {
        $model = $this;
        //搜尋訂單號
        if (isset($query['search']) && $query['search'] != '') {
            $model = $model->where('user.nickName', 'like', '%' . trim($query['search']) . '%');
        }
        //搜尋時間段
        if (isset($query['value1']) && $query['value1'] != '') {
            $sta_time = array_shift($query['value1']);
            $end_time = array_pop($query['value1']);
            $model = $model->whereBetweenTime('log.create_time', $sta_time, date('Y-m-d 23:59:59', strtotime($end_time)));
        }
        // 獲取列表資料
        return $model->with(['user'])
            ->alias('log')
            ->field('log.*')
            ->join('user', 'user.user_id = log.user_id')
            ->order(['log.create_time' => 'desc'])
            ->paginate($query);
    }
}