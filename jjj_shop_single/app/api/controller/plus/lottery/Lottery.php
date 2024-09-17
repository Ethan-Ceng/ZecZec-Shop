<?php

namespace app\api\controller\plus\lottery;

use app\api\controller\Controller;
use app\api\model\plus\lottery\Lottery as LotteryModel;
use app\api\model\plus\lottery\Record as RecordModel;
use app\shop\model\plus\lottery\LotteryPrize as LotteryPrizeModel;

/**
 * 轉盤控制器
 */
class Lottery extends Controller
{
    /**
     * 獲取資料
     * @param null $id
     */
    public function getLottery()
    {
        $model = new LotteryModel();
        $data = $model->getDetail();
        //剩餘抽獎次數
        $num = $model->getNum($this->getUser());
        $nums = $data['times'] - $num;
        //抽獎播放資料
        $recordModel = new RecordModel();
        $recordList = $recordModel->getLimitList(60);
        $data['user_points'] = $this->getUser()['points'];
        return $this->renderSuccess('', compact('data', 'nums', 'recordList'));
    }

    /*
     * 轉盤記錄列表
     */
    public function record()
    {
        $model = new RecordModel();
        $list = $model->getList($this->postData(), $this->getUser());
        return $this->renderSuccess('', compact('list'));
    }

    /*
     * 開始抽獎
     */
    public function draw()
    {
        $model = new LotteryModel();
        $result = $model->getdraw($this->getUser());
        if ($result) {
            return $this->renderSuccess('', compact('result'));
        }
        return $this->renderError($model->getError() ?: '抽獎失敗');
    }
}