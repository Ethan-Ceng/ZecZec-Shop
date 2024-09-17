<?php

namespace app\shop\controller\user;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;
use app\shop\model\user\PointsLog as PointsLogModel;

/**
 * 積分控制器
 */
class Points extends Controller
{
    /**
     * 積分設定
     */
    public function setting()
    {
        if ($this->request->isGet()) {
            $values = SettingModel::getItem('points');
            return $this->renderSuccess('', compact('values'));
        }
        $model = new SettingModel;
        if ($model->edit('points', $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 積分明細
     */
    public function log()
    {
        // 積分明細列表
        $model = new PointsLogModel;
        $list = $model->getList($this->request->param());
        return $this->renderSuccess('', compact('list'));
    }
}