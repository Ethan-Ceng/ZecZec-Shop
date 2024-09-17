<?php

namespace app\admin\controller\file;

use app\JjjController;
use app\common\model\file\UploadImage as UploadImageModel;

class Image extends JjjController
{
    /**
     * 檔案庫列表
     */
    public function list()
    {
        // 檔案列表
        $list = (new UploadImageModel)->getlist($this->postData());
        return $this->renderSuccess('success', compact('list'));
    }

    /**
     * 相簿分類列表
     */
    public function index()
    {
        // 分組列表
        $list = (new UploadImageModel)->getCategoryList();
        return $this->renderSuccess('success', compact('list'));
    }

    /**
     * 新增分組
     */
    public function addCategory()
    {
        $model = new UploadImageModel;
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 編輯分組
     */
    public function edit($category_id, $name)
    {
        $model = UploadImageModel::detail($category_id);
        if ($model->edit($name)) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     * 刪除
     */
    public function delete($category_id)
    {
        $model = UploadImageModel::detail($category_id);
        if ($model->remove()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?: '刪除失敗');
    }

    /**
     * 批次刪除檔案
     */
    public function deleteFiles($imageIds)
    {
        $model = new UploadImageModel;
        if ($model->deleteFiles($imageIds)) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?: '刪除失敗');
    }
}
