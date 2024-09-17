<?php

namespace app\api\model\plus\lottery;

use app\common\model\plus\lottery\Record as RecordModel;

/**
 * Class GiftPackage
 * 記錄模型
 * @package app\common\model\plus\giftpackage
 */
class Record extends RecordModel
{
    /**
     * 記錄列表
     * @param $data
     */
    public function getList($data, $user)
    {
        $model = $this;
        if (isset($data['type']) && $data['type'] > 0) {
            if ($data['type'] == 1) {
                $model = $model->where('prize_type', '=', 3);
            } else {
                $model = $model->where('prize_type', '<>', 3);
            }
        }
        return $model->alias('r')
            ->where('user_id', '=', $user['user_id'])
            ->field('r.*')
            ->order('r.create_time', 'desc')
            ->paginate($data);
    }

    /**
     * 記錄列表
     * @param $data
     */
    public function getLimitList($limit)
    {
        $model = $this;
        return $model->alias('r')
            ->with(['user'])
            ->field('r.*')
            ->where('is_play', '=', 1)
            ->order('r.create_time', 'desc')
            ->limit($limit)
            ->select();
    }
}