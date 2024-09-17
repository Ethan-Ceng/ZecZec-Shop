<?php

namespace app\common\service\product\factory;


/**
 * 商品基類
 * Interface IProductService
 * @package app\common\service\product
 */
abstract class ProductService
{
    /**
     * 更新商品庫存 (針對下單減庫存的商品)
     */
    abstract function updateProductStock($productList);

    /**
     * 更新商品庫存銷量（訂單付款後）
     */
    abstract function updateStockSales($productList);

    /**
     * 回退商品庫存
     */
    abstract function backProductStock($productList, $isPayOrder);

}