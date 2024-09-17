<?php

namespace app\shop\controller\plus;

use app\shop\controller\Controller;
use app\shop\model\plus\lottery\Lottery as LotteryModel;
use app\shop\model\plus\lottery\Record as RecordModel;
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
        $data = $model->getLottery();
        $data['prize'] = $data ? LotteryPrizeModel::detail($data['lottery_id']) : [];
        return $this->renderSuccess('', compact('data'));
    }

    /**
     *修改
     */
    public function setting()
    {
        $model = new LotteryModel();
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /*
     * 轉盤記錄列表
     */
    public function record()
    {
        $model = new RecordModel();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 獲取下架獎項
     * @param null $id
     */
    public function award()
    {
        $model = new LotteryModel();
        $data = $model->getLottery();
        $list = LotteryPrizeModel::getlist($this->postData(), $data['lottery_id']);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 抽獎記錄匯出
     */
    public function export()
    {
        $model = new RecordModel();
        return $model->exportList($this->postData());
    }
}