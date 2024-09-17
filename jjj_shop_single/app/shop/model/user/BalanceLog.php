<?php

namespace app\shop\model\user;

use app\common\model\user\BalanceLog as BalanceLogModel;

/**
 * 使用者餘額變動明細模型
 */
class BalanceLog extends BalanceLogModel
{
    /**
     * 獲取餘額變動明細列表
     */
    public function getList($query = [])
    {
        // 設定預設的檢索資料
        $params = [
            'user_id' => 0,
            'search' => '',
            'scene' => -1,
            'start_time' => '',
            'end_time' => '',
        ];
        // 合併查詢條件
        $data = array_merge($params, $query);
        $model = $this->alias('log')->field('log.*');
        // 使用者暱稱
        $data['search'] = trim($data['search']);
        !empty($data['search']) && $model = $model->where('user.nickName', 'like', "%{$data['search']}%");
        //搜尋時間段
        if (isset($data['value1']) && $data['value1'] != '') {
            $sta_time = array_shift($data['value1']);
            $end_time = array_pop($data['value1']);
            $model = $model->whereBetweenTime('log.create_time', $sta_time, date('Y-m-d 23:59:59', strtotime($end_time)));
        }
        // 餘額變動場景
        if (!empty($data['scene']) && $data['scene'] > -1) {
            $model = $model->where('log.scene', '=', (int)$data['scene']);
        }
        // 使用者ID
        if (!empty($data['user_id']) && $data['user_id'] > 0) {
            $model = $model->where('log.user_id', '=', (int)$data['user_id']);
        }
        // 獲取列表資料
        return $model->with(['user'])
            ->join('user', 'user.user_id = log.user_id')
            ->order(['log.create_time' => 'desc'])
            ->paginate($query);
    }

}