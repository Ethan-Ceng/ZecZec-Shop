<?php

namespace app\shop\controller\plus;

use app\shop\controller\Controller;
use app\shop\model\plus\buy\BuyActivity as BuyActivityModel;

/**
 * 買送
 */
class Buyactivity extends Controller
{
    /**
     * 列表
     */
    public function index()
    {
        $model = new BuyActivityModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增
     */
    public function add()
    {
        $model = new BuyActivityModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 編輯
     */
    public function edit($buy_id)
    {
        $model = BuyActivityModel::detail($buy_id);
        if ($this->request->isGet()) {
            return $this->renderSuccess('', compact('model'));
        }
        // 修改記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 刪除
     */
    public function delete($buy_id)
    {
        // 會員等級詳情
        $model = BuyActivityModel::detail($buy_id);
        if ($model->setDelete()) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError('操作失敗');
    }
}