<?php

namespace app\shop\controller\plus\assemble;

use app\shop\controller\Controller;
use app\shop\model\plus\assemble\Active as ActiveModel;
use app\shop\model\plus\assemble\Product as AssembleProductModel;

/**
 * 拼團活動控制器
 */
class Active extends Controller
{
    /**
     * 活動會場列表
     */
    public function index()
    {
        $model = new ActiveModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增活動會場
     */
    public function add()
    {
        $model = new ActiveModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }


    /**
     * 獲取秒殺活動詳情
     * $seckill_activity_id int 秒殺活動id
     */
    public function edit($assemble_activity_id)
    {
        if($this->request->isGet()){
            $detail = ActiveModel::detailWithTrans($assemble_activity_id);
            //活動商品
            $product_list = AssembleProductModel::getList($assemble_activity_id);
            return $this->renderSuccess('', compact('detail', 'product_list'));
        }
        $model = ActiveModel::detail($assemble_activity_id);
        // 新增記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }
    /**
     * 刪除活動
     */
    public function delete($assemble_activity_id)
    {
        // 活動會場詳情
        $model = ActiveModel::detail($assemble_activity_id);
        if ($model->del()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?: '刪除失敗');
    }
}