<?php

namespace app\shop\model\shop;

use app\common\model\shop\LoginLog as LoginLogModel;
/**
 * 後臺管理員登入日誌模型
 */
class LoginLog extends LoginLogModel
{
    /**
     * 獲取列表資料
     */
    public function getList($params)
    {
        $model = $this;
        // 查詢條件：訂單號
        if (isset($params['username']) && !empty($params['username'])) {
            $model = $model->where('username', 'like', "%{$params['username']}%");
        }
        // 查詢列表資料
        return $model->order(['create_time' => 'desc'])
            ->paginate($params);
    }
}