<?php

namespace app\shop\controller\plus\agent;

use app\shop\controller\Controller;
use app\shop\model\plus\agent\Apply as ApplyModel;

/**
 * 分銷控制器
 */
class Apply extends Controller
{

    /**
     * 分銷商申請列表
     */
    public function index()
    {
        $model = new ApplyModel;
        $apply_list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('apply_list'));
    }

    /**
     * 稽核分銷商
     */
    public function editApplyStatus($apply_id)
    {
        $model = ApplyModel::detail($apply_id);
        if ($model->submit($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError('修改失敗');

    }


}