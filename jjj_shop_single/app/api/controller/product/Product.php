<?php

namespace app\api\controller\product;

use app\api\model\plus\coupon\Coupon as CouponModel;
use app\api\model\plus\coupon\UserCoupon as UserCouponModel;
use app\api\model\product\Product as ProductModel;
use app\api\model\order\Cart as CartModel;
use app\api\controller\Controller;
use app\api\model\settings\Setting as SettingModel;
use app\api\service\common\RecommendService;
use app\common\library\helper;
use app\common\service\qrcode\ProductService;
use app\common\model\user\Favorite as FavoriteModel;
use app\api\model\shop\FullReduce as FullReduceModel;

/**
 * 商品控制器
 */
class Product extends Controller
{
    /**
     * 商品列表
     */
    public function lists()
    {
        // 整理請求的引數
        $param = array_merge($this->postData(), [
            'product_status' => 10
        ]);

        // 獲取列表資料
        $model = new ProductModel;
        $list = $model->getList($param, $this->getUser(false));
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 推薦產品
     */
    public function recommendProduct($location)
    {
        $recommend = SettingModel::getItem('recommend');
        $model = new ProductModel;
        $is_recommend = RecommendService::checkRecommend($recommend, $location);
        $list = [];
        if ($is_recommend) {
            $list = $model->getRecommendProduct($recommend);
        }
        return $this->renderSuccess('', compact('list', 'recommend', 'is_recommend'));
    }

    /**
     * 獲取商品詳情
     */
    public function detail($product_id, $url = '')
    {
        // 使用者資訊
        $user = $this->getUser(false);
        // 商品詳情
        $model = new ProductModel;
        $product = $model->getDetails($product_id, $user);
        if ($product === false) {
            return $this->renderError($model->getError() ?: '商品資訊不存在');
        }
        // 多規格商品sku資訊
        $specData = $product['spec_type'] == 20 ? $model->getManySpecData($product['spec_rel'], $product['sku']) : null;
        // 優惠資訊
        $discount = [
            // 商品滿減
            'product_reduce' => FullReduceModel::getListByProduct($product_id),
            // 贈送積分
            'give_points' => $this->getGivePoints($product),
            // 商品優惠券
            'product_coupon' => $this->getCouponList($product),
        ];
        //是否顯示優惠
        $show_discount = false;
        if (count($discount['product_reduce']) > 0
            || $discount['give_points'] > 0
            || count($discount['product_coupon']) > 0) {
            $show_discount = true;
        }
        return $this->renderSuccess('', [
            // 商品詳情
            'detail' => $product,
            // 優惠資訊
            'discount' => $discount,
            // 顯示優惠
            'show_discount' => $show_discount,
            // 購物車商品總數量
            'cart_total_num' => $user ? (new CartModel())->getProductNum($user) : 0,
            // 多規格商品sku資訊
            'specData' => $specData,
            // 是否收藏
            'is_fav' => $user ? FavoriteModel::isFav($product_id, $user['user_id']) : false,
            // 微信公眾號分享引數
            'share' => $this->getShareParams($url, $product['product_name'], $product['product_name'], '/pages/product/detail/detail', $product['image'][0]['file_path']),
        ]);
    }

    /**
     * 贈送積分
     */
    private function getGivePoints($product)
    {
        if ($product['is_points_gift'] == 0) {
            return 0;
        }
        // 積分設定
        $setting = SettingModel::getItem('points');
        // 條件：後臺開啟開啟購物送積分
        if (!$setting['is_shopping_gift']) {
            return 0;
        }
        // 積分贈送比例
        $ratio = $setting['gift_ratio'] / 100;
        // 計算贈送積分數量
        return helper::bcmul($product['product_price'], $ratio, 0);
    }

    /**
     * 獲取商品可用優惠券
     */
    private function getCouponList($product)
    {
        // 可領取優惠券
        $model = new CouponModel;
        $user = $this->getUser(false);
        $couponList = $model->getList($user, null, true);
        foreach ($couponList as $item) {
            // 限制商品
            if ($item['apply_range'] == 20) {
                $product_ids = explode(',', $item['product_ids']);
                if (!in_array($product['product_id'], $product_ids)) {
                    unset($item);
                }
            } // 限制分類
            else if ($item['apply_range'] == 30) {
                $category_ids = explode(',', $item['category_ids']);
                if (!in_array($product['category_id'], $category_ids)) {
                    unset($item);
                }
            }
        }
        return $couponList;
    }

    /**
     * 預估產品
     */
    public function previewProduct()
    {
        // 整理請求的引數
        $param = array_merge($this->postData(), [
            'type' => 'preview',
        ]);
        // 獲取列表資料
        $model = new ProductModel;
        $list = $model->getList($param, $this->getUser(false));
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 生成商品海報
     */
    public function poster($product_id, $source)
    {
        // 商品詳情
        $detail = ProductModel::detail($product_id);
        $Qrcode = new ProductService($detail, $this->getUser(false), $source);
        return $this->renderSuccess('', [
            'qrcode' => $Qrcode->getImage(),
        ]);
    }
}