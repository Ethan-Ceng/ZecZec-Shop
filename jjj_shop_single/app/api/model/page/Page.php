<?php

namespace app\api\model\page;

use app\api\model\plus\live\WxLive;
use app\api\model\product\Product as ProductModel;
use app\api\model\plus\article\Article;
use app\api\model\store\Store as StoreModel;
use app\common\model\page\Page as PageModel;
use app\api\model\plus\coupon\Coupon;
use app\api\model\plus\seckill\Product as SeckillProductModel;
use app\api\model\plus\seckill\Active as SeckillActiveModel;
use app\api\model\plus\assemble\Product as AssembleProductModel;
use app\api\model\plus\assemble\Active as AssembleActiveModel;
use app\api\model\plus\bargain\Product as BargainProductModel;
use app\api\model\plus\bargain\Active as BargainActiveModel;

/**
 * 首頁模型
 */
class Page extends PageModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * DIY頁面詳情
     */
    public static function getPageData($user, $page_id = null)
    {
        // 頁面詳情
        $detail = $page_id > 0 ? parent::detail($page_id) : parent::getDefault();

        // 頁面diy元素
        $items = $detail['page_data']['items'];
        // 頁面頂部導航
        isset($detail['page_data']['page']) && $items['page'] = $detail['page_data']['page'];
        // 獲取動態資料
        $model = new self;

        foreach ($items as $key => $item) {
            unset($items[$key]['defaultData']);
            if ($item['type'] === 'window') {
                $items[$key]['data'] = array_values($item['data']);
            } else if ($item['type'] === 'product') {
                $items[$key]['data'] = $model->getProductList($user, $item);
            } else if ($item['type'] === 'coupon') {
                $items[$key]['data'] = $model->getCouponList($user, $item);
            } else if ($item['type'] === 'article') {
                $items[$key]['data'] = $model->getArticleList($item);
            } else if ($item['type'] === 'special') {
                $items[$key]['data'] = $model->getSpecialList($item);
            } else if ($item['type'] === 'store') {
                $items[$key]['data'] = $model->getStoreList($item);
            } else if ($item['type'] === 'seckillProduct') {
                // 如果沒有活動，則不顯示
                $item_data = $model->getSeckillList($item);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            } else if ($item['type'] === 'assembleProduct') {
                // 如果沒有活動，則不顯示
                $item_data = $model->getAssembleList($item);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            } else if ($item['type'] === 'bargainProduct') {
                // 如果沒有活動，則不顯示
                $item_data = $model->getBargainList($item);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            } else if ($item['type'] === 'wxlive') {
                $items[$key]['data'] = $model->getWxLiveList($item);
            } else if ($item['type'] === 'previewProduct') {
                $items[$key]['data'] = $model->getPreviewList($user, $item);
            }
        }
        return ['page' => $items['page'], 'items' => $items];
    }

    /**
     * DIY個人中心頁面
     */
    public static function getCenterPageData($user, $page_id = 0)
    {
        // 頁面詳情
        $detail = $page_id > 0 ? parent::detail($page_id) : parent::getDefault(30);

        // 頁面diy元素
        $items = $detail['page_data']['items'];
        // 頁面頂部導航
        isset($detail['page_data']['page']) && $items['page'] = $detail['page_data']['page'];
        // 獲取動態資料
        $model = new self;
        foreach ($items as $key => $item) {
            unset($items[$key]['defaultData']);
            if ($item['type'] === 'product') {
                $items[$key]['data'] = $model->getProductList($user, $item);
            }
        }
        return ['page' => $items['page'], 'items' => $items];
    }

    /**
     * 商品元件：獲取商品列表
     */
    private function getProductList($user, $item)
    {
        // 獲取商品資料
        $model = new ProductModel;
        if ($item['params']['source'] === 'choice') {
            // 資料來源：手動
            $productIds = array_column($item['data'], 'product_id');
            $productList = $model->getListByIdsFromApi($productIds, $user);
        } else {
            // 資料來源：自動
            $productList = $model->getList([
                'type' => 'sell',
                'category_id' => $item['params']['auto']['category'],
                'sortType' => $item['params']['auto']['productSort'],
                'list_rows' => $item['params']['auto']['showNum']
            ], $user);
        }
        if ($productList->isEmpty()) return [];
        // 格式化商品列表
        $data = [];
        foreach ($productList as $product) {
            $show_sku = ProductModel::getShowSku($product);
            $data[] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'selling_point' => $product['selling_point'],
                'image' => $product['image'][0]['file_path'],
                'product_image' => $product['image'][0]['file_path'],
                'product_price' => $show_sku['product_price'],
                'line_price' => $show_sku['line_price'],
                'product_sales' => $product['product_sales'],
            ];
        }
        return $data;
    }

    /**
     * 優惠券元件：獲取優惠券列表
     */
    private function getCouponList($user, $item)
    {
        // 獲取優惠券資料
        return (new Coupon)->getList($user, $item['params']['limit'], true);
    }

    /**
     * 文章元件：獲取文章列表
     */
    private function getArticleList($item)
    {
        // 獲取文章資料
        $model = new Article;
        $articleList = $model->getList($item['params']['auto']['category'], $item['params']['auto']['showNum']);
        return $articleList->isEmpty() ? [] : $articleList->toArray()['data'];
    }

    /**
     * 頭條快報：獲取頭條列表
     */
    private function getSpecialList($item)
    {
        // 獲取頭條資料
        $model = new Article;
        $articleList = $model->getList($item['params']['auto']['category'], $item['params']['auto']['showNum']);
        return $articleList->isEmpty() ? [] : $articleList->toArray()['data'];
    }

    /**
     * 線下門店元件：獲取門店列表
     */
    private function getStoreList($item)
    {
        // 獲取商品資料
        $model = new StoreModel;
        if ($item['params']['source'] === 'choice') {
            // 資料來源：手動
            $storeIds = array_column($item['data'], 'store_id');
            $storeList = $model->getListByIds($storeIds);
        } else {
            // 資料來源：自動
            $storeList = $model->getList(null, false, false, $item['params']['auto']['showNum']);
        }
        if ($storeList->isEmpty()) return [];
        // 格式化商品列表
        $data = [];
        foreach ($storeList as $store) {
            $data[] = [
                'store_id' => $store['store_id'],
                'store_name' => $store['store_name'],
                'logo_image' => $store['logo']['file_path'],
                'phone' => $store['phone'],
                'region' => $store['region'],
                'address' => $store['address'],
            ];
        }
        return $data;
    }

    /**
     * 獲取限時秒殺
     */
    private function getSeckillList($item)
    {
        // 獲取秒殺資料
        $seckill = SeckillActiveModel::getActive();
        if ($seckill) {
            $product_model = new SeckillProductModel;
            $seckill['product_list'] = $product_model->getProductList($seckill['seckill_activity_id'], $item['params']['showNum']);
        }
        return $seckill;
    }

    /**
     * 獲取限時拼團
     */
    private function getAssembleList($item)
    {
        // 獲取拼團資料
        $assemble = AssembleActiveModel::getActive();
        if ($assemble) {
            $assemble->visible(['assemble_activity_id', 'title', 'start_time', 'end_time']);
            $product_model = new AssembleProductModel;
            $assemble['product_list'] = $product_model->getProductList($assemble['assemble_activity_id'], $item['params']['showNum']);
        }
        return $assemble;
    }

    /**
     * 獲取限時砍價
     */
    private function getBargainList($item)
    {
        // 獲取拼團資料
        $bargain = BargainActiveModel::getActive();
        if ($bargain) {
            $bargain->visible(['bargain_activity_id', 'title', 'start_time', 'end_time']);
            $product_model = new BargainProductModel;
            $bargain['product_list'] = $product_model->getProductList($bargain['bargain_activity_id'], $item['params']['showNum']);
        }
        return $bargain;
    }

    /**
     * 微信直播
     */
    private function getWxLiveList($item)
    {
        // 獲取頭條資料
        $model = new WxLive();
        $liveList = $model->getList($item['params']['showNum']);
        return $liveList->isEmpty() ? [] : $liveList->toArray()['data'];
    }

    /**
     * 獲取限時秒殺
     */
    private function getPreviewList($user, $item)
    {
        // 獲取商品資料
        $model = new ProductModel;
        // 資料來源：自動
        $productList = $model->getList([
            'type' => 'preview',
            'list_rows' => $item['params']['showNum']
        ], $user);
        if ($productList->isEmpty()) return [];
        // 格式化商品列表
        $data = [];
        foreach ($productList as $product) {
            $show_sku = ProductModel::getShowSku($product);
            $data[] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'selling_point' => $product['selling_point'],
                'image' => $product['image'][0]['file_path'],
                'product_image' => $product['image'][0]['file_path'],
                'product_price' => $show_sku['product_price'],
                'line_price' => $show_sku['line_price'],
                'product_sales' => $product['product_sales'],
                'preview_time' => $product['preview_time'],
                'is_preview' => $product['is_preview'],
            ];
        }
        return $data;
    }
}