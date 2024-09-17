<?php

namespace app\shop\model\plus\lottery;

use app\common\model\plus\lottery\Record as RecordModel;
use app\shop\service\order\ExportService;

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
    public function getList($data)
    {
        $model = $this;
        //搜尋會員暱稱
        if ($data['search'] != '') {
            $model = $model->where('nickName|mobile', 'like', '%' . trim($data['search']) . '%');
        }
        if (!empty($data['reg_date'][0])) {
            $model = $model->whereTime('r.create_time', 'between', [$data['reg_date'][0], date('Y-m-d 23:59:59', strtotime($data['reg_date'][1]))]);
        }
        if ($data['status'] != '' && $data['status'] > -1) {
            $model = $model->where('r.status', '=', $data['status']);
        }
        if (isset($data['record_name']) && $data['record_name']) {
            $model = $model->where('r.record_name', 'like', '%' . trim($data['record_name']) . '%');
        }
        if (isset($data['type']) && $data['type'] > 0) {
            if ($data['type'] == 1) {
                $model = $model->where('prize_type', '=', 3);
            } else {
                $model = $model->where('prize_type', '<>', 3);
            }
        }
        return $model->alias('r')
            ->with(['user'])
            ->join('user u', 'r.user_id=u.user_id')
            ->field('r.*')
            ->order('r.create_time', 'desc')
            ->paginate($data);
    }

    /**
     * 記錄列表
     * @param $data
     */
    public function getListAll($data)
    {
        $model = $this;
        //搜尋會員暱稱
        if ($data['search'] != '') {
            $model = $model->where('nickName|mobile', 'like', '%' . trim($data['search']) . '%');
        }
        if (!empty($data['reg_date'][0])) {
            $model = $model->whereTime('r.create_time', 'between', [$data['reg_date'][0], date('Y-m-d 23:59:59', strtotime($data['reg_date'][1]))]);
        }
        if ($data['status'] != '' && $data['status'] > -1) {
            $model = $model->where('r.status', '=', $data['status']);
        }
        if (isset($data['record_name']) && $data['record_name']) {
            $model = $model->where('r.record_name', 'like', '%' . trim($data['record_name']) . '%');
        }
        if (isset($data['type']) && $data['type'] > 0) {
            if ($data['type'] == 1) {
                $model = $model->where('prize_type', '=', 3);
            } else {
                $model = $model->where('prize_type', '<>', 3);
            }
        }
        return $model->alias('r')
            ->with(['user'])
            ->join('user u', 'r.user_id=u.user_id')
            ->field('r.*')
            ->order('r.create_time', 'desc')
            ->select();
    }

    /**
     * 訂單匯出
     */
    public function exportList($data)
    {
        // 獲取訂單列表
        $list = $this->getListAll($data);
        // 匯出excel檔案
        (new Exportservice)->lotteryList($list);
    }
}