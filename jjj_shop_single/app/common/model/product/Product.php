<?php

namespace app\common\model\product;

use app\common\library\helper;
use app\common\model\BaseModel;

/**
 * 商品模型
 */
class Product extends BaseModel
{
    protected $name = 'product';
    protected $pk = 'product_id';
    protected $append = ['product_sales', 'type_text'];

    /**
     * 計算顯示銷量 (初始銷量 + 實際銷量)
     */
    public function getProductSalesAttr($value, $data)
    {
        return $data['sales_initial'] + $data['sales_actual'];
    }

    /**
     * 獲取器：單獨設定折扣的配置
     */
    public function getAloneGradeEquityAttr($json)
    {
        return $json ? json_decode($json, true) : '';
    }

    /**
     * 修改器：單獨設定折扣的配置
     */
    public function setAloneGradeEquityAttr($data)
    {
        return json_encode($data);
    }

    /**
     * 修改器：預告開啟購買時間
     */
    public function setPreviewTimeAttr($value)
    {
        return $value ? strtotime($value) : 0;
    }

    /**
     * 設定自定義表單資訊
     * @param $value
     * @return array
     */
    public function setCustomFormAttr($value)
    {
        return $value ? json_encode($value) : '';
    }

    /**
     * 獲取自定義表單資訊
     * @param $value
     * @return array
     */
    public function getCustomFormAttr($value)
    {
        return $value ? json_decode($value, true) : '';
    }

    /**
     * 關聯商品分類表
     */
    public function category()
    {
        return $this->belongsTo('app\\common\\model\\product\\Category');
    }

    /**
     * 關聯商品規格表
     */
    public function sku()
    {
        return $this->hasMany('ProductSku')->order(['product_sku_id' => 'asc']);
    }

    /**
     * 關聯商品規格關係表
     */
    public function specRel()
    {
        return $this->hasMany('ProductSpecRel')->order(['id' => 'asc']);
    }

    /**
     * 關聯商品圖片表
     */
    public function image()
    {
        return $this->hasMany('app\\common\\model\\product\\ProductImage')->where('image_type', '=', 0)->order(['id' => 'asc']);
    }

    /**
     * 關聯商品詳情圖片表
     */
    public function contentImage()
    {
        return $this->hasMany('app\\common\\model\\product\\ProductImage')->where('image_type', '=', 1)->order(['id' => 'asc']);
    }

    /**
     * 關聯運費模板表
     */
    public function delivery()
    {
        return $this->BelongsTo('app\\common\\model\\settings\\Delivery');
    }

    /**
     * 關聯訂單評價表
     */
    public function commentData()
    {
        return $this->hasMany('app\\common\\model\\product\\Comment', 'product_id', 'product_id');
    }

