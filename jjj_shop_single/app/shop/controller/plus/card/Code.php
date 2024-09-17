<?php

namespace app\shop\controller\plus\card;

use app\shop\controller\Controller;
use app\shop\model\plus\card\Category as CategoryModel;
use app\shop\model\plus\card\Code as CodeModel;
use app\shop\model\plus\card\Card as CardModel;

/**
 * 文章控制器
 */
class Code extends Controller
{
    /**
     * 文章列表
     */
    public function index()
    {
        $model = new CodeModel;
        $list = $model->getList($this->postData());
        foreach ($list as &$model){
            $model['start_time_str'] = date('Y-m-d H:i:s', $model['start_time']);
            $model['end_time_str'] = date('Y-m-d H:i:s', $model['end_time']);
        }
        $categoryList = CategoryModel::getALL();
        $cardList = CardModel::getALL();
        return $this->renderSuccess('', compact('list', 'categoryList', 'cardList'));
    }


    /**
     * 更新文章
     */
    public function edit($code_id)
    {
        if($this->request->isGet()){
            $model = CodeModel::detail($code_id);
            $model['start_time'] = date('Y-m-d', $model['start_time']);
            $model['end_time'] = date('Y-m-d', $model['end_time']);
            return $this->renderSuccess('', compact('model'));
        }
        // 文章詳情
        $model = CodeModel::detail($code_id);
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 訂單匯出
     */
    public function export()
    {
        $model = new CodeModel();
        return $model->exportList($this->postData());
    }
}