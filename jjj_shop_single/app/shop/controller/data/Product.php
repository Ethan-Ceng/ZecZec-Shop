<?php

namespace app\shop\controller\data;

use app\shop\controller\Controller;
use app\shop\model\product\Product as ProductModel;
use app\shop\model\product\Category as CategoryModel;
use app\shop\service\ProductService;

/**
 * 商品資料控制器
 */
class Product extends Controller
{
    /**
     * 商品列表
     */
    public function lists()
    {
        // 商品分類
        $category = CategoryModel::getShowCacheTree();
        $model = new ProductModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list', 'category'));
    }

    /**
     * 商品規格列表，組裝成列表
     */
    public function spec($product_id)
    {
        $model = ProductModel::detail($product_id);
        $specData = ProductService::getSpecData($model);
        $specList = $this->transSpecData($specData);
        return $this->renderSuccess('', compact('specList'));
    }

    /**
     * 組裝前端用的資料
     */
    private function transSpecData($specData){
        $specList = [];
        foreach($specData['spec_list'] as $spec){
            $specIds = explode('_',$spec['spec_sku_id']);
            $spec['spec_name'] = '';
            foreach ($specIds as $specId){
                $spec['spec_name'] .= $this->searchSpecItem($specData['spec_attr'], $specId) . ';';
            }
            array_push($specList, $spec);
        }
        return $specList;
    }

    /**
     * 規格值
     */
    private function searchSpecItem($spec_attr, $item_id){
        $specValue = '';
        foreach ($spec_attr as $attr){
            foreach ($attr['spec_items'] as $item){
                if($item['item_id'] == $item_id){
                    $specValue = $attr['group_name'] . ',' . $item['spec_value'];
                    break 2;
                }
            }
        }
        return $specValue;
    }
}
