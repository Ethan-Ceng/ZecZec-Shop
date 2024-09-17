<?php

namespace app\api\controller\order;

use app\api\controller\Controller;
use app\api\model\order\Cart as CartModel;
use app\api\model\product\Product as ProductModel;

/**
 * 購物車控制器
 */
class Cart extends Controller
{

    private $user;

    // $model
    private $model;

    /**
     * 構造方法
     */
    public function initialize()
    {
        $this->user = $this->getUser();
        $this->model = new CartModel;
    }

    /**
     * 購物車列表
     */
    public function lists()
    {
        // 購物車商品列表
        $productList = $this->model->getList($this->user);
        // 會員價
        $product_model = new ProductModel();
        foreach ($productList as $product) {
            $product_model->setProductGradeMoney($this->user, $product);
        }
        return $this->renderSuccess('', $productList);
    }

    /**
     * 加入購物車
     * @param int $product_id 商品id
     * @param int $product_num 商品數量
     * @param string $product_sku_id 商品sku索引
     */
    public function add()
    {
        $data = $this->request->param();
        $product_id = $data['product_id'];
        $product_num = $data['total_num'];
        $spec_sku_id = $data['spec_sku_id'];
        if (!$this->model->add($this->user,$product_id, $product_num, $spec_sku_id)) {
            return $this->renderError($this->model->getError() ?: '加入購物車失敗');
        }
        // 購物車商品總數量
        $totalNum = $this->model->getProductNum($this->user);
        return $this->renderSuccess('加入購物車成功', ['cart_total_num' => $totalNum]);
    }

    /**
     * 減少購物車商品數量
     * @param $product_id
     * @param $product_sku_id
     * @return array
     */
    public function sub($product_id, $spec_sku_id)
    {
        $this->model->sub($this->user, $product_id, $spec_sku_id);
        return $this->renderSuccess('');
    }

    /**
     * 刪除購物車中指定商品
     * @param $product_sku_id (支援字串ID集)
     * @return array
     */
    public function delete($cart_id)
    {
        $this->model->setDelete($this->user, $cart_id);
        return $this->renderSuccess('刪除成功');
    }
}