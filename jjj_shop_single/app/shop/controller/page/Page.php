<?php

namespace app\shop\controller\page;

use app\shop\controller\Controller;
use app\shop\model\page\Page as PageModel;
use app\shop\model\page\PageCategory as PageCategoryModel;

/**
 * App頁面管理
 */
class Page extends Controller
{
    /**
     * 頁面列表
     */
    public function list()
    {
        $model = new PageModel;
        //查詢預設
        $default = $model::getDefault();
        $list = $model->getList($this->postData(), 10);
        return $this->renderSuccess('', compact('list', 'default'));
    }

    /**
     * 頁面列表
     */
    public function index()
    {
        $model = new PageModel;
        $list = $model->getList($this->postData(), 20);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增頁面
     */
    public function add()
    {
        $model = new PageModel;
        if ($this->request->isGet()) {
            return $this->renderSuccess('', [
                'defaultData' => $model->getDefaultItems(),
                'jsonData' => ['page' => $model->getDefaultPage(), 'items' => []],
                'opts' => [
                    'catgory' => [],
                ]
            ]);
        }
        // 接收post資料
        $post = $this->postData();
        if (!$model->add(json_decode($post['params'], true))) {
            return $this->renderError($model->getError() ?: '新增失敗');
        }
        return $this->renderSuccess('新增成功');
    }

    /**
     * 首頁編輯
     * @return \think\response\Json
     */
    public function home()
    {
        return $this->edit();
    }

    /**
     * 編輯頁面
     */
    public function edit($page_id = null)
    {
        $model = $page_id > 0 ? PageModel::detail($page_id) : PageModel::getHomePage();
        if ($this->request->isGet()) {
            $jsonData = $model['page_data'];
            jsonRecursive($jsonData);
            return $this->renderSuccess('', [
                'defaultData' => $model->getDefaultItems(),
                'jsonData' => $jsonData,
                'opts' => [
                    'catgory' => [],
                ]
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
     * 刪除頁面
     */
    public function delete($page_id)
    {
        // 幫助詳情
        $model = PageModel::detail($page_id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

    /**
     * 分類模板
     */
    public function category()
    {
        $model = PageCategoryModel::detail();
        if ($this->request->isGet()) {
            return $this->renderSuccess('', compact('model'));
        }
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 新增頁面
     */
    public function addPage()
    {
        $model = new PageModel;
        if ($this->request->isGet()) {
            return $this->renderSuccess('', [
                'defaultData' => $model->getDefaultItems(),
                'jsonData' => ['page' => $model->getDefaultPage(), 'items' => []],
                'opts' => [
                    'catgory' => [],
                ]
            ]);
        }
        // 接收post資料
        $post = $this->postData();
        if (!$model->add(json_decode($post['params'], true), 10)) {
            return $this->renderError($model->getError() ?: '新增失敗');
        }
        return $this->renderSuccess('新增成功');
    }

    /**
     * 編輯頁面
     */
    public function editPage($page_id)
    {
        $model = PageModel::detail($page_id);
        if ($this->request->isGet()) {
            $jsonData = $model['page_data'];
            jsonRecursive($jsonData);
            $data = (new PageModel)->getPageProduct($jsonData);
            return $this->renderSuccess('', [
                'defaultData' => $model->getDefaultItems(),
                'jsonData' => $data,
                'opts' => [
                    'catgory' => [],
                ]
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
     * 刪除頁面
     */
    public function deletePage($page_id)
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
    public function setPage($page_id)
    {
        // 頁面詳情
        $model = PageModel::detail($page_id);
        if (!$model->setDefault(10)) {
            return $this->renderError($model->getError() ?: '設定失敗');
        }
        return $this->renderSuccess('設定成功');
    }

}
