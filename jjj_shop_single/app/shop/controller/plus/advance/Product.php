<?php

namespace app\shop\controller\plus\advance;

use app\shop\controller\Controller;
use app\shop\model\plus\advance\Product as AdvanceProductModel;
use app\common\model\product\Product as ProductModel;
use app\shop\service\ProductService;

/**
 * 預售商品控制器
 */
class Product extends Controller
{
    /**
     * 預售商品列表
     */
    public function index()
    {
        $model = new AdvanceProductModel();
        $list = $model->getList($this->postData());
        //獲取排除id
        $exclude_ids = $model->getExcludeIds();
        $exclude_ids = array_column($exclude_ids, 'product_id');
        return $this->renderSuccess('', compact('list', 'exclude_ids'));
    }

    /**
     *新增預售商品
     */
    public function add($product_id)
    {
        if ($this->request->isGet()) {
            $model = ProductModel::detail($product_id);
            $specData = ProductService::getSpecData($model);
            $specList = $this->transSpecData($specData);
            return $this->renderSuccess('', compact('model', 'specList'));
        }
        $model = new AdvanceProductModel();
        if ($model->checkProduct($product_id)) {
            return $this->renderError('商品已經存在');
        }
        if ($model->saveProduct($this->postData(), false)) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 組裝前端用的資料
     */
    private function transSpecData($specData)
    {
        if (!$specData) {
            return [];
        }
        $specList = [];
        foreach ($specData['spec_list'] as $spec) {
            $specIds = explode('_', $spec['spec_sku_id']);
            $spec['spec_name'] = '';
            foreach ($specIds as $specId) {
                $spec['spec_name'] .= $this->searchSpecItem($specData['spec_attr'], $specId) . ';';
            }
            array_push($specList, $spec);
        }
        return $specList;
    }

    /**
     * 規格值
     */
    private function searchSpecItem($spec_attr, $item_id)
    {
        $specValue = '';
        foreach ($spec_attr as $attr) {
            foreach ($attr['spec_items'] as $item) {
                if ($item['item_id'] == $item_id) {
                    $specValue = $attr['group_name'] . ',' . $item['spec_value'];
                    break 2;
                }
            }
        }
        return $specValue;
    }

    /**
     *修改商品
     */
    public function edit($advance_product_id)
    {
        $model = AdvanceProductModel::detail($advance_product_id, ['product.image.file', 'sku']);
        if ($this->request->isGet()) {
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
        $model = new AdvanceProductModel();
        if ($model->del($id)) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError('刪除失敗');
    }

    /**
     *獲取diy秒刪商品
     */
    public function getDiyProduct()
    {
        $model = new AdvanceProductModel;
        $list = $model->getDiyProduct();
        return $this->renderSuccess('', compact('list'));
    }
}