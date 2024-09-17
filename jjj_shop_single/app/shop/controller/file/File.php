<?php

namespace app\shop\controller\file;

use app\JjjController;
use app\shop\model\file\UploadFile as UploadFileModel;
use app\shop\model\file\UploadGroup as UploadGroupModel;

class File extends JjjController
{
    /**
     * 檔案庫列表
     */
    public function lists($pageSize, $type = 'image', $group_id = -1)
    {
        // 檔案列表
        $file_list = (new UploadFileModel)->getlist(intval($group_id), $type, $pageSize);
        return $this->renderSuccess('success', compact('file_list'));
    }
	
	/**
	 * 類別列表
	 */
	public function category($type = 'image')
	{
	    // 分組列表
	    $group_list = (new UploadGroupModel)->getList($type);
	    return $this->renderSuccess('success', compact('group_list'));
	}

    /**
     * 新增分組
     */
    public function addGroup($group_name, $group_type = 'image')
    {
        $model = new UploadGroupModel;
        if ($model->add(compact('group_name', 'group_type'))) {
            $group_id = $model->getLastInsID();
            return $this->renderSuccess('新增成功',  compact('group_id', 'group_name'));
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 編輯分組
     */
    public function editGroup($group_id, $group_name)
    {
        $model = UploadGroupModel::detail($group_id);
        if ($model->edit(compact('group_name'))) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     * 刪除分組
     */
    public function deleteGroup($group_id)
    {
        $model = UploadGroupModel::detail($group_id);
        if ($model->remove()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?: '刪除失敗');
    }

    /**
     * 批次刪除檔案
     */
    public function deleteFiles($fileIds)
    {
        $model = new UploadFileModel;
        if ($model->softDelete($fileIds)) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?: '刪除失敗');
    }


    /**
     * 批次移動檔案分組
     */
    public function moveFiles($group_id, $fileIds)
    {
        $model = new UploadFileModel;
        if ($model->moveGroup($group_id, $fileIds) !== false) {
            return $this->renderSuccess('移動成功');
        }
        return $this->renderError($model->getError() ?: '移動失敗');
    }
}
