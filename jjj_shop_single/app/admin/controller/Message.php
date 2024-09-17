<?php

namespace app\admin\controller;

use app\admin\model\settings\MessageField as MessageFieldModel;
use app\admin\model\settings\Message as MessageModel;

class Message extends Controller
{
    /**
     * 列表
     */
    public function index()
    {
        $list = MessageModel::getAll();
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增
     */
    public function add()
    {
        $model = new MessageModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }

    /**
     * 更新許可權
     */
    public function edit()
    {
        // 許可權詳情
        $data = $this->postData();
        $model = MessageModel::detail($data['message_id']);

        // 更新記錄
        if ($model->edit($data)) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失敗');
    }

    /**
     * 刪除小程式
     */
    public function delete($message_id)
    {
        // 小程式詳情
        $model = MessageModel::detail($message_id);
        if (!$model->setDelete()) {
            return $this->renderError('操作失敗');
        }
        return $this->renderSuccess('操作成功');
    }

    /**
     * 訊息欄位列表
     */
    public function field($message_id)
    {
        $list = MessageFieldModel::getAll($message_id);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 儲存訊息欄位
     */
    public function saveField()
    {
        $model = new MessageFieldModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }
}