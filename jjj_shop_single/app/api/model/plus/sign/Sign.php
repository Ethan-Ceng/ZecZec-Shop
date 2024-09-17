<?php

namespace app\api\model\plus\sign;

use app\common\model\plus\sign\Sign as SignModel;
use app\common\exception\BaseException;
use app\api\model\settings\Setting as SettingModel;

/**
 * 使用者簽到模型模型
 */
class Sign extends SignModel
{

    /**
     * 獲取使用者簽到資訊
     */
    public function getDays($user_id, $sign_date)
    {
        $row = $this->where('user_id', '=', $user_id)->order(['create_time' => 'desc'])->find();
        if (empty($row)) {
            return 1;
        }
        $dif = (strtotime($sign_date) - strtotime($row['create_time'])) / (24 * 60 * 60);
        if (strtotime($row['sign_date']) == strtotime($sign_date)) {
            throw new BaseException(['msg' => '今天已簽到']);
        }
        if ($dif > 1) {
            return 1;
        }
        if ($dif < 1) {
            return $row['days'] + 1;
        }
        return false;
    }

    /**
     * 簽到
     */
    public function add($user)
    {
        // 更新記錄
        $this->startTrans();
        try {
            //積分別名
            $points_name = SettingModel::getPointsName();
            //獲取簽到配置
            $sign_conf = SettingModel::getItem('sign');
            if (!$sign_conf['is_open']) {
                $this->error = '簽到未開啟，請稍後再試';
                return false;
            }
            //簽到日期
            $sign_date = date('Y-m-d', time());
            $user_id = $user['user_id'];

            //獲取連續簽到天數
            $days = $this->getDays($user_id, $sign_date);

            //修改使用者積分
            $points = $user->setPoints($user_id, $days, $sign_conf, $sign_date);
            $data = [
                'user_id' => $user_id,
                'sign_date' => date('Y-m-d', time()),
                'sign_day' => intval(date('d', time())),
                'days' => $days,
                'points' => $points,
                'prize' => $points . '積分',
                'app_id' => self::$app_id
            ];
            $this->save($data);
            $this->commit();
            return "簽到成功，獎勵{$points_name}{$points}個";
        } catch (\Exception $e) {
            $this->rollback();
            return false;
        }
    }

    public function getListByUserId($user_id)
    {
        $str = date('Y-m-d', time());
        $arr = explode('-', $str);
        $start_time = strtotime($arr[0] . '-' . $arr[1] . '-01');
        $list = $this->where('user_id', '=', $user_id)
            ->where('create_time', 'between', [$start_time, time()])
            ->order(['create_time' => 'desc'])->select()->toArray();

        $res = array_column($list, 'sign_day');
        $len = count($list);

        if ($len == 0) {
            $days = 0;
            $is_sign = 0;
        } else {
            $days = $len;
            $is_sign = ($list[$len - 1]['sign_date'] == date('Y-m-d', time())) ? 1 : 0;
        }

        return [$res, $days, intval(date('d', time())), $is_sign];
    }


}
