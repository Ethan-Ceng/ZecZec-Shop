<?php

namespace app\shop\controller\plus\agent;

use app\common\library\helper;
use app\common\model\plus\agent\Setting as AgentSetting;
use app\common\model\plus\agent\Setting as AgentSettingModel;
use app\shop\controller\Controller;
use app\shop\model\plus\agent\Grade as AgentGradeModel;
use app\shop\model\product\Product as ProductModel;
use app\shop\service\ProductService;
use app\shop\model\plus\agent\Product as AgentProductModel;
use app\shop\model\product\Category as CategoryModel;

/**
 * 商品運營控制器
 */
class Product extends Controller
{
    /**
     * 商品列表
     */
    public function index()
    {
        $model = new ProductModel;
        $list = $model->getList(array_merge(['status' => -1], $this->postData()));
        // 商品分類
        $category = CategoryModel::getCacheTree();
        return $this->renderSuccess('', compact('list', 'category'));
    }

    /**
     * 編輯分銷商
     */
    public function edit($product_id)
    {
        $model = new AgentProductModel();
        if ($this->request->isGet()) {
            // 平臺分銷規則
            $basicSetting = AgentSetting::getItem('basic');
            $agent_product = AgentProductModel::detail($product_id);
            return $this->renderSuccess('', compact('agent_product', 'basicSetting'));
        }
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 設定狀態
     */
    public function setAgent($productIds, $is_agent)
    {
        $model = new AgentProductModel();
        if ($model->setAgent($productIds, $is_agent)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }
}