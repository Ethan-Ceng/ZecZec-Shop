<?php

namespace app\admin\controller;

use app\admin\model\settings\Region as RegionModel;

/**
 * 地區控制器
 */
class Region extends Controller
{
    /**
     * 物流資料
     */
    public function index()
    {
        $model = new RegionModel;
        $list = $model->getList($this->postData());
        $regionData = RegionModel::getCacheTree();
        return $this->renderSuccess('',compact('list', 'regionData'));
    }

    /**
     * 新增物流公司
     */
    public function add()
    {
        if($this->request->isGet()){
            // 獲取所有地區
            $regionData = RegionModel::getCacheTree();
            return $this->renderSuccess('', compact('regionData'));
        }
        // 新增記錄
        $model = new RegionModel;
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 修改
     * @param $express_id
     * @return \think\response\Json
     */
    public function edit($id)
    {
        $model = RegionModel::detail($id);
        if($this->request->isGet()){
            $regionData = RegionModel::getCacheTree();
            if($model['level'] == 1){
                $model['province_id'] = '';
                $model['city_id'] = '';
            }
            if($model['level'] == 2){
                $model['province_id'] = $model['pid'];
                $model['city_id'] = '';
            }
            if($model['level'] == 3){
                $model['province_id'] = RegionModel::detail($model['pid'])['pid'];
                $model['city_id'] = $model['pid'];
            }
            return $this->renderSuccess('', compact('model', 'regionData'));
        }
        $model = RegionModel::detail($id);
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 刪除記錄
     */
    public function delete($id)
    {
        $model = RegionModel::detail($id);
        if ($model->remove($id)) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?:'刪除失敗');
    }
}
