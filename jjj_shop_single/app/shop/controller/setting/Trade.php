<?php

namespace app\shop\controller\setting;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 交易設定控制器
 */
class Trade extends Controller
{
    /**
     * 交易設定
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new SettingModel;
        $data = $this->request->param();

        $arr = [
            'order' => [
                'close_days' => $data['close_days'],
                'receive_days' => $data['receive_days'],
                'refund_days' => $data['refund_days']
            ],
            'freight_rule' => $data['freight_rule'],
        ];
        if ($model->edit('trade', $arr)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取交易設定
     */
    public function fetchData()
    {
        $vars['values'] = SettingModel::getItem('trade');
        return $this->renderSuccess('', compact('vars'));
    }

}