    /**
     * 關聯影片
     */
    public function video()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'video_id');
    }

    /**
     * 關聯影片封面
     */
    public function poster()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'poster_id');
    }

    /**
     * 計費方式
     */
    public function getProductStatusAttr($value)
    {
        $status = [10 => '上架', 20 => '下架', 30 => '草稿'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 獲取商品列表
     */
    public function getList($param)
    {
        // 商品列表獲取條件
        $params = array_merge([
            'type' => 'sell',         // 商品狀態
            'category_id' => 0,     // 分類id
            'sortType' => 'all',    // 排序型別
            'sortPrice' => false,   // 價格排序 高低
            'list_rows' => 15,       // 每頁數量
        ], $param);

        // 篩選條件
        $filter = [];
        $model = $this;
        if ($params['category_id'] > 0) {
            $arr = Category::getSubCategoryId($params['category_id']);
            $model = $model->where('category_id', 'IN', $arr);
        }
        if (!empty($params['product_name'])) {
            $model = $model->where('product_name', 'like', '%' . trim($params['product_name']) . '%');
        }
        if (!empty($params['search'])) {
            $model = $model->where('product_name', 'like', '%' . trim($params['search']) . '%');
        }
        if (isset($params['spec_type']) && $params['spec_type'] > 0) {
            $model = $model->where('spec_type', '=', $params['spec_type']);
        }
        if (isset($params['is_agent']) && $params['is_agent'] >= 0) {
            $model = $model->where('is_agent', '=', $params['is_agent']);
        }
        // 自定義商品大類
        if (isset($params['p_type']) && $params['p_type'] > 0) {
            $model = $model->where('type', '=', $params['p_type']);
        }
        // 排序規則
        $sort = [];
        if ($params['sortType'] === 'all') {
            $sort = ['product_sort', 'product_id' => 'desc'];
        } else if ($params['sortType'] === 'sales') {
            $sort = ['product_sales' => 'desc'];
        } else if ($params['sortType'] === 'price') {
            $sort = $params['sortPrice'] ? ['product_max_price' => 'desc'] : ['product_min_price'];
        }
        if (isset($params['type'])) {
            //出售中
            if ($params['type'] == 'sell') {
                $model = $model->where('product_status', '=', 10);
            }
            //庫存緊張
            if ($params['type'] == 'stock') {
                $model = $model->whereBetween('product_stock', [1, 10]);
            }
            //已售罄
            if ($params['type'] == 'over') {
                $model = $model->where('product_stock', '=', 0);
            }
            //倉庫中
            if ($params['type'] == 'lower') {
                $model = $model->where('product_status', '=', 20);
            }
            //回收站
            if ($params['type'] == 'recovery') {
                $model = $model->where('product_status', '=', 30);
            }
            //預告商品
            if ($params['type'] == 'preview') {
                $model = $model->where('product_status', '=', 10)
                    ->where('is_preview', '=', 1)
                    ->where('preview_time', '>', time());
            }
        }
        // 商品表名稱
        $tableName = $this->getTable();
        // 多規格商品 最高價與最低價
        $ProductSku = new ProductSku;
        $minPriceSql = $ProductSku->field(['MIN(product_price)'])
            ->where('product_id', 'EXP', "= `$tableName`.`product_id`")->buildSql();
        $maxPriceSql = $ProductSku->field(['MAX(product_price)'])
            ->where('product_id', 'EXP', "= `$tableName`.`product_id`")->buildSql();
        // 執行查詢

        $list = $model
            ->field(['*', '(sales_initial + sales_actual) as product_sales',
                "$minPriceSql AS product_min_price",
                "$maxPriceSql AS product_max_price"
            ])
            ->with(['category', 'image.file', 'sku'])
            ->where('is_delete', '=', 0)
            ->where($filter)
            ->order($sort)
            ->paginate($params);

        // 整理列表資料並返回
        return $this->setProductListData($list, true);
    }

    /**
     * 獲取商品列表
     */
    public function getLists($param)
    {
        // 商品列表獲取條件
        $params = array_merge([
            'product_status' => 10,         // 商品狀態
            'category_id' => 0,     // 分類id
        ], $param);
        // 篩選條件
        $model = $this;
        if ($params['category_id'] > 0) {
            $arr = Category::getSubCategoryId($params['category_id']);
            $model = $model->where('category_id', 'IN', $arr);
        }
        if (!empty($params['product_name'])) {
            $model = $model->where('product_name', 'like', '%' . trim($params['product_name']) . '%');
        }
        if (!empty($params['search'])) {
            $model = $model->where('product_name', 'like', '%' . trim($params['search']) . '%');
        }
        $list = $model
            ->with(['category', 'image.file'])
            ->where('is_delete', '=', 0)
            ->where('product_status', '=', $params['product_status'])
            ->paginate($params);
        // 整理列表資料並返回
        return $this->setProductListData($list, true);
    }

    /**
     * 設定商品展示的資料
     */
    protected function setProductListData($data, $isMultiple = true, callable $callback = null)
    {
        if (!$isMultiple) $dataSource = [&$data]; else $dataSource = &$data;
        // 整理商品列表資料
        foreach ($dataSource as &$product) {
            // 商品主圖
            $product['product_image'] = $product['image'][0]['file_path'];
            // 商品預設規格
            $product['product_sku'] = self::getShowSku($product);
            // 等級id轉換成陣列
            if (!is_array($product['grade_ids'])) {
                if ($product['grade_ids'] != '') {
                    $product['grade_ids'] = explode(',', $product['grade_ids']);
                } else {
                    $product['grade_ids'] = [];
                }
            }
            // 商品起始時間
            $product['active_time'] = [
                date('Y-m-d H:i:s', $product['start_time']),
                date('Y-m-d H:i:s', $product['end_time']),
            ];
            // 回撥函式
            is_callable($callback) && call_user_func($callback, $product);
        }
        return $data;
    }

    /**
     * 根據商品id集獲取商品列表
     */
    public function getListByIds($productIds, $status = null)
    {
        $model = $this;
        $filter = [];
        // 篩選條件
        $status > 0 && $filter['product_status'] = $status;
        if (!empty($productIds)) {
            $model = $model->orderRaw('field(product_id, ' . implode(',', $productIds) . ')');
        }
        // 獲取商品列表資料
        $data = $model->with(['category', 'image.file', 'sku'])
            ->where($filter)
            ->where('product_id', 'in', $productIds)
            ->select();

        // 整理列表資料並返回
        return $this->setProductListData($data, true);
    }

    /**
     * 根據商品id集獲取商品列表
     */
    public function getListByCatIds($categoryIds, $status = null)
    {
        $model = $this;
        $filter = [];
        // 篩選條件
        $status > 0 && $filter['product_status'] = $status;
        // 獲取商品列表資料
        $data = $model->with(['category', 'image.file', 'sku'])
            ->where($filter)
            ->where('category_id', 'in', $categoryIds)
            ->paginate();

        // 整理列表資料並返回
        return $this->setProductListData($data, true);
    }

    /**
     * 商品多規格資訊
     */
    public function getManySpecData($specRel, $skuData)
    {
        // spec_attr
        $specAttrData = [];
        foreach ($specRel as $item) {
            if (!isset($specAttrData[$item['spec_id']])) {
                $specAttrData[$item['spec_id']] = [
                    'group_id' => $item['spec']['spec_id'],
                    'group_name' => $item['spec']['spec_name'],
                    'spec_items' => [],
                ];
            }
            $spec_value = (new SpecValue())::where('spec_value_id', '=', $item['spec_value_id'])->value('spec_value');
            $specAttrData[$item['spec_id']]['spec_items'][] = [
                'item_id' => $item['spec_value_id'],
                'spec_value' => $spec_value,
            ];
        }
        // spec_list
        $specListData = [];
        foreach ($skuData as $item) {
            $image = (isset($item['image']) && !empty($item['image'])) ? $item['image'] : ['file_id' => 0, 'file_path' => ''];
            $specListData[] = [
                'product_sku_id' => $item['product_sku_id'],
                'spec_sku_id' => $item['spec_sku_id'],
                'rows' => [],
                'spec_form' => [
                    'image_id' => $image['file_id'],
                    'image_path' => $image['file_path'],
                    'detail' => $item['detail'],
                    'type' => $item['type'],
                    'product_no' => $item['product_no'],
                    'product_price' => $item['product_price'],
                    'product_weight' => $item['product_weight'],
                    'line_price' => $item['line_price'],
                    'low_price' => $item['low_price'],
                    'stock_num' => $item['stock_num'],
                ],
            ];
        }
        return ['spec_attr' => array_values($specAttrData), 'spec_list' => $specListData];
    }

    /**
     * 多規格表格資料
     */
    public function getManySpecTable($product)
    {
        $specData = $this->getManySpecData($product['spec_rel'], $product['sku']);
        $totalRow = count($specData['spec_list']);
        foreach ($specData['spec_list'] as $i => &$sku) {
            $rowData = [];
            $rowCount = 1;
            foreach ($specData['spec_attr'] as $attr) {
                $skuValues = $attr['spec_items'];
                $rowCount *= count($skuValues);
                $anInterBankNum = ($totalRow / $rowCount);
                $point = (($i / $anInterBankNum) % count($skuValues));
                if (0 === ($i % $anInterBankNum)) {
                    $rowData[] = [
                        'rowspan' => $anInterBankNum,
                        'item_id' => $skuValues[$point]['item_id'],
                        'spec_value' => $skuValues[$point]['spec_value']
                    ];
                }
            }
            $sku['rows'] = $rowData;
        }
        return $specData;
    }


    /**
     * 獲取商品詳情
     */
    public static function detail($product_id)
    {
        $model = (new static())->with([
            'category',
            'image.file',
            'sku.image',
            'spec_rel.spec',
            'video',
            'poster',
            'contentImage.file',
        ])->where('product_id', '=', $product_id)
            ->find();
        if (empty($model)) {
            return $model;
        }
        // 整理商品資料並返回
        return $model->setProductListData($model, false);
    }

    /**
     * 指定的商品規格資訊
     */
    public static function getProductSku($product, $specSkuId)
    {
        // 獲取指定的sku
        $productSku = [];
        foreach ($product['sku'] as $item) {
            if ($item['spec_sku_id'] == $specSkuId) {
                $productSku = $item;
                break;
            }
        }
        if (empty($productSku)) {
            return false;
        }
        // 多規格文字內容
        $productSku['product_attr'] = '';
        if ($product['spec_type'] == 20) {
            $specRelData = helper::arrayColumn2Key($product['spec_rel'], 'spec_value_id');
            $attrs = explode('_', $productSku['spec_sku_id']);
            foreach ($attrs as $specValueId) {
                $spec_value = (new SpecValue())::where('spec_value_id', '=', $specValueId)->value('spec_value');
                $productSku['product_attr'] .= $specRelData[$specValueId]['spec']['spec_name'] . ':'
                    . $spec_value . '; ';
            }
        }
        return $productSku;
    }

    /**
     * 根據商品名稱得到相關列表
     */
    public function getWhereData($product_name)
    {
        return $this->where('product_name', 'like', '%' . trim($product_name) . '%')->select();
    }

    /**
     * 顯示的sku，目前取最低價
     */
    public static function getShowSku($product)
    {
        //如果是單規格
        if ($product['spec_type'] == 10) {
            return $product['sku'][0];
        } else {
            //多規格返回最低價
            foreach ($product['sku'] as $sku) {
                if ($product['product_price'] == $sku['product_price']) {
                    return $sku;
                }
            }
        }
        // 相容歷史資料，如果找不到返回第一個
        return $product['sku'][0];
    }

    /**
     * 獲取商品詳情
     */
    public static function detailNoWith($product_id)
    {
        return (new static())->with(['image.file'])->where('product_id', '=', $product_id)
            ->find();
    }

    /**
     * 商品大分類
     */
    public function getTypeTextAttr($value, $data)
    {
        if (!isset($data['type']) || !(int)$data['type']) {
            return "";
        }
        $status = [
            1 => '群眾集資',
            2 => '預購式專案',
            3 => '訂閱式專案',
            4 => '再登場專案'
        ];
        return $status[(int)$data['type']] ?? '';
    }

}
