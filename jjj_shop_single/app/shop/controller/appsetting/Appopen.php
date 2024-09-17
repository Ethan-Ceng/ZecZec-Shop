<?php

namespace app\shop\controller\appsetting;

use app\shop\controller\Controller;
use app\shop\model\app\AppOpen as AppOpenModel;

/**
 * app開放平臺設定
 */
class Appopen extends Controller
{
    /**
     * 修改
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new AppOpenModel;
        $data = $this->postData();
        unset($data['data']);
        if ($model->edit($data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 獲取微信小程式設定
     */
    public function fetchData()
    {
        // 當前app資訊
        $data = AppOpenModel::detail();
        return $this->renderSuccess('', compact('data'));
    }

}
