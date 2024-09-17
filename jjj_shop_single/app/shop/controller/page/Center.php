<?php

namespace app\shop\controller\page;

use app\shop\controller\Controller;
use app\shop\model\page\Page as PageModel;

/**
 * 個人中心控制器
 */
class Center extends Controller
{
    /**
     * 列表
     */
    public function index()
    {
        $model = new PageModel;
        //查詢預設
        $default = $model::getDefault(30);
        $list = $model->getList($this->postData(), 30);
        return $this->renderSuccess('', compact('list', 'default'));
    }

    /**
     * 新增
     */
    public function add()
    {
        $model = new PageModel;
        if ($this->request->isGet()) {
            return $this->renderSuccess('', [
                'defaultData' => $model->getCenterDefaultItems(),
                'jsonData' => ['page' => $model->getDefaultPage(), 'items' => []],
            ]);
        }
        // 接收post資料
        $post = $this->postData();
        if (!$model->add(json_decode($post['params'], true), 30)) {
            return $this->renderError($model->getError() ?: '新增失敗');
        }
        return $this->renderSuccess('新增成功');
    }

    /**
     * 修改
     */
    public function edit($page_id)
    {
        $model = PageModel::detail($page_id);
        if ($this->request->isGet()) {
            $jsonData = $model['page_data'];
            jsonRecursive($jsonData);
            return $this->renderSuccess('', [
                'defaultData' => $model->getCenterDefaultItems(),
                'jsonData' => $jsonData,
            ]);
        }
        // 接收post資料
        $post = $this->postData();
        if (!$model->edit(json_decode($post['params'], true))) {
            return $this->renderError($model->getError() ?: '更新失敗');
        }
        return $this->renderSuccess('更新成功');
    }

    /**
     * 刪除記錄
     */
    public function delete($page_id)
    {
        // 詳情
        $model = PageModel::detail($page_id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

    /**
     * 設定預設
     */
    public function set($page_id)
    {
        // 頁面詳情
        $model = PageModel::detail($page_id);
        if (!$model->setDefault(30)) {
            return $this->renderError($model->getError() ?: '設定失敗');
        }
        return $this->renderSuccess('設定成功');
    }
}
