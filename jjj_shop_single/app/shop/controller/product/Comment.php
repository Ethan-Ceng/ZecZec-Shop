<?php

namespace app\shop\controller\product;

use app\shop\controller\Controller;
use app\shop\model\product\Comment as CommentModel;

/**
 * 商品評價控制器
 */
class Comment extends Controller
{
    /**
     * 評價列表
     */
    public function index()
    {
        $model = new CommentModel;
        //列表
        $list = $model->getList($this->postData());
        //重要資料
        $num = $model->getStatusNum(['status' => 0]);
        return $this->renderSuccess('', compact('list','num'));
    }

    /**
     * 評價詳情
     */
    public function detail($comment_id)
    {
        // 評價詳情
        $model = new CommentModel();
        $data = $model->detail($comment_id);
        return $this->renderSuccess('', compact('data'));
    }

    /**
     * 更新商品評論
     */
    public function edit()
    {
        $model = new CommentModel();
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
        $model = new CommentModel();
        if (!$model->setDelete($comment_id)) {
            return $this->renderError('刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

}