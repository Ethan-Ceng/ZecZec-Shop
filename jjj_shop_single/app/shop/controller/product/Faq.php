<?php

namespace app\shop\controller\product;

use app\shop\controller\Controller;
use app\shop\model\product\ProductFaq as ProductFaqModel;

/**
 * 商品評價控制器
 */
class Faq extends Controller
{
    /**
     * 評價列表
     */
    public function index()
    {
        $model = new ProductFaqModel;
        //列表
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 評價詳情
     */
    public function detail($comment_id)
    {
        // 評價詳情
        $model = new ProductFaqModel();
        $data = $model->detail($comment_id);
        return $this->renderSuccess('', compact('data'));
    }

    /**
     * 更新商品評論
     */
    public function add()
    {
        $model = new ProductFaqModel();
        // 更新記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('儲存成功');
        }
        return $this->renderError($model->getError() ?: '儲存失敗');
    }

    /**
     * 更新商品評論
     */
    public function edit()
    {
        $model = new ProductFaqModel();
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     * 刪除評價
     */
    public function delete($comment_id)
    {
        $model = new ProductFaqModel();
        if (!$model->setDelete($comment_id)) {
            return $this->renderError('刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

}