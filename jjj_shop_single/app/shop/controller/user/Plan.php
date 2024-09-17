<?php

namespace app\shop\controller\user;

use app\shop\controller\Controller;
use app\shop\model\user\BalancePlan as PlanModel;
use app\shop\model\user\BalanceOrder as BalanceOrderModel;

/**
 * 充值控制器
 */
class Plan extends Controller
{

    /**
     * 列表
     */
    public function index()
    {
        $model = new PlanModel();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增
     */
    public function add()
    {
        $model = new PlanModel();
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }

    /**
     * 更新
     */
    public function edit($plan_id)
    {
        $detail = PlanModel::detail($plan_id);
        if($this->request->isGet()){
            return $this->renderSuccess('', compact('detail'));
        }
        if ($detail->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失敗');
    }

    /**
     * 刪除
     */
    public function delete($plan_id)
    {
        // 詳情
        $model = new PlanModel;
        // 更新記錄
        if ($model->setDelete(['plan_id' => $plan_id])) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError('刪除失敗');
    }

    /**
     * 充值記錄
     */
    public function log()
    {
        $model = new BalanceOrderModel();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}