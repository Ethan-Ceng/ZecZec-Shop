<?php

namespace app\shop\controller\plus\table;

use app\shop\controller\Controller;
use app\shop\model\plus\table\Record as RecordModel;
use app\shop\model\plus\table\Table as TableModel;

/**
 * 表單控制器
 */
class Record extends Controller
{

    /**
     * 優惠券列表
     */
    public function index()
    {
        $model = new RecordModel();
        $list = $model->getList($this->postData());
        // 所有表單
        $table_list = (new TableModel())->getAll();
        return $this->renderSuccess('', compact('list', 'table_list'));
    }

    /**
     * 刪除優惠券
     */
    public function delete($table_record_id)
    {
        $model = RecordModel::detail($table_record_id);
        // 更新記錄
        if ($model->setDelete()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?: '刪除失敗');
    }

    /**
     * 匯出
     */
    public function export()
    {
        $model = new RecordModel();
        return $model->exportList($this->postData());
    }
}