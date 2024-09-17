<?php

namespace app\api\model\order;

use app\common\exception\BaseException;
use think\facade\Cache;
use app\api\model\product\Product as ProductModel;
use app\common\model\order\Cart as CartModel;
use app\common\library\helper;

/**
 * 購物車管理
 */
class Cart extends CartModel
{
    // 錯誤資訊
    public $error = '';

    /**
     * 購物車列表 (含商品資訊)
     */
    public function getList($user, $cart_ids = [])
    {
        if (!$user) {
            return "";
        }
        // 獲取購物車商品列表
        return $this->getOrderProductList($user, $cart_ids);
    }

    /**
     * 獲取購物車中的商品列表
     */
    public function getOrderProductList($user, $cart_ids = [])
    {
        // 購物車商品列表
        $productList = [];
        // 獲取購物車列表
        $model = $this;
        if ($cart_ids) {
            $model = $model->where('cart_id', 'in', explode(',', $cart_ids));
        }
        $cartList = $model->where('user_id', '=', $user['user_id'])->select();
        if (empty($cartList)) {
            $this->setError('當前購物車沒有商品');
            return $productList;
        }
        // 購物車中所有商品id集
        $productIds = array_unique(helper::getArrayColumn($cartList, 'product_id'));
        // 獲取並格式化商品資料
        $sourceData = (new ProductModel)->getListByIds($productIds);
        $sourceData = helper::arrayColumn2Key($sourceData, 'product_id');
        //商品資訊變化
        $productStatus = 0;
        // 格式化購物車資料列表
        foreach ($cartList as $key => $item) {
            // 判斷商品不存在則自動刪除
            if (!isset($sourceData[$item['product_id']])) {
                $item->delete();
                $productStatus++;
                continue;
            }
            // 商品資訊
            $product = clone $sourceData[$item['product_id']];
            // 判斷商品是否已刪除
            if ($product['is_delete']) {
                $item->delete();
                $productStatus++;
                continue;
            }
            if ($product['is_virtual'] == 1) {
                $item->delete();
                $productStatus++;
                continue;
            }
            // 商品sku資訊
            $product['product_sku'] = ProductModel::getProductSku($product, $item['spec_sku_id']);
            $product['spec_sku_id'] = $item['spec_sku_id'];
            // 商品sku不存在則自動刪除
            if (empty($product['product_sku'])) {
                $item->delete();
                $productStatus++;
                continue;
            }
            // 商品單價
            $product['product_price'] = $product['product_sku']['product_price'];
            // 購買數量
            $product['total_num'] = $item['total_num'];
            // 商品總價
            $product['total_price'] = bcmul($product['product_price'], $item['total_num'], 2);
            //購物車id
            $product['cart_id'] = $item['cart_id'];

            $productList[] = $product->hidden(['category', 'content', 'image']);
        }
        if ($productStatus > 0) {
            throw new BaseException(['msg' => '商品資訊發生變化，請重新選擇下單']);
        }
        return $productList;
    }

    /**
     * 加入購物車
     */
    public function add($user, $productId, $productNum, $spec_sku_id)
    {
        if ($productNum <= 0) {
            $this->error = "商品購買數量不能小於1";
            return false;
        }
        // 獲取商品購物車資訊
        $cartDetail = $this->where('user_id', '=', $user['user_id'])
            ->where('product_id', '=', $productId)
            ->where('spec_sku_id', '=', $spec_sku_id)
            ->find();
        $cartProductNum = $cartDetail ? $cartDetail['total_num'] + $productNum : $productNum;
        // 獲取商品資訊
        $product = ProductModel::detail($productId);
        // 驗證商品能否加入
        if (!$product_price = $this->checkProduct($product, $spec_sku_id, $cartProductNum)) {
            return false;
        }
        // 記錄到購物車列表
        if ($cartDetail) {
            return $cartDetail->save(['total_num' => $cartDetail['total_num'] + $productNum]);
        } else {
            return $this->save([
                'user_id' => $user['user_id'],
                'product_id' => $productId,
                'spec_sku_id' => $spec_sku_id,
                'total_num' => $productNum,
                'join_price' => $product_price,
                'app_id' => self::$app_id,
            ]);
        }
    }

    /**
     * 驗證商品是否可以購買
     */
    private function checkProduct($product, $spec_sku_id, $cartProductNum)
    {
        // 判斷商品是否下架
        if (!$product || $product['is_delete'] || $product['product_status']['value'] != 10) {
            $this->setError('很抱歉，商品資訊不存在或已下架');
            return false;
        }
        // 商品sku資訊
        $product['product_sku'] = ProductModel::getProductSku($product, $spec_sku_id);
        if (!$product['product_sku']) {
            $this->setError('很抱歉，商品規格不存在');
            return false;
        }
        // 判斷商品庫存
        if ($cartProductNum > $product['product_sku']['stock_num']) {
            $this->setError('很抱歉，商品庫存不足');
            return false;
        }
        return $product['product_sku']['product_price'];
    }

    /**
     * 減少購物車中某商品數量
     */
    public function sub($user, $productId, $spec_sku_id)
    {
        $cartDetail = $this->where('user_id', '=', $user['user_id'])
            ->where('product_id', '=', $productId)
            ->where('spec_sku_id', '=', $spec_sku_id)
            ->find();
        if ($cartDetail['total_num'] <= 1) {
            return $cartDetail->delete();
        } else {
            $cartDetail->save(['total_num' => $cartDetail['total_num'] - 1]);
        }
    }

    /**
     * 刪除購物車中指定商品
     * @param string $cartIds (支援字串ID集)
     */
    public function setDelete($user, $cart_id)
    {
        return $this->where('user_id', '=', $user['user_id'])->where('cart_id', 'in', explode(',', $cart_id))->delete();
    }

    /**
     * 獲取當前使用者購物車商品總數量(含件數)
     */
    public function getTotalNum($user)
    {
        $num = $this->where('user_id', '=', $user['user_id'])->sum('total_num');
        return $num ? $num : 0;
    }

    /**
     * 獲取當前使用者購物車商品總數量(不含件數)
     */
    public function getProductNum($user)
    {
        return $this->where('user_id', '=', $user['user_id'])->count();
    }

    /**
     * 清空當前使用者購物車
     */
    public function clearAll($user, $cartIds)
    {
        return $this->where('user_id', '=', $user['user_id'])
            ->where('cart_id', 'in', explode(',', $cartIds))
            ->delete();
    }

    /**
     * 設定錯誤資訊
     */
    private function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 獲取錯誤資訊
     */
    public function getError()
    {
        return $this->error;
    }

}