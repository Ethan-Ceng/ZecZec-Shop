<?php

namespace app\shop\controller\appsetting;

use app\shop\controller\Controller;
use app\shop\model\app\AppMp as AppMpModel;

/**
 * 公眾號設定
 */
class Appmp extends Controller
{
    /**
     * 修改
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new AppMpModel;
        $data = $this->postData();
        unset($data['data']);
        if ($model->edit($data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取資料
     */
    public function fetchData()
    {
        // 當前公眾號資訊
        $data = AppMpModel::detail();
        return $this->renderSuccess('', compact('data'));
    }

}
