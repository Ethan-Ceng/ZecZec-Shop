<?php

namespace app\shop\controller\plus\bargain;

use app\shop\controller\Controller;
use app\shop\model\plus\bargain\Product as BargainProductModel;
use app\shop\model\plus\bargain\Active as ActiveModel;

/**
 * 砍價控制器
 */
class Active extends Controller
{
    /**
     * 砍價活動列表
     */
    public function index()
    {
        $model = new ActiveModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增砍價活動
     */
    public function add()
    {
        $model = new ActiveModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?:'新增失敗');
    }

    /**
     * 獲取砍價活動詳情
     */
    public function edit($bargain_activity_id)
    {
        if($this->request->isGet()){
            $detail = ActiveModel::detailWithTrans($bargain_activity_id);
            //活動商品
            $product_list = BargainProductModel::getList($bargain_activity_id);
            return $this->renderSuccess('', compact('detail', 'product_list'));
        }
        $model = ActiveModel::detail($bargain_activity_id);
        // 新增記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     *刪除砍價活動
     */
    public function delete($bargain_activity_id)
    {
        // 活動會場詳情
        $model = ActiveModel::detail($bargain_activity_id);
        if ($model->del()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?: '刪除失敗');
    }
}