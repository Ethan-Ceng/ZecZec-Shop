<?php

namespace app\shop\model\shop;

use app\common\model\shop\OptLog as OptLogModel;
/**
 * 後臺管理員登入日誌模型
 */
class OptLog extends OptLogModel
{
    /**
     * 獲取列表資料
     */
    public function getList($params)
    {
        $model = $this;
        // 查詢條件：訂單號
        if (isset($params['username']) && !empty($params['username'])) {
            $model = $model->where('user.user_name|user.real_name', 'like', "%{$params['username']}%");
        }
        // 查詢列表資料
        return $model->alias('log')->field(['log.*','user.user_name','user.real_name'])
            ->join('shop_user user', 'user.shop_user_id = log.shop_user_id','left')
            ->order(['log.create_time' => 'desc'])
            ->paginate($params);
    }
}