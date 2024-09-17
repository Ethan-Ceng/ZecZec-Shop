<?php

namespace app\api\controller\store;

use app\api\controller\Controller;
use app\api\model\store\Store as StoreModel;


/**
 * 門店列表
 */
class Store extends Controller
{
    /**
     * 門店列表
     */
    public function lists($longitude = '', $latitude = '')
    {
        $model = new StoreModel;
        $list = $model->getList(true, $longitude, $latitude);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 門店詳情
     */
    public function detail($store_id)
    {
        $detail = StoreModel::detail($store_id);
        return $this->renderSuccess('', compact('detail'));
    }

}