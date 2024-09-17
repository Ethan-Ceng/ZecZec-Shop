<?php

namespace app\admin\controller;


use app\admin\model\Access as AccesscModel;

/**
 * 商家使用者許可權控制器
 */
class Access extends Controller
{
    /**
     * 許可權列表
     */
    public function index()
    {
        $model = new AccesscModel;
        $list = $model->getList();
        return $this->renderSuccess('', $list);
    }

    /**
     * 新增許可權
     */
    public function add()
    {
        $model = new AccesscModel;
        $data = $this->postData();

        if ($model->add($data)) {
            return $this->renderSuccess('新增成功', compact('model'));
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 更新許可權
     */
    public function edit()
    {
        $data = $this->postData();
        // 許可權詳情
        $model = AccesscModel::detail($data['access_id']);
        // 更新記錄
        if ($model->edit($data)) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 刪除許可權
     */
    public function delete($access_id)
    {
        $num = (new  AccesscModel())->getChildCount(['parent_id' => $access_id]);
        if ($num > 0) {
            return $this->renderError('當前選單下存在子許可權，請先刪除子選單');
        }
        $model = AccesscModel::detail($access_id);
        if ($model->remove()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?: '刪除失敗');
    }

    /**
     * 許可權狀態
     */
    public function status($access_id, $status)
    {
        $model = AccesscModel::detail($access_id);
        if ($model->status($status)) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

}