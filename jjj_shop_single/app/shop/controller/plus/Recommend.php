<?php

namespace app\shop\controller\plus;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;

/**
 * Class Officia
 * 商品推薦控制器
 * @package app\shop\controller\plus\officia
 */
class Recommend extends Controller
{
    /**
     *商品推薦
     */
    public function index()
    {
        $key = 'recommend';
        if($this->request->isGet()){
            $vars['values'] = SettingModel::getItem($key);
            $product_arr = [];
            if (isset($vars['values']['product'])) {
                $product_arr = array_column($vars['values']['product'], 'product_id');
            }
            return $this->renderSuccess('', compact('vars','product_arr'));
        }
        $model = new SettingModel;
        if ($model->edit($key, $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError()?:'操作失敗');
    }
}