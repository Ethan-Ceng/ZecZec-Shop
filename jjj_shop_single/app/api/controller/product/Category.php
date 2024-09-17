<?php

namespace app\api\controller\product;

use app\api\model\order\Cart as CartModel;
use app\api\model\product\Category as CategoryModel;
use app\api\controller\Controller;
use app\api\model\product\Product as ProductModel;
use app\common\model\page\PageCategory as PageCategoryModel;

/**
 * 商品分類控制器
 */
class Category extends Controller
{
    /**
     * 分類頁面
     */
    public function index()
    {
        // 分類模板
        $template = PageCategoryModel::detail();
        // 商品分類列表
        $list = array_values(CategoryModel::getShowCacheTree());
        return $this->renderSuccess('', compact('template', 'list'));
    }

    /**
     * 購物車列表
     */
    public function lists()
    {
        // 購物車商品列表
        $productList = (new CartModel)->getList($this->getUser(false));
        if ($productList) {
            // 會員價
            $product_model = new ProductModel();
            foreach ($productList as $product) {
                $product_model->setProductGradeMoney($this->getUser(), $product);
            }
        }
        return $this->renderSuccess('', compact('productList'));
    }

}