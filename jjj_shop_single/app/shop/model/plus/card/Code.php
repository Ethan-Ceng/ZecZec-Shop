<?php

namespace app\shop\model\plus\card;

use app\common\model\plus\card\Code as CodeModel;
use app\shop\service\card\ExportCodeService;

/**
 * 文章模型
 */
class Code extends CodeModel
{
    /**
     * 獲取文章列表
     */
    public function getList($params, $page = true)
    {
        $model = $this;
        // 檢索查詢條件
        $model = $model->setWhere($model, $params);
        $model = $model->alias('code')->field(['code.*,card.card_title'])
            ->join('card', 'card.card_id = code.card_id', 'left')
            ->order(['code.create_time' => 'desc']);
        if($page){
            return $model->paginate($params);
        }else{
            return $model->select();
        }
    }

    /**
     * 設定檢索查詢條件
     */
    private function setWhere($model, $data)
    {
        if (isset($data['code_no']) && $data['code_no'] != '') {
            $model = $model->where('code.code_no', '=' , trim($data['code_no']));
        }
        if (isset($data['code_status']) && $data['code_status'] != '') {
            $model = $model->where('code.code_status', '=', $data['code_status']);
        }
        if (isset($data['is_delete']) && $data['is_delete'] != '') {
            $model = $model->where('code.is_delete', '=', $data['is_delete']);
        }
        if (isset($data['category_id']) && $data['category_id'] != '') {
            $model = $model->where('card.category_id', '=', $data['category_id']);
        }
        if (isset($data['card_id']) && $data['card_id'] != '') {
            $model = $model->where('code.card_id', '=', $data['card_id']);
        }
        return $model;
    }

    public function edit($data){
        $data['start_time'] = strtotime($data['start_time'].' 00:00:00');
        $data['end_time'] = strtotime($data['end_time']. '23:59:59');
        return $this->save($data);
    }

    /**
     * 訂單匯出
     */
    public function exportList($query)
    {
        // 獲取訂單列表
        $list = $this->getList($query, false);
        // 匯出excel檔案
        (new ExportCodeService)->exportList($list);
    }
}