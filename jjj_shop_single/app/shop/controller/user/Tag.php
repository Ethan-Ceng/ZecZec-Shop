<?php

namespace app\shop\controller\user;

use app\shop\controller\Controller;
use app\shop\model\user\Tag as TagModel;

/**
 * 會員等級
 */
class Tag extends Controller
{
    /**
     * 會員等級列表
     */
    public function index()
    {
        $model = new TagModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增等級
     */
    public function add()
    {
        $model = new TagModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }

    /**
     * 編輯會員等級
     */
    public function edit($tag_id)
    {
        $model = TagModel::detail($tag_id);
        // 修改記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess();
        }
        return $this->renderError();
    }

    /**
     * 刪除會員等級
     */
    public function delete($tag_id)
    {
        // 會員等級詳情
        $model = TagModel::detail($tag_id);
        if (!$model->deleteTag()) {
            return $this->renderError('刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

}