<?php

namespace app\shop\controller\store;

use app\shop\controller\Controller;
use app\shop\model\store\Clerk as ClerkModel;
use app\shop\model\store\Store as StoreModel;

/**
 * 店員控制器
 */
class Clerk extends Controller
{

    /**
     * 店員列表
     */
    public function index($store_id = 0, $search = '')
    {
        // 店員列表
        $model = new ClerkModel;
        $list = $model->getList(-1, $store_id, $search, $this->postData());
        // 門店列表
        $store_list = (new StoreModel)->getList();

        return $this->renderSuccess('', compact('list', 'store_list'));
    }

    /**
     * 新增店員
     */
    public function add()
    {
        if($this->request->isGet()){
            // 門店列表
            $store_list = StoreModel::getAllList();
            return $this->renderSuccess('', compact('store_list'));
        }
        $model = new ClerkModel;
        //傳過來的資訊
        $data = $this->postData();

        $list = $model->getAll()->toArray();
        $list_user_id = array_column($list, 'user_id');
        if (in_array($data['user_id'], $list_user_id)) {
            return $this->renderError('該使用者已經是店員，無需重複新增');
        }
        // 新增記錄
        if ($model->add($data)) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    public function detail($clerk_id)
    {
        $detail = ClerkModel::detail($clerk_id);
        // 門店列表
        $store_list = StoreModel::getAllList();
        return $this->renderSuccess('', compact('detail', 'store_list'));
    }

    /**
     * 編輯店員
     */
    public function edit($clerk_id)
    {
        if ($this->request->isGet()) {
            return $this->detail($clerk_id);
        }
        $model = ClerkModel::detail($clerk_id);
        //編輯店員的資料
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('', '更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');

    }

    /**
     * 刪除店員
     */
    public function delete($clerk_id)
    {
        // 店員詳情
        $model = ClerkModel::detail($clerk_id);
        if (!$model->setDelete()) {
            return $this->renderError('刪除失敗');
        }
        return $this->renderSuccess('', $model->getError() ?: '刪除成功');
    }

}
