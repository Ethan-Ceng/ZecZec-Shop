<?php

namespace app\shop\model\plus\sign;

use app\common\model\plus\sign\Sign as SignModel;

/**
 * 使用者簽到模型模型
 */
class Sign extends SignModel
{
    /**
     * @param $data array 查詢條件
     * @param $days array 連續簽到天數陣列
     * @param $sign_date array 最近簽到時間
     * @return mixed
     */
    public function getList($data, $days, $sign_date)
    {
        $model = $this;

        if (isset($data['days']) && $data['days'] > -1) {
            $model = $model->where('sign.days', '=', $days[$data['days']]);
        }
        if (isset($data['sign_date']) && $data['sign_date'] > -1) {
            if ($data['sign_date'] != 3) {
                $dif_time = time() - ($sign_date[$data['sign_date']] * 24 * 60 * 60);
                $model = $model->where('sign.create_time', '>', $dif_time);
            }
            if ($data['sign_date'] == 3 && isset($data['create_time']) && !empty($data['create_time'])) {
                $data['create_time'][0] = strtotime($data['create_time'][0]);
                $data['create_time'][1] = strtotime($data['create_time'][1]);
                $model = $model->where('sign.create_time', 'between', [$data['create_time'][0], $data['create_time'][1]]);
            }
        }
        if (isset($data['nickName']) && !empty($data['nickName'])) {
            $model = $model->where('user.nickName', 'like', '%' . trim($data['nickName']) . '%');
        }

        return $model->with(['user'])->alias('sign')
            ->join('user', 'user.user_id = sign.user_id')
            ->order(['sign.create_time' => 'desc'])
            ->paginate($data);
    }

}
