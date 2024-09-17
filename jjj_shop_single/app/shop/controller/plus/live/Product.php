<?php

namespace app\shop\controller\plus\live;

use app\shop\controller\Controller;
use app\shop\model\plus\live\WxProduct as WxProductModel;
use app\shop\model\plus\live\WxLiveProduct as WxLiveProductModel;

/**
 * 直播商品管理
 */
class Product extends Controller
{
    /**
     *直播商品列表
     */
    public function index()
    {
        $model = new WxProductModel();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     *稽核透過商品列表
     */
    public function list($room_id)
    {
        $model = new WxProductModel();
        $list = $model->getOnList($this->postData());
        $excludeIds = (new WxLiveProductModel)->getExcludeIds($room_id);
        return $this->renderSuccess('', compact('list', 'excludeIds'));
    }

    /**
     *稽核透過商品列表
     */
    public function liveProduct()
    {
        $model = new WxProductModel();
        $list = $model->getliveProduct($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增商品
     */
    public function add()
    {
        // 直播間詳情
        $model = new WxProductModel();
        if (!$model->addProduct($this->postData())) {
            return $this->renderError($model->getError() ?: '新增失敗');
        }
        return $this->renderSuccess('新增成功');
    }

    /**
     * 編輯商品
     */
    public function edit($wx_product_id)
    {
        $model = WxProductModel::detail($wx_product_id);
        if (!$model->editProduct($this->postData())) {
            return $this->renderError($model->getError() ?: '修改失敗');
        }
        return $this->renderSuccess('修改成功');
    }

    /**
     * 刪除商品
     */
    public function delete($wx_product_id)
    {
        $model = WxProductModel::detail($wx_product_id);
        if (!$model->delProduct()) {
            return $this->renderError($model->getError() ?: '刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }


}