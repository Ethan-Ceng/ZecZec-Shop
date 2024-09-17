<?php

namespace app\shop\controller\store;

use app\shop\controller\Controller;
use app\shop\model\store\Store as StoreModel;
use app\shop\model\store\Order as OrderModel;

/**
 * 訂單核銷控制器
 */
class Order extends Controller
{
    /**
     * 訂單核銷記錄列表
     */
    public function index($store_id = 0, $search = '')
    {
        $data = $this->postData();
        // 核銷記錄列表
        $model = new OrderModel;
        $list = $model->getList($store_id, $search, $data);
        // 門店列表
        $store_list = (new StoreModel)->getList();
        return $this->renderSuccess('', compact('list', 'store_list'));
    }
}
