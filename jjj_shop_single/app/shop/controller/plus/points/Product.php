<?php

namespace app\shop\controller\plus\points;

use app\shop\controller\Controller;
use app\shop\model\plus\point\Product as PointProductModel;
use app\shop\model\settings\Setting as SettingModel;
use app\shop\model\order\Order as OrderModel;
use app\common\model\product\Product as ProductModel;

/**
 * 積分兌換控制器
 */
class Product extends Controller
{
    /**
     *積分商品
     */
    public function index()
    {
        $model = new PointProductModel();
        $list = $model->getList($this->postData());
        //獲取排除id
        $exclude_ids = $model->getExcludeIds();
        $exclude_ids = array_column($exclude_ids, 'product_id');
        return $this->renderSuccess('', compact('list', 'exclude_ids'));
    }

    /**
     *新增積分商品
     */
    public function add($product_id)
    {
        if($this->request->isGet()){
            $model = ProductModel::detail($product_id);
            return $this->renderSuccess('', compact('model'));
        }
        $model = new PointProductModel();
        if ($model->checkProduct($product_id)) {
            return $this->renderError('商品已經存在');
        }
        if ($model->saveProduct($this->postData(), false)) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     *修改商品
     */
    public function edit($point_product_id)
    {
        $model = PointProductModel::detail($point_product_id, ['product.image.file','sku']);
        if($this->request->isGet()){
            return $this->renderSuccess('', compact('model'));
        }

        if ($model->saveProduct($this->postData(), true)) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     *刪除商品
     */
    public function delete($id)
    {
        $model = new PointProductModel();
        if ($model->del($id)) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError('刪除失敗');
    }


    /**
     *配置
     */
    public function settings()
    {
        if($this->request->isGet()){
            $vars['values'] = SettingModel::getItem('pointsmall');
            return $this->renderSuccess('', compact('vars'));
        }

        $model = new SettingModel;
        $data = $this->request->param();
        if ($model->edit('pointsmall', $data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError()?:'操作失敗');
    }

    /**
     *獲取兌換記錄
     */
    public function record()
    {
        $model = new OrderModel;
        $list = $model->getExchange($this->postData());
        return $this->renderSuccess('', compact('list'));

    }
}