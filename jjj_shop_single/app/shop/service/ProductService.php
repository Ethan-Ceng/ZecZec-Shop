<?php

namespace app\shop\service;

use app\common\model\plus\agent\Setting as AgentSetting;
use app\common\service\product\BaseProductService;
use app\shop\model\product\Category as CategoryModel;
use app\shop\model\settings\Delivery as DeliveryModel;
use app\shop\model\user\Grade as GradeModel;
use app\shop\model\plus\point\Product as PointsProductModel;
use app\shop\model\plus\assemble\Product as AssembleProductModel;
use app\shop\model\plus\bargain\Product as BargainProductModel;
use app\shop\model\plus\seckill\Product as SeckillProductModel;
use app\shop\model\plus\table\Table as TableModel;

/**
 * 商品服務類
 */
class ProductService extends BaseProductService
{
    /**
     * 商品管理公共資料
     */
    public static function getEditData($model = null, $scene = 'edit')
    {
        // 商品分類
        $category = CategoryModel::getCacheTree();
        // 配送模板
        $delivery = DeliveryModel::getAll();
        // 會員等級列表
        $gradeList = GradeModel::getUsableList();
        // 商品sku資料
        $specData = static::getSpecData($model);
        // 商品規格是否鎖定
        $isSpecLocked = static::checkSpecLocked($model, $scene);
        // 平臺分銷規則
        $basicSetting = AgentSetting::getItem('basic');
        // 所有萬能表單
        $tableList = (new TableModel())->getAll();
        return compact('category', 'delivery', 'gradeList', 'specData', 'isSpecLocked', 'basicSetting', 'tableList');
    }

    /**
     * 驗證商品是否允許刪除
     */
    public static function checkSpecLocked($model = null, $scene = 'edit')
    {
        if ($model == null || $scene == 'copy') return false;
        $service = new static;
        // 積分
        if ($service->checkPointsProduct($model['product_id'])) return true;
        // 拼團
        if ($service->checkAssembleProduct($model['product_id'])) return true;
        // 砍價
        if ($service->checkBargainProduct($model['product_id'])) return true;
        // 秒殺
        if ($service->checkSeckillProduct($model['product_id'])) return true;
        return false;
    }

    /**
     * 驗證商品是否參與了積分商品
     */
    private function checkPointsProduct($productId)
    {
        return PointsProductModel::isExistProductId($productId);
    }

    /**
     * 驗證商品是否參與了拼團商品
     */
    private function checkAssembleProduct($productId)
    {
        return AssembleProductModel::isExistProductId($productId);
    }

    /**
     * 驗證商品是否參與了砍價商品
     */
    private function checkBargainProduct($productId)
    {
        return BargainProductModel::isExistProductId($productId);
    }

    /**
     * 驗證商品是否參與了秒殺商品
     */
    private function checkSeckillProduct($productId)
    {
        return SeckillProductModel::isExistProductId($productId);
    }
}