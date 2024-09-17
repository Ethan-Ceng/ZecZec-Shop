<?php

namespace app\shop\controller\user;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;
use app\shop\model\user\BalanceLog as BalanceLogModel;

/**
 * 餘額明細
 */
class Balance extends Controller
{
    /**
     * 餘額明細
     */
    public function log()
    {
        $model = new BalanceLogModel;
        return $this->renderSuccess('', [
            // 充值記錄列表
            'list' => $model->getList($this->postData('Params')),
            // 屬性集
            'attributes' => $model::getAttributes(),
        ]);
    }

    /**
     * 充值設定
     */
    public function setting()
    {
        if ($this->request->isGet()) {
            $values = SettingModel::getItem('balance');
            return $this->renderSuccess('', compact('values'));
        }
        $model = new SettingModel;
        if ($model->edit('balance', $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }
}