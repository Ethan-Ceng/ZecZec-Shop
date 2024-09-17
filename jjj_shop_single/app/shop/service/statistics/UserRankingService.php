<?php

namespace app\shop\service\statistics;

use app\shop\model\user\User as UserModel;

/**
 * 資料統計-使用者排行
 */
class UserRankingService
{
    /**
     * 使用者消費榜
     */
    public function getUserRanking($type)
    {
        $model = new UserModel();
        $model = $model->field(['user_id', 'nickName', 'avatarUrl', 'expend_money', 'total_points', 'total_invite'])
            ->where('is_delete', '=', 0);
        if($type == 'pay'){
            $model = $model->order(['expend_money' => 'DESC']);
        } else if($type == 'points'){
            $model = $model->order(['total_points' => 'DESC']);
        } else if($type == 'invite'){
            $model = $model->order(['total_invite' => 'DESC']);
        }
        return $model->limit(10)->select();
    }

}