<?php

namespace app\api\controller\points;

use app\api\controller\Controller;
use app\api\model\user\PointsLog as PointsLogModel;
use app\api\model\settings\Setting as SettingModel;

/**
 * 積分明細控制器
 */
class Log extends Controller
{
    /**
     * 積分明細列表
     */
    public function index()
    {
        $user = $this->getUser();
        $points = $user['points'];
        $list = (new PointsLogModel)->getList($user['user_id'], $this->postData());
        //積分商城是否開放
        $is_open = SettingModel::getItem('pointsmall')['is_open'];
        //積分設定
        $setting = SettingModel::getItem('points');
        $discount_ratio = $setting['discount']['discount_ratio'];
        $is_trans_balance = $setting['is_trans_balance'];
        return $this->renderSuccess('', compact('list', 'points', 'is_open', 'discount_ratio', 'is_trans_balance'));
    }

}