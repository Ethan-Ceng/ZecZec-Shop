<?php

namespace app\shop\controller\appsetting;

use app\shop\controller\Controller;
use app\shop\model\app\AppWx as AppWxModel;

/**
 * 微信小程式設定
 */
class Appwx extends Controller
{
    /**
     * 修改
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new AppWxModel;
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
        // 當前微信小程式資訊
        $data = AppWxModel::detail();
        return $this->renderSuccess('', compact('data'));
    }

}
