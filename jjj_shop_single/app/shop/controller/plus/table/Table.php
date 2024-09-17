<?php

namespace app\shop\controller\plus\table;

use app\shop\controller\Controller;
use app\shop\model\plus\table\Table as TableModel;

/**
 * 表單控制器
 */
class Table extends Controller
{

    /**
     * 優惠券列表
     */
    public function index()
    {
        $model = new TableModel();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增優惠券
     */
    public function add()
    {
        $model = new TableModel();
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError()?:'新增失敗');
    }

    /**
     * 更新優惠券
     */
    public function edit($table_id)
    {
        $model = TableModel::detail($table_id);
        if($this->request->isGet()){
            return $this->renderSuccess('', compact('model'));
        }
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失敗');
    }

    /**
     * 刪除優惠券
     */
    public function delete($table_id)
    {
        $model = TableModel::detail($table_id);
        // 更新記錄
        if ($model->setDelete()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError()?:'刪除失敗');
    }
}