<?php

namespace app\api\model\product;

use app\common\model\product\Product as ProductModel;
use app\common\service\product\BaseProductService;
use app\common\library\helper;
use app\common\model\plus\advance\Product as AdvanceProductModel;
use app\api\model\plus\seckill\Active as ActiveModel;

/**
 * 商品模型
 */
class Product extends ProductModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'spec_rel',
        'delivery',
        'sales_initial',
        'sales_actual',
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * 商品詳情：HTML實體轉換回普通字元
     */
    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * 獲取商品列表
     * 增加自定義型別 p_type
     */
    public function getList($param, $userInfo = false)
    {
        // 獲取商品列表
        $data = parent::getList($param);

        // 隱藏api屬性
        !$data->isEmpty() && $data->hidden(['category', 'content', 'image', 'sku']);
        // 整理列表資料並返回
        return $this->setProductListDataFromApi($data, true, ['userInfo' => $userInfo]);
    }

    /**
     * 商品詳情
     */
    public static function detail($product_id)
    {
        // 商品詳情
        $detail = parent::detail($product_id);
        // 多規格商品sku資訊
        $detail['product_multi_spec'] = BaseProductService::getSpecData($detail);
        return $detail;
    }

    /**
     * 獲取商品詳情頁面
     */
    public function getDetails($productId, $userInfo = false)
    {
        // 獲取商品詳情
        $detail = $this->with([
            'category',
            'image' => ['file'],
            'contentImage' => ['file'],
            'sku' => ['image'],
            'spec_rel' => ['spec'],
            'delivery' => ['rule'],
            'commentData' => function ($query) {
                $query->with('user')->where(['is_delete' => 0, 'status' => 1])->limit(2);
            },
            'video',
            'poster'
        ])->withCount(['commentData' => function ($query) {
            $query->where(['is_delete' => 0, 'status' => 1]);
        }])
            ->where('product_id', '=', $productId)
            ->find();
        // 判斷商品的狀態
        if (empty($detail) || $detail['is_delete'] || $detail['product_status']['value'] != 10) {
            $this->error = '很抱歉，商品資訊不存在或已下架';
            return false;
        }
        // 更新訪問資料
        $this->where('product_id', '=', $detail['product_id'])->inc('view_times')->update();
        // 設定商品展示的資料
        $detail = $this->setProductListDataFromApi($detail, false, ['userInfo' => $userInfo]);
        // 多規格商品sku資訊
        $detail['product_multi_spec'] = BaseProductService::getSpecData($detail);
        //預售詳情
        $detail['advance'] = AdvanceProductModel::getProductAdvanceDetail($productId);
        //秒殺詳情
        $detail['secKill'] = ActiveModel::getProductSecKillDetail($productId);
        //商品起始時間
        $detail['active_time'] = [
            date('Y-m-d H:i:s', $detail['start_time']),
            date('Y-m-d H:i:s', $detail['end_time']),
        ];
        return $detail;
    }

    /**
     * 根據商品id集獲取商品列表
     */
    public function getListByIdsFromApi($productIds, $userInfo = false)
    {
        // 獲取商品列表
        $data = parent::getListByIds($productIds, 10);
        // 整理列表資料並返回
        return $this->setProductListDataFromApi($data, true, ['userInfo' => $userInfo]);
    }


    /**
     * 根據商品id集獲取商品列表
     */
    public function getListByCatIdsFromApi($categoryIds, $userInfo = false)
    {
        $category_ids = [];
        // 查詢1級分類下的二級分類
        if (!empty($categoryIds['first'])) {
            $categorys = (new Category())->where('parent_id', 'in', $categoryIds['first'])->select();
            foreach ($categorys as $item) {
                array_push($category_ids, $item['category_id']);
            }
        }
        foreach ($categoryIds['second'] as $item) {
            array_push($category_ids, $item);
        }
        // 獲取商品列表
        $data = parent::getListByCatIds($category_ids, 10);
        // 整理列表資料並返回
        return $this->setProductListDataFromApi($data, true, ['userInfo' => $userInfo]);
    }

    /**
     * 設定商品展示的資料 api模組
     */
    private function setProductListDataFromApi($data, $isMultiple, $param)
    {
        return parent::setProductListData($data, $isMultiple, function ($product) use ($param) {
            // 計算並設定商品會員價
            $this->setProductGradeMoney($param['userInfo'], $product);
        });
    }

    /**
     * 設定商品的會員價
     */
    public function setProductGradeMoney($user, &$product)
    {
        //查詢商品是否參與活動
        //預售詳情
        $advance = AdvanceProductModel::getProductAdvanceDetail($product['product_id']);
        //秒殺詳情
        $secKill = ActiveModel::getProductSecKillDetail($product['product_id']);
        $product['isActivity'] = 0;
        if ($advance || $secKill || ($product['is_preview'] == 1 && $product['preview_time'] > time())) {
            $product['isActivity'] = 1;
        }
        //獲取商品最大價格
        $productPrice = helper::getArrayColumn($product['sku'], 'product_price');
        rsort($productPrice);
        $product['product_max_price'] = isset($productPrice[0]) ? $productPrice[0] : 0;
        // 會員等級狀態
        $gradeStatus = (!empty($user) && $user['grade_id'] > 0 && !empty($user['grade']))
            && (!$user['grade']['is_delete']);
        // 判斷商品是否參與會員折扣
        if (!$gradeStatus || !$product['is_enable_grade']) {
            $product['is_user_grade'] = false;
            return;
        }
        $alone_grade_type = 10;
        // 商品單獨設定了會員折扣
        if ($product['is_alone_grade'] && isset($product['alone_grade_equity'][$user['grade_id']])) {
            if ($product['alone_grade_type'] == 10) {
                // 折扣比例
                $discountRatio = helper::bcdiv($product['alone_grade_equity'][$user['grade_id']], 100);
            } else {
                $alone_grade_type = 20;
                $discountRatio = helper::bcdiv($product['alone_grade_equity'][$user['grade_id']], $product['product_price'], 2);
            }
        } else {
            // 折扣比例
            $discountRatio = helper::bcdiv($user['grade']['equity'], 100);
        }
        if ($discountRatio < 1) {
            // 標記參與會員折扣
            $product['is_user_grade'] = true;
            // 會員折扣後的商品總金額
            if ($alone_grade_type == 20) {
                $product['product_price'] = $product['alone_grade_equity'][$user['grade_id']];
            } else {
                $product['product_price'] = helper::number2(helper::bcmul($product['product_price'], $discountRatio), true);
            }

            // 會員折扣價
            foreach ($product['sku'] as &$skuItem) {
                if ($alone_grade_type == 20) {
                    $skuItem['product_price'] = $product['alone_grade_equity'][$user['grade_id']];
                } else {
                    $skuItem['product_price'] = helper::number2(helper::bcmul($skuItem['product_price'], $discountRatio), true);
                }
            }
        } else {
            $product['is_user_grade'] = false;
        }
        //獲取商品最大價格
        $productPrice = helper::getArrayColumn($product['sku'], 'product_price');
        rsort($productPrice);
        $product['product_max_price'] = isset($productPrice[0]) ? $productPrice[0] : 0;
        sort($productPrice);
        $product['product_min_price'] = isset($productPrice[0]) ? $productPrice[0] : 0;
    }

    /**
     * 為你推薦
     */
    public function getRecommendProduct($params)
    {
        $model = $this;
        // 手動
        if ($params['choice'] == 1) {
            $product_id = array_column($params['product'], 'product_id');
            $model = $model->where('product_id', 'IN', $product_id);
            $list = $model->with(['category', 'image.file'])
                ->where('product_status', '=', 10)
                ->where('is_delete', '=', 0)
                ->select();
            // 整理列表資料並返回
            return $this->setProductListData($list, true);
        } else {
            if ($params['type'] == 10) {
                $sort = ['sales_actual' => 'desc'];
            } else if ($params['type'] == 20) {
                $sort = ['create_time' => 'desc'];
            } else if ($params['type'] == 30) {
                $sort = ['view_times' => 'desc'];
            }
            $list = $model->field(['*', '(sales_initial + sales_actual) as product_sales'])->with(['category', 'image.file'])
                ->where('product_status', '=', 10)
                ->where('is_delete', '=', 0)
                ->limit($params['num'])
                ->order($sort)
                ->select();
            return $this->setProductListData($list, true);
        }
    }

    /**
     * 查詢指定商品
     * @param $value
     */
    public function getProduct($value)
    {
        $list = $this->where('product_id', 'in', $value)->with(['image.file', 'sku', 'spec_rel' => ['spec']])->select();
        foreach ($list as $key => &$value) {
            // 多規格商品sku資訊
            $value['product_multi_spec'] = BaseProductService::getSpecData($value);
        }
        return $list;
    }

    /**
     * 查詢指定商品
     * @param $value
     */
    public function getProductList($value)
    {
        $product = json_decode($value, true);
        $product_id = array_column($product, 'product_id');
        $list = $this->where('product_id', 'in', $product_id)->with(['image.file', 'sku', 'spec_rel' => ['spec']])->select();
        foreach ($list as $key => &$value) {
            $product_sku_id = 0;
            foreach ($product as $kk => $vv) {
                if ($vv['product_id'] == $value['product_id']) {
                    $product_sku_id = $vv['product_sku_id'];
                }
            }
            // 商品sku資訊
            $value['product_sku'] = ProductModel::getProductSku($value, $product_sku_id);
        }
        return $list;
    }

}