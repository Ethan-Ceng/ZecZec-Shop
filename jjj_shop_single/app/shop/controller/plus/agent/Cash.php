<?php

namespace app\shop\controller\plus\agent;

use app\shop\controller\Controller;
use app\shop\model\plus\agent\Cash as CashModel;

/**
 * 提現申請制器
 */
class Cash extends Controller
{
    /**
     * 提現記錄列表
     */
    public function index($user_id = null, $apply_status = -1, $pay_type = -1, $search = '')
    {
        $model = new CashModel;
        $list = $model->getList($user_id, $apply_status, $pay_type, $search);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 提現稽核
     */
    public function submit($id)
    {
        $model = CashModel::detail($id);
        if ($model->submit($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 確認打款
     */
    public function money($id)
    {
        $model = CashModel::detail($id);

        if ($model->money()) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 分銷商提現：微信支付企業付款
     */
    public function wechat_pay($id)
    {
        $model = CashModel::detail($id);
        if ($model->wechatPay()) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 訂單匯出
     */
    public function export($user_id = null, $apply_status = -1, $pay_type = -1, $search = '')
    {
        $model = new CashModel();
        return $model->exportList($user_id, $apply_status, $pay_type, $search);
    }
}