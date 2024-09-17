<?php

namespace app\shop\controller\user;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;
use app\shop\model\user\Cash as CashModel;

/**
 * 提現
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
    public function audit($id)
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
     * 餘額提現：微信支付企業付款
     */
    public function wxpay($id)
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

    /**
     * 提現設定
     */
    public function setting()
    {
        if ($this->request->isGet()) {
            $values = SettingModel::getItem('balance_cash');
            $pay_type = (new CashModel)->getPayType();
            return $this->renderSuccess('', compact('values', 'pay_type'));
        }
        $model = new SettingModel;
        if ($model->edit('balance_cash', $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }
}