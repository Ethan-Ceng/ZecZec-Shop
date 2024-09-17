<?php

namespace app\shop\controller\setting;

use app\shop\controller\Controller;
use app\shop\model\settings\ReturnAddress as ReturnAddressModel;

/**
 * 地址控制器
 */
class Address extends Controller
{
    /**
     * 地址首頁
     */
    public function index()
    {
        $model = new ReturnAddressModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增退貨地址
     */
    public function add()
    {
        // 新增記錄
        $model = new ReturnAddressModel;
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 退貨地址詳情
     */
    public function detail($address_id)
    {
        // 物流公司詳情
        $detail = ReturnAddressModel::detail($address_id);
        return $this->renderSuccess('', compact('detail'));

    }

    /**
     * 修改
     */
    public function edit($address_id)
    {
        if($this->request->isGet()){
            return $this->detail($address_id);
        }
        $model = ReturnAddressModel::detail($address_id);
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 刪除記錄
     */
    public function delete($address_id)
    {
        $model = ReturnAddressModel::detail($address_id);
        if ($model->remove()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?:'刪除失敗');
    }

}
