<?php

namespace app\shop\controller\store;

use app\common\model\settings\Region as RegionModel;
use app\shop\controller\Controller;
use app\shop\model\store\Store as StoreModel;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 門店控制器
 */
class Store extends Controller
{
    /**
     * 門店列表
     */
    public function index()
    {
        $model = new StoreModel;
        $list = $model->getList($this->postData());

        foreach ($list as $key => $val) {
            $list[$key]['detail_address'] = $val['region']['province'] . $val['region']['city'] . $val['region']['region'] . $val['address'];
        }

        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增門店
     */
    public function add()
    {
        if ($this->request->isGet()) {
            // 獲取所有地區
            $regionData = RegionModel::getCacheTree();
            $key = SettingModel::getItem('store')['tx_key'];
            return $this->renderSuccess('', compact('regionData', 'key'));
        }
        $model = new StoreModel;
        //Vue要新增的資料
        $data = $this->postData();
        if ($data['logo_image_id'] == 0) {
            return $this->renderError('請上傳logo');
        }
        $coordinate = explode(',', $data['coordinate']);
        if (count($coordinate) <= 1) {
            return $this->renderError('請在地圖點選選擇座標');
        }
        // 新增記錄
        if ($model->add($data)) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 修改門店資訊
     */
    public function edit($store_id)
    {
        // 門店詳情
        $model = StoreModel::detail($store_id);
        if ($this->request->isGet()) {
            $model['coordinate'] = $model['latitude'] . ',' . $model['longitude'];
            $regionData = RegionModel::getCacheTree();
            $key = SettingModel::getItem('store')['tx_key'];
            return $this->renderSuccess('', compact('model', 'regionData', 'key'));
        }
        // 修改記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 軟刪除
     */
    public function delete($store_id)
    {
        $model = new StoreModel;
        // 門店詳情
        if (!$model->setDelete(['store_id' => $store_id])) {
            return $this->renderError('刪除失敗');
        }
        return $this->renderSuccess($model->getError() ?: '刪除成功');

    }


}
